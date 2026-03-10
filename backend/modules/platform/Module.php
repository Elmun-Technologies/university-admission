<?php

namespace backend\modules\platform;

use Yii;
use yii\base\Module;
use yii\filters\AccessControl;

/**
 * platform module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'backend\modules\platform\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            $user = Yii::$app->user->identity;
                            return $user && $user->is_platform_admin;
                        }
                    ],
                ],
            ],
        ];
    }
}
