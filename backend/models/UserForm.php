<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\User;
use common\models\Employee;

/**
 * UserForm coordinates creating a User account with an Employee profile and Role assignments physically natively
 */
class UserForm extends Model
{
    public $id;
    public $username;
    public $email;
    public $password;
    public $role;

    // Employee Props
    public $first_name;
    public $last_name;
    public $phone;
    public $branch_id;
    public $status = 10; // Active native enum

    private $_user;

    public function rules()
    {
        return [
            [['username', 'email', 'role', 'first_name', 'last_name', 'branch_id'], 'required'],
            ['username', 'trim'],
            [
                'username',
                'unique',
                'targetClass' => '\common\models\User',
                'message' => 'Bu login band.',
                'when' => function ($model) {
                    return $model->isNewRecord();
                }
            ],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            [
                'email',
                'unique',
                'targetClass' => '\common\models\User',
                'message' => 'Bu email band.',
                'when' => function ($model) {
                    return $model->isNewRecord();
                }
            ],

            [
                'password',
                'required',
                'when' => function ($model) {
                    return $model->isNewRecord();
                }
            ],
            ['password', 'string', 'min' => 6],

            [['phone'], 'string', 'max' => 20],
            [['status', 'branch_id'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Login (Username)',
            'email' => 'Email',
            'password' => 'Parol',
            'role' => 'Huquq (Role)',
            'first_name' => 'Ism',
            'last_name' => 'Familiya',
            'phone' => 'Telefon',
            'branch_id' => 'Filial',
            'status' => 'Holati'
        ];
    }

    public function isNewRecord()
    {
        return empty($this->id);
    }

    /**
     * Set bindings for Update forms
     */
    public function setModel(User $user)
    {
        $this->_user = $user;
        $this->id = $user->id;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->status = $user->status;
        $this->branch_id = $user->branch_id;

        if ($user->employee) {
            $this->first_name = $user->employee->first_name;
            $this->last_name = $user->employee->last_name;
            $this->phone = $user->employee->phone;
        }

        // Get native primary role
        $roles = Yii::$app->authManager->getRolesByUser($user->id);
        if (!empty($roles)) {
            $this->role = array_keys($roles)[0]; // Pick first mapping
        }
    }

    /**
     * Executes strict transaction persisting User, Employee and Role binding
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($this->isNewRecord()) {
                $user = new User();
                $user->generateAuthKey();
            } else {
                $user = $this->_user;
            }

            $user->username = $this->username;
            $user->email = $this->email;
            $user->status = $this->status;
            $user->branch_id = $this->branch_id;

            if ($this->password) {
                $user->setPassword($this->password);
            }

            if (!$user->save(false)) { // Skip native validation as Form did it
                throw new \Exception("Noma'lum xatolik user saqlashda.");
            }

            // Sync Employee mapping
            $employee = Employee::findOne(['user_id' => $user->id]);
            if (!$employee) {
                $employee = new Employee();
                $employee->user_id = $user->id;
            }
            $employee->branch_id = $this->branch_id;
            $employee->first_name = $this->first_name;
            $employee->last_name = $this->last_name;
            $employee->phone = $this->phone;
            $employee->position = $this->role; // Meta map loosely if needed

            if (!$employee->save(false)) {
                throw new \Exception("Xodim profilini saqlashda xatolik.");
            }

            // Revoke Old, Assign New Role Natively via RBAC
            $auth = Yii::$app->authManager;
            $auth->revokeAll($user->id);
            $roleObj = $auth->getRole($this->role);
            if ($roleObj) {
                $auth->assign($roleObj, $user->id);
            }

            $transaction->commit();
            return true;

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
            return false;
        }
    }
}
