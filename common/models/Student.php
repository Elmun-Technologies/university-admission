<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "student".
 *
 * @property int $id
 * @property int $branch_id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $middle_name
 * @property string|null $first_name_ru
 * @property string|null $last_name_ru
 * @property string|null $middle_name_ru
 * @property string|null $birth_date
 * @property int|null $gender
 * @property string $phone
 * @property string|null $phone2
 * @property string|null $email
 * @property string|null $passport_series
 * @property string|null $passport_number
 * @property string|null $passport_given_by
 * @property string|null $passport_given_date
 * @property string|null $pinfl
 * @property int|null $region_id
 * @property int|null $district_id
 * @property string|null $address
 * @property int|null $direction_id
 * @property int|null $edu_form_id
 * @property int|null $edu_type_id
 * @property int|null $course_id
 * @property int|null $consulting_id
 * @property int $status
 * @property string|null $photo
 * @property array|null $status_history
 * @property int|null $created_by
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Branch $branch
 * @property Direction $direction
 * @property EduForm $eduForm
 * @property EduType $eduType
 * @property Course $course
 * @property Consulting $consulting
 * @property StudentOferta $studentOferta
 * @property StudentExam[] $studentExams
 * @property User $createdBy
 */
class Student extends \common\db\BranchActiveRecord
{
    public const STATUS_NEW = 0;
    public const STATUS_ANKETA = 1;
    public const STATUS_EXAM_SCHEDULED = 2;
    public const STATUS_EXAM_PASSED = 3;
    public const STATUS_EXAM_FAILED = 5;
    public const STATUS_CONTRACT_SIGNED = 6;
    public const STATUS_PAID = 7;
    public const STATUS_REJECTED = 9;

    /**
     * Strict State Machine mapping allowed transitions
     */
    public const STATUS_TRANSITIONS = [
        self::STATUS_NEW => [self::STATUS_ANKETA, self::STATUS_REJECTED],
        self::STATUS_ANKETA => [self::STATUS_EXAM_SCHEDULED, self::STATUS_REJECTED],
        self::STATUS_EXAM_SCHEDULED => [self::STATUS_EXAM_PASSED, self::STATUS_EXAM_FAILED, self::STATUS_REJECTED],
        self::STATUS_EXAM_PASSED => [self::STATUS_CONTRACT_SIGNED, self::STATUS_REJECTED],
        self::STATUS_EXAM_FAILED => [self::STATUS_REJECTED],
        self::STATUS_CONTRACT_SIGNED => [self::STATUS_PAID, self::STATUS_REJECTED],
        self::STATUS_PAID => [], // Terminal state
        self::STATUS_REJECTED => [], // Terminal state
    ];

