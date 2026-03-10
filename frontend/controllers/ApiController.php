<?php

namespace frontend\controllers;

use common\models\Student;
use common\models\StudentExamAnswer;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

/**
 * ApiController handles native JSON endpoints requested by Vue/Vanilla DOM components
 */
class ApiController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // Restrict rigidly to Auth sessions
                    ],
                ],
            ],
        ];
    }

    /**
     * Set standard output to JSON explicitly
     */
    public function beforeAction($action)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        // Ignore CSRF for simple forms logic fetch if needed, but we keep it tight for answers
        if ($action->id !== 'answer') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    /**
     * Fetch cascaded elements logic natively (Returns JSON forms/types)
     */
    public function actionDirectionForms($directionId)
    {
        $direction = \common\models\Direction::findOne($directionId);

        if (!$direction) {
            return ['error' => 'Direction not found'];
        }

        $forms = [];
        foreach ($direction->eduForms as $f) {
            $forms[] = ['id' => $f->id, 'name' => $f->name_uz];
        }

        $types = [];
        foreach ($direction->eduTypes as $t) {
            $types[] = ['id' => $t->id, 'name' => $t->name_uz];
        }

        return [
            'eduForms' => $forms,
            'eduTypes' => $types,
        ];
    }

    /**
     * Handles background heartbeat syncing of Answers from exam.js
     */
    public function actionAnswer()
    {
        if (!Yii::$app->request->isPost) {
            return ['error' => 'Method not allowed'];
        }

        $raw = Yii::$app->request->getRawBody();
        $data = json_decode($raw, true);

        if (!$data || !isset($data['studentExamId'], $data['questionId'], $data['optionId'])) {
            return ['error' => 'Invalid payload structure'];
        }

        // Must verify ownership to prevent remote ID tampering natively
        $student = Student::findOne(['created_by' => Yii::$app->user->id]);

        $answer = StudentExamAnswer::find()
            ->innerJoinWith('studentExam')
            ->where([
                'student_exam_answer.student_exam_id' => $data['studentExamId'],
                'student_exam_answer.question_id' => $data['questionId'],
                'student_exam.student_id' => $student->id // Rigid constraint
            ])
            ->one();

        if ($answer) {
            $answer->selected_option_id = $data['optionId'];
            $answer->answered_at = time();

            // Check if it's explicitly correct instantly internally (though calculated end of exam)
            $isCorrectOption = \common\models\QuestionOption::find()
                ->where(['id' => $data['optionId'], 'is_correct' => 1])
                ->exists();

            $answer->is_correct = $isCorrectOption ? 1 : 0;

            if ($answer->save(false)) {
                return ['success' => true];
            }
            return ['error' => 'Failed to save physically in DB', 'details' => $answer->errors];
        }

        return ['error' => 'Answer boundary missing or not authorized'];
    }

    /**
     * Polling endpoint used specifically on Dashboard for background status updates
     */
    public function actionStatus()
    {
        $student = Student::findOne(['created_by' => Yii::$app->user->id]);

        if (!$student) {
            return ['error' => 'Data missing'];
        }

        return [
            'status' => (int) $student->status,
            'label_uz' => $student->getStatusLabel(false) // Assuming boolean false means pure text vs HTML badge
        ];
    }
}
