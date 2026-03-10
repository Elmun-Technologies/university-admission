<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "direction_edu_type".
 *
 * @property int $direction_id
 * @property int $edu_type_id
 *
 * @property Direction $direction
 * @property EduType $eduType
 */
class DirectionEduType extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%direction_edu_type}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['direction_id', 'edu_type_id'], 'required'],
            [['direction_id', 'edu_type_id'], 'integer'],
            [['direction_id', 'edu_type_id'], 'unique', 'targetAttribute' => ['direction_id', 'edu_type_id']],
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
            'direction_id' => Yii::t('app', 'Yo\'nalish / Направление / Direction'),
            'edu_type_id' => Yii::t('app', 'Qabul turi / Тип поступления / Education Type'),
        ];
    }

    /**
     * Gets query for [[Direction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDirection()
    {
        return $this->hasOne(Direction::class, ['id' => 'direction_id']);
    }

    /**
     * Gets query for [[EduType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduType()
    {
        return $this->hasOne(EduType::class, ['id' => 'edu_type_id']);
    }
}
