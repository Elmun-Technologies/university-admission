<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\Url;

/**
 * TelegramController sets up the webhook for the bot
 */
class TelegramController extends Controller
{
    /**
     * Set the webhook URL for the bot
     * @param string $url The full URL to the webhook endpoint (e.g. https://domain.uz/telegram-webhook)
     */
    public function actionSetWebhook($url = null)
    {
        // For production, we'd get the bot token from a branch config or .env
        // Here we'll try to find an active branch to get its token
        $branch = \common\models\Branch::find()->one();
        if (!$branch) {
            $this->stderr("No branch found to extract bot token.\n", Console::FG_RED);
            return Controller::EXIT_CODE_ERROR;
        }

        $configs = json_decode($branch->config_data ?? '{}', true);
        $token = $configs['telegram_bot_token'] ?? null;

        if (!$token) {
            $this->stderr("Telegram bot token not found in branch config.\n", Console::FG_RED);
            return Controller::EXIT_CODE_ERROR;
        }

        if (!$url) {
            $this->stdout("Current set URL will be queried from Telegram API...\n");
        }

        $apiUrl = "https://api.telegram.org/bot{$token}/setWebhook?url=" . urlencode($url);

        $this->stdout("Setting webhook to: " . ($url ?: 'DELETING WEBHOOK') . "\n", Console::FG_YELLOW);

        $response = file_get_contents($apiUrl);
        $data = json_decode($response, true);

        if ($data && isset($data['ok']) && $data['ok']) {
            $this->stdout("Success: " . $data['description'] . "\n", Console::FG_GREEN);
        } else {
            $this->stderr("Error: " . ($data['description'] ?? 'Unknown error') . "\n", Console::FG_RED);
        }
    }
}
