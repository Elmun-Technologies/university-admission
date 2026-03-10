<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use common\models\StudentNotificationPref;

/**
 * TelegramController handles incoming webhook requests from Telegram Bot
 */
class TelegramController extends Controller
{
    // Disable CSRF for webhook
    public $enableCsrfValidation = false;

    // Set your bot token in .env, e.g. TELEGRAM_BOT_TOKEN
    private function getBotToken()
    {
        return getenv('TELEGRAM_BOT_TOKEN') ?: 'YOUR_TELEGRAM_BOT_TOKEN_HERE';
    }

    public function actionWebhook()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $input = file_get_contents('php://input');
        $update = json_decode($input, true);

        if (!$update || !isset($update['message'])) {
            return ['status' => 'ignored'];
        }

        $message = $update['message'];
        $chatId = $message['chat']['id'] ?? null;
        $text = $message['text'] ?? '';

        if (!$chatId || empty($text)) {
            return ['status' => 'ignored'];
        }

        // Handle /start command which might include the 4-digit code
        // e.g. User types: 1234 or /start 1234
        $code = trim(str_replace('/start', '', $text));

        if (empty($code)) {
            $this->sendMessage($chatId, "Assalomu alaykum! Beruniy Qabul platformasida ro'yxatdan o'tgan bo'lsangiz, profilingizdagi 4 xonali tasdiqlash kodini yuboring.");
            return ['status' => 'ok'];
        }

        // Validate 4-digit code
        if (preg_match('/^\d{4}$/', $code)) {
            $pref = StudentNotificationPref::findOne([
                'type' => 'telegram',
                'telegram_code' => $code
            ]);

            if ($pref) {
                if ($pref->telegram_id == $chatId) {
                    $this->sendMessage($chatId, "Sizning hisobingiz allaqachon ulangan! Barcha xabarnomalarni shu yerdan olishingiz mumkin.");
                } else {
                    $pref->telegram_id = $chatId;
                    // Optionally regenerate code to prevent reuse, but maybe better to keep it
                    if ($pref->save(false, ['telegram_id'])) {
                        $studentName = $pref->student->getFullName();
                        $this->sendMessage($chatId, "Muvaffaqiyatli ulangan! Xush kelibsiz, $studentName. Endi qabul jarayoniga oid barcha xabarnomalarni shu yerdan olasiz.");
                    } else {
                        $this->sendMessage($chatId, "Xatolik yuz berdi. Iltimos keyinroq qayta urinib ko'ring.");
                    }
                }
            } else {
                $this->sendMessage($chatId, "Kiritilgan kod xato yoki serverda topilmadi. Profilingizdagi kodni qayta tekshirib yuboring.");
            }
        } else {
            $this->sendMessage($chatId, "Kiritilgan kod formati xato. Faqat 4 ta raqamdan iborat kodni yuboring.");
        }

        return ['status' => 'ok'];
    }

    private function sendMessage($chatId, $text)
    {
        $token = $this->getBotToken();
        $url = "https://api.telegram.org/bot{$token}/sendMessage";

        $data = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_exec($ch);
        curl_close($ch);
    }
}
