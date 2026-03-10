<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "edu_type".
 *
 * @property int $id
 * @property string $name_uz
 * @property string|null $name_ru
 * @property string|null $name_en
 * @property int $status
 * @property int $sort_order
 */
class EduType extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%edu_type}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_uz'], 'required'],
            [['status', 'sort_order'], 'integer'],
            [['name_uz', 'name_ru', 'name_en'], 'string', 'max' => 255],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['sort_order', 'default', 'value' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name_uz' => Yii::t('app', 'Nomi (O\'z) / Тип поступления (Уз)'),
            'name_ru' => Yii::t('app', 'Nomi (Ru) / Тип поступления (Ру)'),
            'name_en' => Yii::t('app', 'Nomi (En) / Тип поступления (En)'),
            'status' => Yii::t('app', 'Holat / Статус'),
            'sort_order' => Yii::t('app', 'Tartib raqami / Порядок сортировки'),
        ];
    }

    /**
     * Get list of admission types for dropdowns
     * @return array [id => name_uz]
     */
    public static function getList()
    {
        return ArrayHelper::map(
            self::find()
                ->where(['status' => self::STATUS_ACTIVE])
                ->orderBy(['sort_order' => SORT_ASC])
                ->all(),
            'id',
            'name_uz'
        );
    }
}
