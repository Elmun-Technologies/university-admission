<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "student_exam_answer".
 *
 * @property int $id
 * @property int $student_exam_id
 * @property int $question_id
 * @property int|null $selected_option_id
 * @property int|null $is_correct
 * @property int|null $answered_at
 *
 * @property StudentExam $studentExam
 * @property Question $question
 * @property QuestionOption $selectedOption
 */
class StudentExamAnswer extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%student_exam_answer}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['student_exam_id', 'question_id'], 'required'],
            [['student_exam_id', 'question_id', 'selected_option_id', 'is_correct', 'answered_at'], 'integer'],
            [['student_exam_id', 'question_id'], 'unique', 'targetAttribute' => ['student_exam_id', 'question_id']],
            [['student_exam_id'], 'exist', 'skipOnError' => true, 'targetClass' => StudentExam::class, 'targetAttribute' => ['student_exam_id' => 'id']],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => Question::class, 'targetAttribute' => ['question_id' => 'id']],
            [['selected_option_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuestionOption::class, 'targetAttribute' => ['selected_option_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'student_exam_id' => Yii::t('app', 'Talaba imtihoni / Экзамен студента'),
            'question_id' => Yii::t('app', 'Savol / Вопрос'),
            'selected_option_id' => Yii::t('app', 'Tanlangan javob / Выбранный вариант'),
            'is_correct' => Yii::t('app', 'To\'g\'rimi? / Правильно?'),
            'answered_at' => Yii::t('app', 'Javob berilgan vaqt / Время ответа'),
        ];
    }

    /**
     * Gets query for [[StudentExam]].
     */
    public function getStudentExam()
    {
        return $this->hasOne(StudentExam::class, ['id' => 'student_exam_id']);
    }

    /**
     * Gets query for [[Question]].
     */
    public function getQuestion()
    {
        return $this->hasOne(Question::class, ['id' => 'question_id']);
    }

    /**
     * Gets query for [[SelectedOption]].
     */
    public function getSelectedOption()
    {
        return $this->hasOne(QuestionOption::class, ['id' => 'selected_option_id']);
    }
}
