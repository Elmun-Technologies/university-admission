<?php

namespace api\controllers;

use Yii;
use yii\rest\Controller;
use api\components\JwtAuth;
use common\models\User;
use yii\web\UnauthorizedHttpException;

/**
 * AuthController handles mobile app login and token refresh
 */
class AuthController extends Controller
{
    public function actionLogin()
    {
        $request = Yii::$app->request;
        $username = $request->post('username');
        $password = $request->post('password');

        $user = User::findByUsername($username);
        if ($user && $user->validatePassword($password)) {
            return [
                'status' => 'success',
                'token' => JwtAuth::generateToken($user->id),
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'full_name' => $user->employee ? $user->employee->first_name . ' ' . $user->employee->last_name : $user->username
                ]
            ];
        }

        throw new UnauthorizedHttpException('Login yoki parol xato');
    }
}
