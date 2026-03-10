<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "consulting".
 *
 * @property int $id
 * @property string $name
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $contact_person
 * @property float $commission_percent
 * @property int $status
 * @property int $created_at
 *
 * @property ConsultingBranch[] $consultingBranches
 * @property ConsultingStudent[] $consultingStudents
 * @property Student[] $students
 */
class Consulting extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%consulting}}';
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
            [['name'], 'required'],
            [['commission_percent'], 'number'],
            [['status', 'created_at'], 'integer'],
            [['name', 'contact_person'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 20],
            [['email'], 'string', 'max' => 150],
            [['email'], 'email'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Kompaniya nomi / Название компании'),
            'phone' => Yii::t('app', 'Telefon / Телефон'),
            'email' => Yii::t('app', 'Email'),
            'contact_person' => Yii::t('app', 'Mas\'ul shaxs / Контактное лицо'),
            'commission_percent' => Yii::t('app', 'Komissiya fozii / Процент комиссии (%)'),
            'status' => Yii::t('app', 'Holat / Статус'),
            'created_at' => Yii::t('app', 'Qo\'shilgan vaqt / Время добавления'),
        ];
    }

    /**
     * Gets query for [[ConsultingBranches]].
     */
    public function getConsultingBranches()
    {
        return $this->hasMany(ConsultingBranch::class, ['consulting_id' => 'id']);
    }

    /**
     * Gets query for [[ConsultingStudents]].
     */
    public function getConsultingStudents()
    {
        return $this->hasMany(ConsultingStudent::class, ['consulting_id' => 'id']);
    }

    /**
     * Gets query for [[Students]] through pivot.
     */
    public function getStudents()
    {
        return $this->hasMany(Student::class, ['id' => 'student_id'])
            ->via('consultingStudents');
    }

    /**
     * Total students from this agency
     */
    public function getStudentCount()
    {
        return $this->getConsultingStudents()->count();
    }

    /**
     * Students who paid contract
     */
    public function getPaidStudentCount()
    {
        return Student::find()
            ->innerJoinWith('consultingStudent cs')
            ->where(['cs.consulting_id' => $this->id])
            ->andWhere(['status' => Student::STATUS_PAID])
            ->count();
    }

    /**
     * Total commission earned mapped heavily by the pivot table tracking commission
     */
    public function getCommissionTotal()
    {
        $total = ConsultingStudent::find()
            ->where(['consulting_id' => $this->id, 'commission_paid' => 1])
            ->sum('commission_amount');

        return number_format((float) $total, 2, '.', ',');
    }

    /**
     * For dropdowns
     */
    public static function getActiveList()
    {
        return ArrayHelper::map(self::find()->where(['status' => self::STATUS_ACTIVE])->all(), 'id', 'name');
    }
}
