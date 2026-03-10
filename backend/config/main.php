<?php

$params = array_merge(
    // require __DIR__ . '/../../common/config/params.php',
    // require __DIR__ . '/../../common/config/params-local.php',
    // require __DIR__ . '/params.php',
    // require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'defaultRoute' => 'dashboard/index',
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'platform' => [
            'class' => 'backend\modules\platform\Module',
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
            'cookieValidationKey' => getenv('COOKIE_VALIDATION_KEY') ?: 'testkey',
            'baseUrl' => '/admin',
            'csrfCookie' => [
                'httpOnly' => true,
                'secure' => true, // Ensure production uses HTTPS
            ],
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => [
                'name' => '_identity-backend',
                'httpOnly' => true,
                'secure' => true,
            ],
        ],
        'session' => [
            'name' => 'advanced-backend',
            'cookieParams' => [
                'httpOnly' => true,
                'secure' => true,
                'lifetime' => 14400, // 4 hours timeout requested
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        /*
        'errorHandler' => [
            'class' => \common\components\AppErrorHandler::class,
            'errorAction' => 'site/error',
        ],
        */
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'assetManager' => [
            'basePath' => dirname(__DIR__) . '/web/assets',
            'baseUrl' => '/admin/assets',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ],
    'params' => $params,
];
