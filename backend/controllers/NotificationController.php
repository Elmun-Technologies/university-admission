<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use backend\components\NotificationManager;

/**
 * NotificationController handles AJAX polling and state changes for the UI Bell
 */
class NotificationController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // Must be logged in staff
                    ],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    /**
     * Long polling endpoint for the top nav bell
     */
    public function actionGetUnread()
    {
        $userId = Yii::$app->user->id;
        $count = NotificationManager::getUnreadCount($userId);
        $recent = NotificationManager::getRecent($userId, 10);

        // Format relative times for the UI mapping organically
        $formatter = Yii::$app->formatter;
        foreach ($recent as &$notif) {
            $notif['time_ago'] = $formatter->asRelativeTime($notif['created_at']);
        }

        return [
            'unreadCount' => $count,
            'notifications' => $recent
        ];
    }

    /**
     * Mark single notification as read
     */
    public function actionMarkRead($id)
    {
        Yii::$app->db->createCommand()->update('{{%notifications}}', ['is_read' => 1], [
            'id' => $id,
            'user_id' => Yii::$app->user->id
        ])->execute();

        return ['success' => true];
    }

    /**
     * Bulk mark all as read
     */
    public function actionMarkAllRead()
    {
        NotificationManager::markAllRead(Yii::$app->user->id);
        return ['success' => true];
    }
}
