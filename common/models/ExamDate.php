<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "exam_date".
 *
 * @property int $id
 * @property int $exam_id
 * @property string $exam_date
 * @property string $start_time
 * @property string $end_time
 * @property int $max_participants
 * @property int $status
 * @property int $created_at
 *
 * @property Exam $exam
 */
class ExamDate extends ActiveRecord
{
    const STATUS_CANCELLED = 0;
    const STATUS_SCHEDULED = 1;
    const STATUS_ONGOING = 2;
    const STATUS_FINISHED = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%exam_date}}';
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
            [['exam_id', 'exam_date', 'start_time', 'end_time', 'max_participants'], 'required'],
            [['exam_id', 'max_participants', 'status', 'created_at'], 'integer'],
            [['exam_date', 'start_time', 'end_time'], 'safe'],
            ['status', 'default', 'value' => self::STATUS_SCHEDULED],
            [['exam_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exam::class, 'targetAttribute' => ['exam_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'exam_id' => Yii::t('app', 'Imtihon / Экзамен'),
            'exam_date' => Yii::t('app', 'Sana / Дата'),
            'start_time' => Yii::t('app', 'Boshlanish vaqti / Время начала'),
            'end_time' => Yii::t('app', 'Tugash vaqti / Время окончания'),
            'max_participants' => Yii::t('app', 'Maksimal ishtirokchilar / Макс. участников'),
            'status' => Yii::t('app', 'Holat / Статус'),
        ];
    }

    /**
     * Gets query for [[Exam]].
     */
    public function getExam()
    {
        return $this->hasOne(Exam::class, ['id' => 'exam_id']);
    }

    /**
     * Count remaining available slots for this specific date
     */
    public function getSlotsAvailable()
    {
        $registeredCount = StudentExam::find()
            ->where(['exam_date_id' => $this->id])
            ->count();

        return max(0, $this->max_participants - $registeredCount);
    }

    /**
     * Checks if a student can register for this date
     */
    public function isAvailableForRegistration()
    {
        $dateTimeStr = $this->exam_date . ' ' . $this->start_time;
        if (strtotime($dateTimeStr) < time()) {
            return false; // already passed
        }

        if ($this->status !== self::STATUS_SCHEDULED) {
            return false;
        }

        return $this->getSlotsAvailable() > 0;
    }
}
