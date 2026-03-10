<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "student_exam".
 *
 * @property int $id
 * @property int $student_id
 * @property int $exam_id
 * @property int $exam_date_id
 * @property int|null $started_at
 * @property int|null $finished_at
 * @property int|null $score
 * @property int|null $max_score
 * @property int|null $is_passed
 * @property int $status
 * @property int $created_at
 *
 * @property Student $student
 * @property Exam $exam
 * @property ExamDate $examDate
 * @property StudentExamAnswer[] $studentExamAnswers
 */
class StudentExam extends ActiveRecord
{
    const STATUS_REGISTERED = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_FINISHED = 2;
    const STATUS_EXPIRED = 3;
    const STATUS_CHEATED = 4;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%student_exam}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['student_id', 'exam_id', 'exam_date_id'], 'required'],
            [['student_id', 'exam_id', 'exam_date_id', 'started_at', 'finished_at', 'score', 'max_score', 'is_passed', 'status', 'created_at'], 'integer'],
            ['status', 'default', 'value' => self::STATUS_REGISTERED],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::class, 'targetAttribute' => ['student_id' => 'id']],
            [['exam_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exam::class, 'targetAttribute' => ['exam_id' => 'id']],
            [['exam_date_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamDate::class, 'targetAttribute' => ['exam_date_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'student_id' => Yii::t('app', 'Talaba / Студент'),
            'exam_id' => Yii::t('app', 'Imtihon / Экзамен'),
            'exam_date_id' => Yii::t('app', 'Imtihon sanasi / Дата экзамена'),
            'started_at' => Yii::t('app', 'Boshlangan vaqt / Время начала'),
            'finished_at' => Yii::t('app', 'Tugallangan vaqt / Время завершения'),
            'score' => Yii::t('app', 'To\'plangan bal / Набранный балл'),
            'max_score' => Yii::t('app', 'Maksimal bal / Макс. балл'),
            'is_passed' => Yii::t('app', 'O\'tdimi? / Прошел?'),
            'status' => Yii::t('app', 'Holat / Статус'),
        ];
    }

    /**
     * Gets query for [[Student]].
     */
    public function getStudent()
    {
        return $this->hasOne(Student::class, ['id' => 'student_id']);
    }

    /**
     * Gets query for [[Exam]].
     */
    public function getExam()
    {
        return $this->hasOne(Exam::class, ['id' => 'exam_id']);
    }

    /**
     * Gets query for [[ExamDate]].
     */
    public function getExamDate()
    {
        return $this->hasOne(ExamDate::class, ['id' => 'exam_date_id']);
    }

    /**
     * Gets query for [[StudentExamAnswers]].
     */
    public function getStudentExamAnswers()
    {
        return $this->hasMany(StudentExamAnswer::class, ['student_exam_id' => 'id']);
    }

    /**
     * Returns score as percentage
     */
    public function getScorePercent()
    {
        if (!$this->max_score || !$this->score) {
            return 0;
        }
        return round(($this->score / $this->max_score) * 100, 2);
    }

    /**
     * Returns exam duration in minutes
     */
    public function getDuration()
    {
        return $this->exam ? $this->exam->duration_minutes : 0;
    }

    /**
     * Check if exam time is over based on started_at and duration
     */
    public function isExpired()
    {
        if (!$this->started_at) {
            return false;
        }
        $endTime = $this->started_at + ($this->getDuration() * 60);
        return time() > $endTime;
    }

    /**
     * Generates a random set of questions specifically for this exam session
     */
    public function generateQuestions(Exam $exam)
    {
        $examSubjects = $exam->examSubjects;

        $answersData = [];
        foreach ($examSubjects as $examSubject) {
            // Select random questions
            $questions = Question::getForExam($examSubject->subject_id, $examSubject->questions_count);

            foreach ($questions as $question) {
                $answersData[] = [
                    $this->id,
                    $question->id,
                    null, // selected_option_id
                    null, // is_correct
                    null, // answered_at
                ];
            }
        }

        if (!empty($answersData)) {
            // Batch insert for performance
            Yii::$app->db->createCommand()->batchInsert(
                StudentExamAnswer::tableName(),
                ['student_exam_id', 'question_id', 'selected_option_id', 'is_correct', 'answered_at'],
                $answersData
            )->execute();

            return true;
        }

        return false;
    }
}
