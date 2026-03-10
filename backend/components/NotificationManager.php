<?php

namespace backend\components;

use common\models\Notification; // We'll assume a model exists mapping to notifications table
use Yii;

/**
 * Handles creation and querying of real-time backend notifications
 */
class NotificationManager
{
    const TYPE_NEW_STUDENT = 'new_student';
    const TYPE_EXAM_DONE = 'exam_done';
    const TYPE_PAYMENT_RECEIVED = 'payment_received';
    const TYPE_SYSTEM_ALERT = 'system_alert';

    /**
     * Create a new notification for a specific admin user
     */
    public static function createNotification($userId, $type, $title, $message, $link = null)
    {
        // Execute a raw insert if the Active Record model isn't fully scaffolded natively yet
        return Yii::$app->db->createCommand()->insert('{{%notifications}}', [
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'is_read' => 0,
            'created_at' => time(),
        ])->execute();
    }

    /**
     * Get count of unread notifications
     */
    public static function getUnreadCount($userId)
    {
        return (int) Yii::$app->db->createCommand(
            'SELECT COUNT(*) FROM {{%notifications}} WHERE user_id = :userId AND is_read = 0'
        )->bindValue(':userId', $userId)->queryScalar();
    }

    /**
     * Get recent notifications
     */
    public static function getRecent($userId, $limit = 10)
    {
        return Yii::$app->db->createCommand(
            'SELECT * FROM {{%notifications}} WHERE user_id = :userId ORDER BY created_at DESC LIMIT :limit'
        )
            ->bindValue(':userId', $userId)
            ->bindValue(':limit', $limit)
            ->queryAll();
    }

    /**
     * Mark all as read for user
     */
    public static function markAllRead($userId)
    {
        return Yii::$app->db->createCommand()->update('{{%notifications}}', ['is_read' => 1], [
            'user_id' => $userId,
            'is_read' => 0
        ])->execute();
    }
}
