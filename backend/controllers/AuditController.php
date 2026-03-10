<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\db\Query;

/**
 * AuditController provides visibility into system events
 */
class AuditController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['superAdmin'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $query = (new Query())->from('{{%audit_log}}')->orderBy(['created_at' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 50],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
}
