<?php

namespace frontend\controllers;

use common\models\Student;
use common\models\Exam;
use common\models\ExamDate;
use common\models\StudentExam;
use common\models\StudentExamAnswer;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;

/**
 * ExamController handles test taking, scheduling, and anti-cheat endpoints natively
 */
class ExamController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    protected function findStudentModel()
    {
        $student = Student::findOne(['created_by' => Yii::$app->user->id]);
        if (!$student) {
            throw new NotFoundHttpException('Profile not found.');
        }
        return $student;
    }

    /**
     * Lists available dates to book
     */
    public function actionSchedule()
    {
        $student = $this->findStudentModel();

        // Ensure they have reached this step
        if (!$student->canTakeExam()) {
            Yii::$app->session->setFlash('warning', Yii::t('app', 'Siz hozir imtihonga yoza olmaysiz.'));
            return $this->redirect(['/dashboard/index']);
        }

        // Find the exam mapped to their direction and edu_type
        $exam = Exam::findOne([
            'branch_id' => $student->branch_id,
            'direction_id' => $student->direction_id,
            'edu_type_id' => $student->edu_type_id,
            'status' => Exam::STATUS_ACTIVE
        ]);

        if (!$exam) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Ushbu yo\'nalish uchun imtihon hali belgilanmagan.'));
            return $this->redirect(['/dashboard/index']);
        }

        // Fetch Future Dates
        $availableDates = ExamDate::find()
            ->where(['exam_id' => $exam->id])
            ->andWhere(['>=', 'exam_date', date('Y-m-d')])
            ->andWhere(['status' => ExamDate::STATUS_SCHEDULED])
            ->all();

        return $this->render('schedule', [
            'student' => $student,
            'exam' => $exam,
            'dates' => $availableDates,
        ]);
    }

    /**
     * Confirms booking of a date
     */
    public function actionRegister($id)
    {
        $student = $this->findStudentModel();
        $date = ExamDate::findOne($id);

        if (!$date || !$date->isAvailableForRegistration()) {
            throw new NotFoundHttpException("Sana mavjud emas yoki joylar to'lgan.");
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Create Exam attempt shell
            $attempt = new StudentExam([
                'student_id' => $student->id,
                'exam_id' => $date->exam_id,
                'exam_date_id' => $date->id,
                'status' => StudentExam::STATUS_REGISTERED
            ]);
            $attempt->save(false);

            // Update Student Status natively
            $student->logStatusChange(Student::STATUS_EXAM_SCHEDULED, Yii::$app->user->id, "Imtihon sanasi tanlandi: " . $date->exam_date);
            $student->save(false);

            $transaction->commit();
            Yii::$app->session->setFlash('success', Yii::t('app', 'Imtihonga muvaffaqiyatli ro\'yxatdan o\'tdingiz!'));
            return $this->redirect(['/dashboard/index']);
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Tizim xatosi.');
            return $this->redirect(['schedule']);
        }
    }

    /**
     * Initiates the active test session strictly
     */
    public function actionStart($id)
    {
        // Enforce full page width layout for exam to obscure distraction
        $this->layout = 'main-login';

        $student = $this->findStudentModel();
        $attempt = StudentExam::findOne(['id' => $id, 'student_id' => $student->id]);

        if (!$attempt) {
            throw new NotFoundHttpException('Exam not found.');
        }

        // Verify window constraint
        $dateStr = $attempt->examDate->exam_date . ' ' . $attempt->examDate->start_time;
        if (strtotime($dateStr) > time()) {
            Yii::$app->session->setFlash('warning', Yii::t('app', 'Imtihon vaqti hali kelmadi.'));
            return $this->redirect(['/dashboard/index']);
        }

        if ($attempt->status == StudentExam::STATUS_FINISHED || $attempt->isExpired()) {
            return $this->redirect(['results', 'id' => $attempt->id]);
        }

        // GENERATE QUESTIONS IF FIRST TIME OPENING
        if ($attempt->status == StudentExam::STATUS_REGISTERED && !$attempt->started_at) {
            $attempt->started_at = time();
            $attempt->status = StudentExam::STATUS_IN_PROGRESS;
            $attempt->save(false);

            // Map core generation native method
            $attempt->generateQuestions($attempt->exam);
        }

        $answers = StudentExamAnswer::find()
            ->with(['question', 'question.questionOptions'])
            ->where(['student_exam_id' => $attempt->id])
            ->all();

        return $this->render('start', [
            'attempt' => $attempt,
            'answers' => $answers,
        ]);
    }

    /**
     * Forcefully processes end of exam internally
     */
    public function actionFinish($id)
    {
        $student = $this->findStudentModel();
        $attempt = StudentExam::findOne(['id' => $id, 'student_id' => $student->id]);

        if ($attempt && $attempt->status == StudentExam::STATUS_IN_PROGRESS) {
            // Calculate final score purely based on DB values previously synced via API
            $correctCount = StudentExamAnswer::find()
                ->where(['student_exam_id' => $attempt->id, 'is_correct' => 1])
                ->count();

            $totalCount = StudentExamAnswer::find()
                ->where(['student_exam_id' => $attempt->id])
                ->count();

            $attempt->score = $correctCount;
            $attempt->max_score = $totalCount;
            $attempt->finished_at = time();
            $attempt->status = StudentExam::STATUS_FINISHED;

            // Check Pass/Fail logic natively
            $passingScore = $attempt->exam->passing_score;
            $percent = $attempt->getScorePercent();
            $attempt->is_passed = ($percent >= $passingScore) ? 1 : 0;

            $attempt->save(false);

            // Update parent student state dynamically
            $newState = $attempt->is_passed ? Student::STATUS_EXAM_PASSED : Student::STATUS_EXAM_FAILED;
            $student->logStatusChange($newState, Yii::$app->user->id, "Imtihon yakunlandi. Natija: {$percent}%");
            $student->save(false);
        }

        return $this->redirect(['results', 'id' => $id]);
    }

    /**
     * Shows breakdown after completion
     */
    public function actionResults($id)
    {
        $student = $this->findStudentModel();
        $attempt = StudentExam::findOne(['id' => $id, 'student_id' => $student->id]);

        if (!$attempt || $attempt->status != StudentExam::STATUS_FINISHED) {
            return $this->redirect(['/dashboard/index']);
        }

        return $this->render('results', [
            'attempt' => $attempt,
        ]);
    }
}
