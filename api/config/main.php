<?php

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'response' => [
            'format' => \yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'GET v1/profile' => 'student/profile',
                'POST v1/login' => 'auth/login',
            ],
        ],
    ],
];
