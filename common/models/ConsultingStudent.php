<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "consulting_student".
 *
 * @property int $id
 * @property int $consulting_id
 * @property int|null $consulting_branch_id
 * @property int $student_id
 * @property int $registered_at
 * @property float|null $commission_amount
 * @property int $commission_paid
 *
 * @property Consulting $consulting
 * @property ConsultingBranch $consultingBranch
 * @property Student $student
 */
class ConsultingStudent extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%consulting_student}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['consulting_id', 'student_id', 'registered_at'], 'required'],
            [['consulting_id', 'consulting_branch_id', 'student_id', 'registered_at', 'commission_paid'], 'integer'],
            [['commission_amount'], 'number'],
            [['student_id'], 'unique'],
            [['consulting_branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => ConsultingBranch::class, 'targetAttribute' => ['consulting_branch_id' => 'id']],
            [['consulting_id'], 'exist', 'skipOnError' => true, 'targetClass' => Consulting::class, 'targetAttribute' => ['consulting_id' => 'id']],
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
            'consulting_id' => Yii::t('app', 'Konsalting / Консалтинг'),
            'consulting_branch_id' => Yii::t('app', 'Konsalting filiali / Филиал консалтинга'),
            'student_id' => Yii::t('app', 'Talaba / Студент'),
            'registered_at' => Yii::t('app', 'Qo\'shilgan vaqt / Время регистрации'),
            'commission_amount' => Yii::t('app', 'Komissiya summasi / Сумма комиссии'),
            'commission_paid' => Yii::t('app', 'To\'landimi? / Выплачено?'),
        ];
    }

    /**
     * Gets query for [[Consulting]].
     */
    public function getConsulting()
    {
        return $this->hasOne(Consulting::class, ['id' => 'consulting_id']);
    }

    /**
     * Gets query for [[ConsultingBranch]].
     */
    public function getConsultingBranch()
    {
        return $this->hasOne(ConsultingBranch::class, ['id' => 'consulting_branch_id']);
    }

    /**
     * Gets query for [[Student]].
     */
    public function getStudent()
    {
        return $this->hasOne(Student::class, ['id' => 'student_id']);
    }

    /**
     * Calculates and saves commission based on student contract payment
     */
    public function calculateCommission()
    {
        $oferta = $this->student->studentOferta;
        if ($oferta && $oferta->payment_amount > 0) {
            $percent = $this->consulting->commission_percent;
            $this->commission_amount = ($oferta->payment_amount * $percent) / 100;
            return $this->save(false, ['commission_amount']);
        }
        return false;
    }
}
