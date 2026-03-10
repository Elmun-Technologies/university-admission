<?php

namespace common\jobs;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

/**
 * Job to send notification to Telegram bot
 */
class TelegramNotifyJob extends BaseObject implements JobInterface
{
    public $branchId;
    public $message;

    public function execute($queue)
    {
        $branch = \common\models\Branch::findOne($this->branchId);
        if (!$branch)
            return;

        $configs = json_decode($branch->config_data ?? '{}', true);
        $token = $configs['telegram_bot_token'] ?? null;
        $chatId = $configs['telegram_chat_id'] ?? null;

        if ($token && $chatId) {
            $url = "https://api.telegram.org/bot{$token}/sendMessage";
            $data = [
                'chat_id' => $chatId,
                'text' => $this->message,
                'parse_mode' => 'HTML'
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
        }
    }
}
