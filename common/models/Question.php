<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "questions".
 *
 * @property int $id
 * @property int $subject_id
 * @property string $question_text
 * @property string|null $question_text_ru
 * @property int $difficulty
 * @property int $status
 * @property int $created_at
 *
 * @property QuestionOption[] $questionOptions
 * @property Subject $subject
 */
class Question extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const DIFFICULTY_EASY = 1;
    const DIFFICULTY_MEDIUM = 2;
    const DIFFICULTY_HARD = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%questions}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false, // Table only has created_at
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subject_id', 'question_text'], 'required'],
            [['subject_id', 'difficulty', 'status', 'created_at'], 'integer'],
            [['question_text', 'question_text_ru'], 'string'],
            ['difficulty', 'default', 'value' => self::DIFFICULTY_EASY],
            ['difficulty', 'in', 'range' => [self::DIFFICULTY_EASY, self::DIFFICULTY_MEDIUM, self::DIFFICULTY_HARD]],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::class, 'targetAttribute' => ['subject_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'subject_id' => Yii::t('app', 'Fan / Предмет / Subject'),
            'question_text' => Yii::t('app', 'Savol matni (O\'z) / Текст вопроса (Уз)'),
            'question_text_ru' => Yii::t('app', 'Savol matni (Ru) / Текст вопроса (Ру)'),
            'difficulty' => Yii::t('app', 'Qiyinlik darajasi / Сложность / Difficulty'),
            'status' => Yii::t('app', 'Holat / Статус / Status'),
            'created_at' => Yii::t('app', 'Yaratilgan vaqti / Время создания'),
        ];
    }

    /**
     * Gets query for [[QuestionOptions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionOptions()
    {
        return $this->hasMany(QuestionOption::class, ['question_id' => 'id']);
    }

    /**
     * Gets query for [[Subject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::class, ['id' => 'subject_id']);
    }

    /**
     * Get the correct option for this question
     *
     * @return QuestionOption|null
     */
    public function getCorrectOption()
    {
        return $this->getQuestionOptions()
            ->andWhere(['is_correct' => QuestionOption::IS_CORRECT])
            ->one();
    }

    /**
     * Get options in random order
     *
     * @return QuestionOption[]
     */
    public function getShuffledOptions()
    {
        $options = $this->questionOptions;
        shuffle($options); // randomize the array natively
        return $options;
    }

    /**
     * Get $count number of random questions for an exam for a specific subject
     *
     * @param int $subjectId
     * @param int $count Number of questions to return
     * @param int|null $difficulty Optional difficulty filter
     * @return Question[]
     */
    public static function getForExam($subjectId, $count, $difficulty = null)
    {
        $query = self::find()
            ->where([
                'subject_id' => $subjectId,
                'status' => self::STATUS_ACTIVE,
            ])
            ->with('questionOptions');

        if ($difficulty !== null) {
            $query->andWhere(['difficulty' => $difficulty]);
        }

        // Use MySQL RAND() for random selection, fallback robust enough for exams
        return $query->orderBy(new \yii\db\Expression('RAND()'))
            ->limit($count)
            ->all();
    }
}
