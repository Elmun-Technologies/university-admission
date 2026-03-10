<?php
namespace frontend\controllers;

use yii\web\Controller;

class SiteController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['auth/login']);
        }
        return $this->redirect(['dashboard/index']);
    }
}
