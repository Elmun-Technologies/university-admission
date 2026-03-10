<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "student_notification_pref".
 *
 * @property int $id
 * @property int $student_id
 * @property string $type
 * @property int|null $is_enabled
 * @property int|null $telegram_id
 * @property string|null $telegram_code
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Student $student
 */
class StudentNotificationPref extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%student_notification_pref}}';
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
            [['student_id', 'type'], 'required'],
            [['student_id', 'is_enabled', 'telegram_id', 'created_at', 'updated_at'], 'integer'],
            [['type'], 'string', 'max' => 20],
            [['telegram_code'], 'string', 'max' => 10],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::class, 'targetAttribute' => ['student_id' => 'id']],
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
            'type' => Yii::t('app', 'Turi / Тип'),
            'is_enabled' => Yii::t('app', 'Faol / Активен'),
            'telegram_id' => Yii::t('app', 'Telegram ID'),
            'telegram_code' => Yii::t('app', 'Telegram tekshiruv kodi'),
            'created_at' => Yii::t('app', 'Yaratilgan vaqt / Время создания'),
            'updated_at' => Yii::t('app', 'Yangilangan vaqt / Время обновления'),
        ];
    }

    /**
     * Gets query for [[Student]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudent()
    {
        return $this->hasOne(Student::class, ['id' => 'student_id']);
    }
}
