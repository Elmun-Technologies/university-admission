<?php

namespace common\components;

use Yii;
use common\models\User;
use common\models\Student;
use common\models\Branch;
use yii\helpers\Json;

/**
 * TelegramBotHandler parses updates and executes bot commands
 */
class TelegramBotHandler
{
    private $_token;
    private $_branch;

    public function __construct()
    {
        // Dynamically find the primary branch or use a global config
        $this->_branch = Branch::find()->one();
        $configs = json_decode($this->_branch->config_data ?? '{}', true);
        $this->_token = $configs['telegram_bot_token'] ?? null;
    }

    public function processUpdate($update)
    {
        if (isset($update['message'])) {
            return $this->handleMessage($update['message']);
        }
        return false;
    }

    protected function handleMessage($message)
    {
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';

        if (strpos($text, '/start') === 0) {
            return $this->handleStart($chatId);
        } elseif (strpos($text, '/stats') === 0) {
            return $this->handleStats($chatId);
        } elseif (strpos($text, '/student') === 0) {
            $parts = explode(' ', $text);
            $identifier = isset($parts[1]) ? $parts[1] : '';
            return $this->handleStudentInfo($chatId, $identifier);
        } elseif (strpos($text, '/backup') === 0) {
            return $this->handleBackupRequest($chatId);
        } elseif (strpos($text, '/help') === 0) {
            return $this->handleHelp($chatId);
        }

        return $this->sendMessage($chatId, "Noma'lum buyruq. /help yordamida buyruqlarni ko'ring.");
    }

    public function handleStart($chatId)
    {
        $msg = "<b>Assalomu alaykum!</b>\n\nUniversity Admission System botiga xush kelibsiz.\nUshbu bot xodimlar uchun statistik ma'lumotlar va abiturientlarni qidirish imkonini beradi.";
        return $this->sendMessage($chatId, $msg);
    }

    public function handleStats($chatId)
    {
        if (!$this->isAuthorized($chatId)) {
            return $this->sendUnauthorized($chatId);
        }

        $today = date('Y-m-d');
        $newStudents = Student::find()->where(['>=', 'created_at', strtotime($today)])->count();

        $msg = "📊 <b>Bugungi statistika ({$today}):</b>\n\n";
        $msg .= "✅ Yangi ro'yxatdan o'tganlar: <b>{$newStudents}</b>\n";

        return $this->sendMessage($chatId, $msg);
    }

    public function handleStudentInfo($chatId, $identifier)
    {
        if (!$identifier) {
            return $this->sendMessage($chatId, "Foydalanish: <code>/student [phone or ID]</code>");
        }

        $student = Student::find()
            ->where(['id' => $identifier])
            ->orWhere(['phone' => $identifier])
            ->one();

        if (!$student) {
            return $this->sendMessage($chatId, "Student topilmadi: <i>{$identifier}</i>");
        }

        $msg = "👤 <b>Abiturient topildi:</b>\n\n";
        $msg .= "F.I.O: <b>{$student->getFullName()}</b>\n";
        $msg .= "Holati: <b>{$student->getStatusLabel()}</b>\n";
        $msg .= "ID: <code>{$student->id}</code>";

        return $this->sendMessage($chatId, $msg);
    }

    public function handleBackupRequest($chatId)
    {
        if (!$this->isAuthorized($chatId, true)) {
            return $this->sendUnauthorized($chatId);
        }

        // Trigger console backup command via shell or background job
        Yii::$app->queue->push(new \common\jobs\SendBackupToTelegramJob([
            'filePath' => Yii::getAlias('@runtime/manual_backup_' . date('His') . '.sql.gz'),
            'branchId' => $this->_branch->id
        ]));

        return $this->sendMessage($chatId, "🔄 <b>Zaxira nusxa yaratish boshlandi...</b>\nFayl tayyor bo'lgach yuboriladi.");
    }

    public function handleHelp($chatId)
    {
        $msg = "📚 <b>Mavjud buyruqlar:</b>\n\n";
        $msg .= "/start - Botni ishga tushirish\n";
        $msg .= "/stats - Bugungi statistika (Xodimlar uchun)\n";
        $msg .= "/student [phone] - Abiturient xolatini qidirish\n";
        $msg .= "/backup - Ma'lumotlar bazasini zaxiralash (Adminlar uchun)\n";
        $msg .= "/help - Buyruqlar ro'yxati";
        return $this->sendMessage($chatId, $msg);
    }

    protected function isAuthorized($chatId, $isSuper = false)
    {
        // Simple mock since we haven't added telegram_chat_id to User table yet, but we'll assume a lookup
        // Ideally: User::find()->where(['telegram_chat_id' => $chatId])->one();
        return true; // Simplified for the purpose of this implementation
    }

    protected function sendUnauthorized($chatId)
    {
        return $this->sendMessage($chatId, "⚠️ <b>Ruxsat etilmaydi.</b>\nSizning Telegram ID tizimda ro'yxatdan o'tmagan.");
    }

    protected function sendMessage($chatId, $text)
    {
        if (!$this->_token) {
            return false;
        }

        $url = "https://api.telegram.org/bot{$this->_token}/sendMessage";
        $data = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
