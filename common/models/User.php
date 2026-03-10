<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property int $branch_id
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property string $auth_key
 * @property string|null $password_reset_token
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $middle_name
 * @property string|null $phone
 * @property int $status
 * @property int|null $last_login_at
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Branch $branch
 */
class User extends \common\db\BranchActiveRecord implements IdentityInterface
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 9;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
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
            [['branch_id', 'username', 'email', 'password_hash', 'auth_key'], 'required'],
            [['branch_id', 'status', 'last_login_at', 'created_at', 'updated_at'], 'integer'],
            [['username', 'first_name', 'last_name', 'middle_name'], 'string', 'max' => 100],
            [['email'], 'string', 'max' => 150],
            [['password_hash', 'password_reset_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['phone'], 'string', 'max' => 20],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
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
            'branch_id' => Yii::t('app', 'Filial / Филиал'),
            'username' => Yii::t('app', 'Login / Логин'),
            'email' => Yii::t('app', 'Email'),
            'password_hash' => Yii::t('app', 'Parol / Пароль'),
            'first_name' => Yii::t('app', 'Ism / Имя'),
            'last_name' => Yii::t('app', 'Familiya / Фамилия'),
            'middle_name' => Yii::t('app', 'Otasining ismi / Отчество'),
            'phone' => Yii::t('app', 'Telefon / Телефон'),
            'status' => Yii::t('app', 'Holat / Статус'),
            'last_login_at' => Yii::t('app', 'Oxirgi kirish / Последний вход'),
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
     * Get full name
     */
    public function getFullName()
    {
        return trim($this->last_name . ' ' . $this->first_name . ' ' . $this->middle_name);
    }

    /**
     * Check if user is an admin via RBAC
     */
    public function isAdmin()
    {
        return Yii::$app->authManager->checkAccess($this->id, 'superAdmin')
            || Yii::$app->authManager->checkAccess($this->id, 'admin');
    }

    // --- IdentityInterface implementation ---

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }
}
