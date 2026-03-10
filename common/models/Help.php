<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "help".
 *
 * @property int $id
 * @property string $category_uz
 * @property string $category_ru
 * @property string $question_uz
 * @property string $question_ru
 * @property string $answer_uz
 * @property string $answer_ru
 * @property int|null $sort_order
 * @property int|null $is_active
 * @property int $created_at
 * @property int $updated_at
 */
class Help extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%help}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_uz', 'category_ru', 'question_uz', 'question_ru', 'answer_uz', 'answer_ru'], 'required'],
            [['question_uz', 'question_ru', 'answer_uz', 'answer_ru'], 'string'],
            [['sort_order', 'is_active', 'created_at', 'updated_at'], 'integer'],
            [['category_uz', 'category_ru'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'category_uz' => Yii::t('app', 'Kategoriya (O\'z)'),
            'category_ru' => Yii::t('app', 'Категория (Ру)'),
            'question_uz' => Yii::t('app', 'Savol (O\'z)'),
            'question_ru' => Yii::t('app', 'Вопрос (Ру)'),
            'answer_uz' => Yii::t('app', 'Javob (O\'z)'),
            'answer_ru' => Yii::t('app', 'Ответ (Ру)'),
            'sort_order' => Yii::t('app', 'Tartib raqami / Порядок сортировки'),
            'is_active' => Yii::t('app', 'Faol / Активен'),
            'created_at' => Yii::t('app', 'Yaratilgan vaqt / Время создания'),
            'updated_at' => Yii::t('app', 'Yangilangan vaqt / Время обновления'),
        ];
    }

    public function getCategory()
    {
        return Yii::$app->language === 'ru' ? $this->category_ru : $this->category_uz;
    }

    public function getQuestion()
    {
        return Yii::$app->language === 'ru' ? $this->question_ru : $this->question_uz;
    }

    public function getAnswer()
    {
        return Yii::$app->language === 'ru' ? $this->answer_ru : $this->answer_uz;
    }
}