    public const GENDER_MALE = 1;
    public const GENDER_FEMALE = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%student}}';
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
            [['branch_id', 'first_name', 'last_name', 'phone'], 'required'],
            [['branch_id', 'gender', 'region_id', 'district_id', 'direction_id', 'edu_form_id',
                'edu_type_id', 'course_id', 'consulting_id', 'status', 'created_by', 'created_at',
                'updated_at'], 'integer'],
            [['birth_date', 'passport_given_date', 'status_history'], 'safe'],
            [['address'], 'string'],
            [['first_name', 'last_name', 'middle_name', 'first_name_ru', 'last_name_ru', 'middle_name_ru'],
                'string', 'max' => 100],
            [['phone', 'phone2', 'pinfl'], 'string', 'max' => 20],
            [['email'], 'string', 'max' => 150],
            [['email'], 'email'],
            [['passport_series'], 'string', 'max' => 10],
            [['passport_number'], 'string', 'max' => 15],
            [['passport_given_by', 'photo'], 'string', 'max' => 255],

            // Photo validation with MIME type check
            [['photo'], 'file', 'extensions' => 'png, jpg, jpeg', 'mimeTypes' => 'image/jpeg, image/png',
                'maxSize' => 1024 * 1024 * 5, 'tooBig' => 'Rasm hajmi 5MB dan oshmasligi kerak'],

            // Generic string trims and XSS prevention via Filter (handled in beforeSave)
            [['first_name', 'last_name', 'middle_name', 'address'], 'filter',
                'filter' => '\common\components\ContentFilter::cleanText'],

            // Unique constraints
            [['phone'], 'unique'],
            [['pinfl'], 'unique', 'skipOnEmpty' => true],
            [['passport_series', 'passport_number'], 'unique',
                'targetAttribute' => ['passport_series', 'passport_number'],
                'skipOnEmpty' => true,
                'message' => 'Ushbu pasport seriya va raqamli abituriyent ro\'yxatdan o\'tgan.'],

            // Phone regex pattern +998XXXXXXXXX
            [['phone', 'phone2'], 'match', 'pattern' => '/^\+998\d{9}$/',
                'message' => 'Telefon raqam formati +998XXXXXXXXX bo\'lishi kerak.'],

            // Status defaults
            ['status', 'default', 'value' => self::STATUS_NEW],
            ['status', 'in', 'range' => array_keys(self::getStatusList())],

            // Foreign key checks
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::class,
                'targetAttribute' => ['branch_id' => 'id']],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::class,
                'targetAttribute' => ['direction_id' => 'id']],
            [['edu_form_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduForm::class,
                'targetAttribute' => ['edu_form_id' => 'id']],
            [['edu_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduType::class,
                'targetAttribute' => ['edu_type_id' => 'id']],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::class,
                'targetAttribute' => ['course_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'branch_id' => Yii::t('app', 'Filial / Филиал'),
            'first_name' => Yii::t('app', 'Ism / Имя'),
            'last_name' => Yii::t('app', 'Familiya / Фамилия'),
            'middle_name' => Yii::t('app', 'Otasining ismi / Отчество'),
            'first_name_ru' => Yii::t('app', 'Ism (Ru) / Имя (Ру)'),
            'last_name_ru' => Yii::t('app', 'Familiya (Ru) / Фамилия (Ру)'),
            'middle_name_ru' => Yii::t('app', 'Otasining ismi (Ru) / Отчество (Ру)'),
            'birth_date' => Yii::t('app', 'Tug\'ilgan sana / Дата рождения'),
            'gender' => Yii::t('app', 'Jins / Пол'),
            'phone' => Yii::t('app', 'Telefon / Телефон'),
            'phone2' => Yii::t('app', 'Qo\'shimcha telefon / Доп. телефон'),
            'email' => Yii::t('app', 'Email'),
            'passport_series' => Yii::t('app', 'Pasport seriyasi / Серия паспорта'),
            'passport_number' => Yii::t('app', 'Pasport raqami / Номер паспорта'),
            'passport_given_by' => Yii::t('app', 'Kim tomonidan berilgan / Кем выдан'),
            'passport_given_date' => Yii::t('app', 'Berilgan sana / Дата выдачи'),
            'pinfl' => Yii::t('app', 'JSHSHIR (ПИНФЛ)'),
            'region_id' => Yii::t('app', 'Viloyat / Область'),
            'district_id' => Yii::t('app', 'Tuman / Район'),
            'address' => Yii::t('app', 'Manzil / Адрес'),
            'direction_id' => Yii::t('app', 'Yo\'nalish / Направление'),
            'edu_form_id' => Yii::t('app', 'Ta\'lim shakli / Форма обучения'),
            'edu_type_id' => Yii::t('app', 'Qabul turi / Тип поступления'),
            'course_id' => Yii::t('app', 'Kurs / Курс'),
            'consulting_id' => Yii::t('app', 'Konsalting / Консалтинг'),
            'status' => Yii::t('app', 'Holat / Статус'),
            'photo' => Yii::t('app', 'Rasm / Фото'),
            'status_history' => Yii::t('app', 'Holat tarixi / История статусов'),
            'created_by' => Yii::t('app', 'Kirituvchi / Кто добавил'),
            'created_at' => Yii::t('app', 'Ro\'yxatdan o\'tgan vaqti / Время регистрации'),
            'updated_at' => Yii::t('app', 'Tahrirlangan vaqti / Время изменения'),
        ];
    }

    /**
     * Return list of statuses
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_NEW => Yii::t('app', 'Yangi / Новый'),
            self::STATUS_ANKETA => Yii::t('app', 'Anketa to\'ldirildi / Анкета заполнена'),
            self::STATUS_EXAM_SCHEDULED => Yii::t('app', 'Imtihon belgilandi / Экзамен назначен'),
            self::STATUS_EXAM_PASSED => Yii::t('app', 'Imtihondan o\'tdi / Экзамен сдан'),
            self::STATUS_EXAM_FAILED => Yii::t('app', 'Imtihondan yiqildi / Экзамен не сдан'),
            self::STATUS_CONTRACT_SIGNED => Yii::t('app', 'Shartnoma tuzildi / Договор подписан'),
            self::STATUS_PAID => Yii::t('app', 'To\'landi / Оплачено'),
            self::STATUS_REJECTED => Yii::t('app', 'Rad etildi / Отклонен'),
        ];
    }

    /**
     * Defines status color classes
     */
    public static function getStatusColors()
    {
        return [
            self::STATUS_NEW => 'secondary',
            self::STATUS_ANKETA => 'primary',
            self::STATUS_EXAM_SCHEDULED => 'info',
            self::STATUS_EXAM_PASSED => 'success',
            self::STATUS_EXAM_FAILED => 'danger',
            self::STATUS_CONTRACT_SIGNED => 'warning',
            self::STATUS_PAID => 'success',
            self::STATUS_REJECTED => 'dark',
        ];
    }

    public function getStatusLabel()
    {
        $list = self::getStatusList();
        return $list[$this->status] ?? 'Noma\'lum / Неизвестно';
    }

    public function getStatusBadge()
    {
        $colors = self::getStatusColors();
        $color = $colors[$this->status] ?? 'secondary';
        return '<span class="badge bg-' . $color . '">' . $this->getStatusLabel() . '</span>';
    }

    /**
     * Get full name
     */
    public function getFullName()
    {
        return trim($this->last_name . ' ' . $this->first_name . ' ' . $this->middle_name);
    }

    /**
     * Determines if a student can currently take an exam
     */
    public function canTakeExam()
    {
        return $this->status == self::STATUS_EXAM_SCHEDULED;
    }

    /**
     * Updates student status and logs history
     *
     * @param int $newStatus
     * @param int|null $userId
     * @param string|null $comment
     * @return bool
     */
    public function logStatusChange($newStatus, $userId = null, $comment = null)
    {
        $oldStatus = $this->status;
        if ($oldStatus === $newStatus) {
            return true;
        }

        $history = is_string($this->status_history) ? json_decode($this->status_history, true) : $this->status_history;
        if (!is_array($history)) {
            $history = [];
        }

        $history[] = [
            'from_status' => $oldStatus,
            'to_status' => $newStatus,
            'changed_at' => time(),
            'changed_by' => $userId,
            'comment' => $comment,
        ];

        $this->status = $newStatus;
        $this->status_history = $history;

        // Audit Logging
        \common\components\AuditLogger::log('student.status_changed', 'student', $this->id, $oldStatus, $newStatus);

        // Bypassing validation for simple status changes
        return $this->save(false, ['status', 'status_history', 'updated_at']);
    }

    /**
     * Gets query for [[Branch]].
     */
    public function getBranch()
    {
        return $this->hasOne(Branch::class, ['id' => 'branch_id']);
    }

    /**
     * Gets query for [[Direction]].
     */
    public function getDirection()
    {
        return $this->hasOne(Direction::class, ['id' => 'direction_id']);
    }

    /**
     * Gets query for [[EduForm]].
     */
    public function getEduForm()
    {
        return $this->hasOne(EduForm::class, ['id' => 'edu_form_id']);
    }

    /**
     * Gets query for [[EduType]].
     */
    public function getEduType()
    {
        return $this->hasOne(EduType::class, ['id' => 'edu_type_id']);
    }

    /**
     * Gets query for [[Course]].
     */
    public function getCourse()
    {
        return $this->hasOne(Course::class, ['id' => 'course_id']);
    }

    /**
     * Gets query for [[Consulting]].
     */
    public function getConsulting()
    {
        // Model will just be expected to exist at common\models\Consulting
        return $this->hasOne(\common\models\Consulting::class, ['id' => 'consulting_id']);
    }

    /**
     * Gets query for [[StudentOferta]].
     */
    public function getStudentOferta()
    {
        return $this->hasOne(\common\models\StudentOferta::class, ['student_id' => 'id']);
    }

    /**
     * Gets query for [[StudentExams]].
     */
    public function getStudentExams()
    {
        return $this->hasMany(\common\models\StudentExam::class, ['student_id' => 'id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }
}
