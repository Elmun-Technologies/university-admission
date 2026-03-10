<?php

namespace common\jobs;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

/**
 * Job to send notification to Telegram bot
 */
class SendTelegramNotificationJob extends BaseObject implements JobInterface
{
    public $chatId;
    public $message;
    public $parseMode = 'HTML';

    public function execute($queue)
    {
        // This will call the actual notification component when implemented in step 12
        try {
            // Simplified execution for now, placeholder for step 12 Telegram component
            Yii::info("Sending Telegram to {$this->chatId}: {$this->message}", 'queue');
            return true;
        } catch (\Exception $e) {
            Yii::error("Failed to send Telegram: " . $e->getMessage());
            return false;
        }
    }
}
