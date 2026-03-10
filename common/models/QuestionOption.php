<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "question_options".
 *
 * @property int $id
 * @property int $question_id
 * @property string $option_text
 * @property string|null $option_text_ru
 * @property int $is_correct
 *
 * @property Question $question
 */
class QuestionOption extends ActiveRecord
{
    const IS_NOT_CORRECT = 0;
    const IS_CORRECT = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%question_options}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['question_id', 'option_text'], 'required'],
            [['question_id', 'is_correct'], 'integer'],
            [['option_text', 'option_text_ru'], 'string'],
            ['is_correct', 'default', 'value' => self::IS_NOT_CORRECT],
            ['is_correct', 'in', 'range' => [self::IS_NOT_CORRECT, self::IS_CORRECT]],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => Question::class, 'targetAttribute' => ['question_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'question_id' => Yii::t('app', 'Savol / Вопрос / Question'),
            'option_text' => Yii::t('app', 'Variant matni (O\'z) / Текст варианта (Уз)'),
            'option_text_ru' => Yii::t('app', 'Variant matni (Ru) / Текст варианта (Ру)'),
            'is_correct' => Yii::t('app', 'To\'g\'rimi? / Правильный? / Is Correct?'),
        ];
    }

    /**
     * Gets query for [[Question]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(Question::class, ['id' => 'question_id']);
    }
}
