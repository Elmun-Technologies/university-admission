<?php

namespace common\jobs;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

/**
 * Job to send SMS (Placeholder for now)
 */
class SendWelcomeSmsJob extends BaseObject implements JobInterface
{
    public $phone;
    public $studentName;

    public function execute($queue)
    {
        // Placeholder for step 11/12 SMS gateway
        Yii::info("Sms placeholder: Welcome {$this->studentName} on {$this->phone}", 'queue');
        return true;
    }
}
