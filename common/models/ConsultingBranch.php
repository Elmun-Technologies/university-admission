<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "consulting_branch".
 *
 * @property int $id
 * @property int $consulting_id
 * @property string $name
 * @property string|null $address
 * @property string|null $phone
 * @property int $status
 * @property int $created_at
 *
 * @property Consulting $consulting
 */
class ConsultingBranch extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%consulting_branch}}';
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
            [['consulting_id', 'name'], 'required'],
            [['consulting_id', 'status', 'created_at'], 'integer'],
            [['name', 'address'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 20],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            [['consulting_id'], 'exist', 'skipOnError' => true, 'targetClass' => Consulting::class, 'targetAttribute' => ['consulting_id' => 'id']],
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
            'name' => Yii::t('app', 'Filial nomi / Название филиала'),
            'address' => Yii::t('app', 'Manzil / Адрес'),
            'phone' => Yii::t('app', 'Telefon / Телефон'),
            'status' => Yii::t('app', 'Holat / Статус'),
        ];
    }

    /**
     * Gets query for [[Consulting]].
     */
    public function getConsulting()
    {
        return $this->hasOne(Consulting::class, ['id' => 'consulting_id']);
    }
}
