<?php

namespace common\jobs;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

/**
 * Job to send backup file to Telegram
 */
class SendBackupToTelegramJob extends BaseObject implements JobInterface
{
    public $filePath;
    public $branchId;

    public function execute($queue)
    {
        if (!file_exists($this->filePath)) {
            Yii::error("Backup file not found: " . $this->filePath);
            return false;
        }

        // Placeholder for step 14: Actually call TelegramNotifier::sendDocument()
        Yii::info("Sending backup file {$this->filePath} for branch {$this->branchId}", 'queue');

        // After sending, delete if the job logic implies it
        // @unlink($this->filePath);

        return true;
    }
}
