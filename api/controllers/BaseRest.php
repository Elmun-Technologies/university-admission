<?php

namespace api\controllers;

use Yii;
use yii\rest\Controller;
use api\components\JwtAuth;
use common\models\Student;
use yii\web\UnauthorizedHttpException;

/**
 * BaseRest we use for shared auth logic
 */
class BaseRest extends Controller
{
    public $user;

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $authHeader = Yii::$app->request->headers->get('Authorization');
            if ($authHeader && preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
                $token = $matches[1];
                $this->user = JwtAuth::validateToken($token);
                if ($this->user) {
                    return true;
                }
            }
            throw new UnauthorizedHttpException('Token yaroqsiz yoki muddati o\'tgan');
        }
        return false;
    }
}
