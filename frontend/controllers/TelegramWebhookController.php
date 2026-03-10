<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use common\components\TelegramBotHandler;
use yii\web\BadRequestHttpException;

/**
 * TelegramWebhookController handles incoming updates from Telegram
 */
class TelegramWebhookController extends Controller
{
    /**
     * Disable CSRF for webhook POST requests from Telegram
     */
    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $json = file_get_contents('php://input');
        $update = json_decode($json, true);

        if (!$update) {
            throw new BadRequestHttpException('Invalid request');
        }

        // Optional: Simple security check using a secret token in the URL if needed
        // but typically Telegram bot token is the trust root.

        $handler = new TelegramBotHandler();
        try {
            $result = $handler->processUpdate($update);
            return ['status' => 'ok', 'result' => $result];
        } catch (\Exception $e) {
            Yii::error("Telegram Webhook Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
