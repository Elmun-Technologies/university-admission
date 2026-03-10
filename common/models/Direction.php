<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "direction".
 *
 * @property int $id
 * @property int $branch_id
 * @property string $name_uz
 * @property string|null $name_ru
 * @property string|null $name_en
 * @property string|null $code
 * @property string|null $description_uz
 * @property string|null $description_ru
 * @property float|null $tuition_fee
 * @property int|null $duration_years
 * @property int $status
 * @property int $sort_order
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Branch $branch
 * @property DirectionEduForm[] $directionEduForms
 * @property EduForm[] $eduForms
 * @property DirectionEduType[] $directionEduTypes
 * @property EduType[] $eduTypes
 * @property DirectionSubject[] $directionSubjects
 * @property Student[] $students
 */
class Direction extends \common\db\BranchActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%direction}}';
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
            [['branch_id', 'name_uz'], 'required'],
            [['branch_id', 'duration_years', 'status', 'sort_order', 'created_at', 'updated_at'], 'integer'],
            [['description_uz', 'description_ru'], 'string'],
            [['tuition_fee'], 'number'],
            [['name_uz', 'name_ru', 'name_en'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 20],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['sort_order', 'default', 'value' => 0],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::class, 'targetAttribute' => ['branch_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'branch_id' => Yii::t('app', 'Filial / Филиал / Branch'),
            'name_uz' => Yii::t('app', 'Nomi (O\'z) / Название (Уз) / Name (Uz)'),
            'name_ru' => Yii::t('app', 'Nomi (Ru) / Название (Ру) / Name (Ru)'),
            'name_en' => Yii::t('app', 'Nomi (En) / Название (Англ) / Name (En)'),
            'code' => Yii::t('app', 'Kod (Shifr) / Код направления / Code'),
            'description_uz' => Yii::t('app', 'Tavsif (O\'z) / Описание (Уз) / Description (Uz)'),
            'description_ru' => Yii::t('app', 'Tavsif (Ru) / Описание (Ру) / Description (Ru)'),
            'tuition_fee' => Yii::t('app', 'Kontrakt narxi / Сумма контракта / Tuition Fee'),
            'duration_years' => Yii::t('app', 'O\'qish muddati / Срок обучения / Duration (years)'),
            'status' => Yii::t('app', 'Holat / Статус / Status'),
            'sort_order' => Yii::t('app', 'Tartib / Порядок / Sort Order'),
            'created_at' => Yii::t('app', 'Yaratilgan vaqti / Время создания / Created At'),
            'updated_at' => Yii::t('app', 'Tahrirlangan vaqti / Время изменения / Updated At'),
        ];
    }

    /**
     * Gets query for [[Branch]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branch::class, ['id' => 'branch_id']);
    }

    /**
     * Gets query for [[DirectionEduForms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDirectionEduForms()
    {
        return $this->hasMany(DirectionEduForm::class, ['direction_id' => 'id']);
    }

    /**
     * Gets query for [[EduForms]] through [[DirectionEduForms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduForms()
    {
        return $this->hasMany(EduForm::class, ['id' => 'edu_form_id'])
            ->via('directionEduForms');
    }

    /**
     * Gets query for [[DirectionEduTypes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDirectionEduTypes()
    {
        return $this->hasMany(DirectionEduType::class, ['direction_id' => 'id']);
    }

    /**
     * Gets query for [[EduTypes]] through [[DirectionEduTypes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduTypes()
    {
        return $this->hasMany(EduType::class, ['id' => 'edu_type_id'])
            ->via('directionEduTypes');
    }

    /**
     * Gets query for [[DirectionSubjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDirectionSubjects()
    {
        // Assuming DirectionSubject model will be created later based on m260101_000010_create_direction_subject_table
        return $this->hasMany(DirectionSubject::class, ['direction_id' => 'id']);
    }

    /**
     * Gets query for [[Students]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudents()
    {
        return $this->hasMany(Student::class, ['direction_id' => 'id']);
    }

    /**
     * Get active education forms for this specific direction
     *
     * @return EduForm[]
     */
    public function getAvailableEduForms()
    {
        return $this->getEduForms()
            ->andWhere(['status' => EduForm::STATUS_ACTIVE])
            ->orderBy(['sort_order' => SORT_ASC])
            ->all();
    }

    /**
     * Format tuition fee as "5,500,000 UZS"
     *
     * @return string
     */
    public function getTuitionFeeFormatted()
    {
        if ($this->tuition_fee) {
            return number_format($this->tuition_fee, 0, '.', ',') . ' UZS';
        }
        return '0 UZS';
    }

    /**
     * Get list of active directions for a specific branch, with egar loaded pivot relations
     *
     * @param int $branchId
     * @return array|ActiveRecord[]
     */
    public static function getActiveByBranch($branchId)
    {
        return self::find()
            ->where([
                'branch_id' => $branchId,
                'status' => self::STATUS_ACTIVE
            ])
            ->with(['eduForms', 'eduTypes'])
            ->orderBy(['sort_order' => SORT_ASC])
            ->all();
    }
}
