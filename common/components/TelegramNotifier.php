<?php

namespace common\components;

use common\models\Telegram;
use Yii;

/**
 * TelegramNotifier handles physical CURL dispatch logic natively bound to Branch settings
 */
class TelegramNotifier
{
    /**
     * Determine if branch has active bot and dispatch text
     *
     * @param int $branchId 
     * @param string $message 
     * @param string $type E.g. 'notify_new_student', 'notify_payment' mapped to DB columns
     * @return bool
     */
    public static function send($branchId, $message, $type = null)
    {
        $settings = Telegram::findOne(['branch_id' => $branchId, 'is_active' => 1]);

        if (!$settings || empty($settings->bot_token) || empty($settings->chat_id)) {
            return false;
        }

        // If a specific type constraint was passed, ensure the branch opted in
        if ($type && isset($settings->$type) && $settings->$type != 1) {
            return false;
        }

        $url = "https://api.telegram.org/bot{$settings->bot_token}/sendMessage";

        // Use native stream context to avoid hard curl dependency requirements
        $data = [
            'chat_id' => $settings->chat_id,
            'text' => $message,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
                'timeout' => 5 // fail quickly, do not hang main app thread
            ]
        ];

        $context = stream_context_create($options);

        try {
            // @ suppresses warning if TG API is blocked returning false organically
            $result = @file_get_contents($url, false, $context);
            return $result !== false;
        } catch (\Exception $e) {
            Yii::error('Telegram API Failed: ' . $e->getMessage(), __METHOD__);
            return false;
        }
    }
}
