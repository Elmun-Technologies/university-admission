<?php

namespace common\jobs;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use common\models\Student;

/**
 * Job to sync student data to AmoCRM
 */
class SyncToCrmJob extends BaseObject implements JobInterface
{
    public $studentId;
    public $eventType; // registered, status_changed, exam_passed, paid

    public function execute($queue)
    {
        $student = Student::findOne($this->studentId);
        if (!$student) {
            return false;
        }

        // Placeholder for step 13: Actually call AmoCrmSync::sync...
        Yii::info("Syncing student {$student->id} to CRM for event: {$this->eventType}", 'queue');

        return true;
    }
}
