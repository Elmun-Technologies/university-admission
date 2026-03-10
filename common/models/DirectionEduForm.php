<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "direction_edu_form".
 *
 * @property int $direction_id
 * @property int $edu_form_id
 *
 * @property Direction $direction
 * @property EduForm $eduForm
 */
class DirectionEduForm extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%direction_edu_form}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['direction_id', 'edu_form_id'], 'required'],
            [['direction_id', 'edu_form_id'], 'integer'],
            [['direction_id', 'edu_form_id'], 'unique', 'targetAttribute' => ['direction_id', 'edu_form_id']],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::class, 'targetAttribute' => ['direction_id' => 'id']],
            [['edu_form_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduForm::class, 'targetAttribute' => ['edu_form_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'direction_id' => Yii::t('app', 'Yo\'nalish / Направление / Direction'),
            'edu_form_id' => Yii::t('app', 'Ta\'lim shakli / Форма обучения / Education Form'),
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
     * Gets query for [[EduForm]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduForm()
    {
        return $this->hasOne(EduForm::class, ['id' => 'edu_form_id']);
    }
}
