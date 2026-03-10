<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "student_oferta".
 *
 * @property int $id
 * @property int $student_id
 * @property string $contract_number
 * @property int|null $signed_at
 * @property float|null $payment_amount
 * @property int $payment_status
 * @property string|null $payment_date
 * @property string|null $contract_file
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Student $student
 */
class StudentOferta extends ActiveRecord
{
    const PAYMENT_UNPAID = 0;
    const PAYMENT_PARTIAL = 1;
    const PAYMENT_PAID = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%student_oferta}}';
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
            [['student_id', 'contract_number'], 'required'],
            [['student_id', 'signed_at', 'payment_status', 'created_at', 'updated_at'], 'integer'],
            [['payment_amount'], 'number'],
            [['payment_date'], 'safe'],
            [['contract_number'], 'string', 'max' => 50],
            [['contract_file'], 'string', 'max' => 255],
            [['contract_number'], 'unique'],
            [['student_id'], 'unique'],
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
            'contract_number' => Yii::t('app', 'Shartnoma raqami / Номер договора'),
            'signed_at' => Yii::t('app', 'Imzolangan vaqt / Время подписания'),
            'payment_amount' => Yii::t('app', 'To\'lov summasi / Сумма оплаты'),
            'payment_status' => Yii::t('app', 'To\'lov holati / Статус оплаты'),
            'payment_date' => Yii::t('app', 'To\'lov muddati / Срок оплаты'),
            'contract_file' => Yii::t('app', 'Shartnoma fayli / Файл договора'),
            'created_at' => Yii::t('app', 'Yaratilgan vaqt / Время создания'),
            'updated_at' => Yii::t('app', 'Yangilangan vaqt / Время обновления'),
        ];
    }

    /**
     * Gets query for [[Student]] with eager loading to prevent N+1 later
     */
    public function getStudent()
    {
        return $this->hasOne(Student::class, ['id' => 'student_id'])
            ->with(['direction', 'eduForm']);
    }

    /**
     * Generates a unique sequential contract number globally
     * e.g BRN-2026-000123
     */
    public static function generateContractNumber($branchId)
    {
        $year = date('Y');
        $prefix = "BRN-{$year}-";

        // Find last contract inserted this year
        $lastOferta = self::find()
            ->where(['like', 'contract_number', $prefix . '%', false])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        if ($lastOferta) {
            $parts = explode('-', $lastOferta->contract_number);
            $lastNumber = (int) end($parts);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Prepare specific flat data representation for an external PDF generator tool
     */
    public function generateContractPdf()
    {
        $student = $this->student;
        return [
            'contract_number' => $this->contract_number,
            'amount' => $this->payment_amount,
            'full_name' => $student->getFullName(),
            'passport' => $student->passport_series . $student->passport_number,
            'pinfl' => $student->pinfl,
            'direction' => $student->direction->name_uz ?? '',
            'branch' => $student->branch->name_uz ?? '',
            'created_at' => date('d.m.Y', $this->created_at),
        ];
    }

    /**
     * Outputs a color coded badge for admin gridviews natively
     */
    public function getPaymentStatusLabel()
    {
        $statuses = [
            self::PAYMENT_UNPAID => ['label' => Yii::t('app', 'To\'lanmagan / Неоплачено'), 'class' => 'danger'],
            self::PAYMENT_PARTIAL => ['label' => Yii::t('app', 'Qisman to\'langan / Частично оплачено'), 'class' => 'warning'],
            self::PAYMENT_PAID => ['label' => Yii::t('app', 'To\'langan / Оплачено'), 'class' => 'success'],
        ];

        $status = $statuses[$this->payment_status] ?? $statuses[self::PAYMENT_UNPAID];
        return '<span class="badge bg-' . $status['class'] . '">' . $status['label'] . '</span>';
    }

    /**
     * Virtual attribute: Helper to instantly calculate how many days applicant has left
     */
    public function getDaysUntilDeadline()
    {
        if (!$this->payment_date) {
            return null;
        }

        $deadline = strtotime($this->payment_date);
        $diff = $deadline - time();

        return round($diff / (60 * 60 * 24));
    }
}
