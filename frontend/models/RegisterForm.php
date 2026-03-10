<?php

namespace frontend\models;

use common\models\Student;
use common\models\User;
use Yii;
use yii\base\Model;

/**
 * Register form
 */
class RegisterForm extends Model
{
    public $phone;
    public $first_name;
    public $last_name;
    public $password;
    public $password_confirm;
    public $verifyCode;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['phone', 'first_name', 'last_name', 'password', 'password_confirm'], 'required'],

            // Name filters
            [['first_name', 'last_name'], 'trim'],
            [['first_name', 'last_name'], 'string', 'min' => 2, 'max' => 100],

            // Phone exact strict validation mapping to Student table rule
            ['phone', 'match', 'pattern' => '/^\+998\d{9}$/', 'message' => Yii::t('app', 'Telefon formati xato (+998XXXXXXXXX)')],
            ['phone', 'unique', 'targetClass' => '\common\models\Student', 'message' => Yii::t('app', 'Bu telefon raqam ro\'yxatdan o\'tgan.')],

            // Passwords
            ['password', 'string', 'min' => 6],
            ['password_confirm', 'compare', 'compareAttribute' => 'password', 'message' => Yii::t('app', 'Parollar mos emas.')],

            // Captcha to block bots
            ['verifyCode', 'captcha', 'captchaAction' => 'auth/captcha'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'phone' => Yii::t('app', 'Telefon raqam / Номер телефона'),
            'first_name' => Yii::t('app', 'Ismingiz / Ваше имя'),
            'last_name' => Yii::t('app', 'Familiyangiz / Ваша фамилия'),
            'password' => Yii::t('app', 'Parol / Пароль'),
            'password_confirm' => Yii::t('app', 'Parolni tasdiqlang / Подтвердите пароль'),
            'verifyCode' => Yii::t('app', 'Tekshiruv kodi / Код проверки'),
        ];
    }

    /**
     * Signs user up and creates the empty Student profile using DB Transaction.
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function register()
    {
        if (!$this->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 1. Create User Authentication Entity
            $user = new User();
            $user->username = $this->phone;
            $user->email = $this->phone . '@applicant.local'; // Mock required email field
            $user->first_name = $this->first_name;
            $user->last_name = $this->last_name;
            $user->phone = $this->phone;
            $user->branch_id = 1; // Default branch assigned internally
            $user->status = User::STATUS_ACTIVE;
            $user->setPassword($this->password);
            $user->generateAuthKey();

            if (!$user->save()) {
                throw new \Exception("User save failed: " . json_encode($user->errors));
            }

            // 2. Create Student Entity Base Record
            $student = new Student();
            $student->first_name = $this->first_name;
            $student->last_name = $this->last_name;
            $student->phone = $this->phone;
            $student->branch_id = 1;
            $student->status = Student::STATUS_NEW;
            $student->created_by = $user->id;

            // Auto-log the creation in history natively
            $student->logStatusChange(Student::STATUS_NEW, $user->id, "Tizimdan ro'yxatdan o'tdi");

            if (!$student->save()) {
                throw new \Exception("Student save failed: " . json_encode($student->errors));
            }

            // 3. Dispatch Telegram Notification (Assuming component exists)
            // if (Yii::$app->has('telegram')) { ... }

            // 4. Assign Default Applicant Role
            $auth = Yii::$app->authManager;
            if ($auth) {
                // Assuming 'operator' can act as base or we build 'applicant'
                // We'll leave this empty for now since applicants aren't RBAC checked 
                // in the same manner as staff usually
            }

            $transaction->commit();
            return $user;
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error("Registration Exception: " . $e->getMessage());
            return false;
        }
    }
}
