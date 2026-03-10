<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "branch".
 *
 * @property int $id
 * @property string $name_uz
 * @property string|null $name_ru
 * @property string|null $name_en
 * @property string|null $address_uz
 * @property string|null $address_ru
 * @property string|null $tel1
 * @property string|null $tel2
 * @property string|null $telegram
 * @property string|null $instagram
 * @property string|null $rector_uz
 * @property string|null $rector_ru
 * @property string|null $logo
 * @property int|null $cons_id
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Direction[] $directions
 * @property User[] $users
 */
class Branch extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%branch}}';
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
            [['name_uz'], 'required'],
            [['address_uz', 'address_ru'], 'string'],
            [['cons_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['name_uz', 'name_ru', 'name_en', 'rector_uz', 'rector_ru', 'logo'], 'string', 'max' => 255],
            [['tel1', 'tel2'], 'string', 'max' => 20],
            [['telegram', 'instagram'], 'string', 'max' => 100],
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
            'name_uz' => Yii::t('app', 'Nomi (O\'z) / Название (Уз)'),
            'name_ru' => Yii::t('app', 'Nomi (Ru) / Название (Ру)'),
            'name_en' => Yii::t('app', 'Nomi (En) / Название (Англ)'),
            'address_uz' => Yii::t('app', 'Manzil (O\'z) / Адрес (Уз)'),
            'address_ru' => Yii::t('app', 'Manzil (Ru) / Адрес (Ру)'),
            'tel1' => Yii::t('app', 'Telefon 1 / Телефон 1'),
            'tel2' => Yii::t('app', 'Telefon 2 / Телефон 2'),
            'telegram' => Yii::t('app', 'Telegram'),
            'instagram' => Yii::t('app', 'Instagram'),
            'rector_uz' => Yii::t('app', 'Rektor (O\'z) / Ректор (Уз)'),
            'rector_ru' => Yii::t('app', 'Rektor (Ru) / Ректор (Ру)'),
            'logo' => Yii::t('app', 'Logotip / Логотип'),
            'cons_id' => Yii::t('app', 'Konsalting ID / ID Консалтинга'),
            'status' => Yii::t('app', 'Holat / Статус'),
            'created_at' => Yii::t('app', 'Yaratilgan vaqti / Время создания'),
            'updated_at' => Yii::t('app', 'Tahrirlangan vaqti / Время изменения'),
        ];
    }

    /**
     * Gets query for [[Directions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDirections()
    {
        return $this->hasMany(Direction::class, ['branch_id' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['branch_id' => 'id']);
    }

    /**
     * Fetch active branches as a key-value list
     * @return array
     */
    public static function getActiveBranches()
    {
        return self::find()
            ->select(['name_uz', 'id'])
            ->where(['status' => self::STATUS_ACTIVE])
            ->indexBy('id')
            ->column();
    }

    /**
     * Get full URL to branch logo
     * @return string|null
     */
    public function getLogoUrl()
    {
        if ($this->logo) {
            return Yii::$app->request->hostInfo . '/uploads/branches/' . $this->logo;
        }
        return null;
    }
}
