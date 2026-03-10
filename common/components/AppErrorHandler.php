<?php

namespace common\components;

use Yii;
use yii\web\ErrorHandler;
use common\components\AuditLogger;

/**
 * AppErrorHandler catches exceptions and routes alerts
 */
class AppErrorHandler extends ErrorHandler
{
    protected function renderException($exception)
    {
        if ($exception instanceof \yii\web\HttpException) {
            $code = $exception->statusCode;
        } else {
            $code = 500;
        }

        // Log to Audit Log for staff/admin actions
        AuditLogger::log('system.error', 'exception', $code, null, [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);

        // Send Telegram alert for critical 500 errors in non-debug mode
        if ($code >= 500 && !YII_DEBUG) {
            $this->sendTelegramAlert($exception);
        }

        return parent::renderException($exception);
    }

    protected function sendTelegramAlert($exception)
    {
        $msg = "🚨 <b>SYSTEM ERROR (500)</b>\n\n";
        $msg .= "Message: <code>" . $exception->getMessage() . "</code>\n";
        $msg .= "File: " . $exception->getFile() . ":" . $exception->getLine() . "\n";
        $msg .= "URL: " . Yii::$app->request->absoluteUrl;

        Yii::$app->queue->push(new \common\jobs\SendTelegramNotificationJob([
            'chatId' => getenv('TELEGRAM_CHAT_ID'), // Admin chat
            'message' => $msg
        ]));
    }
}
