<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "exam".
 *
 * @property int $id
 * @property int $branch_id
 * @property int $direction_id
 * @property int $edu_type_id
 * @property string $name_uz
 * @property string|null $name_ru
 * @property int $duration_minutes
 * @property int $questions_count
 * @property int $passing_score
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Branch $branch
 * @property Direction $direction
 * @property EduType $eduType
 * @property ExamDate[] $examDates
 * @property ExamSubject[] $examSubjects
 */
class Exam extends \common\db\BranchActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%exam}}';
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
            [['branch_id', 'direction_id', 'edu_type_id', 'name_uz', 'duration_minutes', 'questions_count', 'passing_score'], 'required'],
            [['branch_id', 'direction_id', 'edu_type_id', 'duration_minutes', 'questions_count', 'passing_score', 'status', 'created_at', 'updated_at'], 'integer'],
            [['name_uz', 'name_ru'], 'string', 'max' => 255],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::class, 'targetAttribute' => ['branch_id' => 'id']],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::class, 'targetAttribute' => ['direction_id' => 'id']],
            [['edu_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduType::class, 'targetAttribute' => ['edu_type_id' => 'id']],
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
            'direction_id' => Yii::t('app', 'Yo\'nalish / Направление'),
            'edu_type_id' => Yii::t('app', 'Qabul turi / Тип поступления'),
            'name_uz' => Yii::t('app', 'Nomi (O\'z) / Название (Уз)'),
            'name_ru' => Yii::t('app', 'Nomi (Ru) / Название (Ру)'),
            'duration_minutes' => Yii::t('app', 'Davomiyligi (daqiqa) / Длительность (минут)'),
            'questions_count' => Yii::t('app', 'Savollar soni / Количество вопросов'),
            'passing_score' => Yii::t('app', 'O\'tish bali / Проходной балл'),
            'status' => Yii::t('app', 'Holat / Статус'),
        ];
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
     * Gets query for [[EduType]].
     */
    public function getEduType()
    {
        return $this->hasOne(EduType::class, ['id' => 'edu_type_id']);
    }

    /**
     * Gets query for [[ExamDates]].
     */
    public function getExamDates()
    {
        return $this->hasMany(ExamDate::class, ['exam_id' => 'id']);
    }

    /**
     * Gets query for [[ExamSubjects]].
     */
    public function getExamSubjects()
    {
        // Must exist based on previous migrations
        return $this->hasMany(\common\models\ExamSubject::class, ['exam_id' => 'id']);
    }
}
