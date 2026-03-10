<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "subject".
 *
 * @property int $id
 * @property string $name_uz
 * @property string|null $name_ru
 * @property string|null $name_en
 * @property int $status
 *
 * @property DirectionSubject[] $directionSubjects
 * @property Question[] $questions
 */
class Subject extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%subject}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_uz'], 'required'],
            [['status'], 'integer'],
            [['name_uz', 'name_ru', 'name_en'], 'string', 'max' => 255],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name_uz' => Yii::t('app', 'Nomi (O\'z) / Предмет (Уз) / Subject (Uz)'),
            'name_ru' => Yii::t('app', 'Nomi (Ru) / Предмет (Ру) / Subject (Ru)'),
            'name_en' => Yii::t('app', 'Nomi (En) / Предмет (En) / Subject (En)'),
            'status' => Yii::t('app', 'Holat / Статус / Status'),
        ];
    }

    /**
     * Gets query for [[DirectionSubjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDirectionSubjects()
    {
        // DirectionSubject model to be fully defined based on m260101_000010_create_direction_subject_table
        return $this->hasMany(DirectionSubject::class, ['subject_id' => 'id']);
    }

    /**
     * Gets query for [[Questions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestions()
    {
        return $this->hasMany(Question::class, ['subject_id' => 'id']);
    }

    /**
     * Get list of active subjects for dropdowns
     * @return array [id => name_uz]
     */
    public static function getList()
    {
        return ArrayHelper::map(
            self::find()
                ->where(['status' => self::STATUS_ACTIVE])
                ->all(),
            'id',
            'name_uz'
        );
    }
}
