<?php

namespace common\jobs;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use common\models\CrmPush;
use common\components\AmoCrmSync;

/**
 * CrmSyncJob processes a single CRM push item via background queue
 */
class CrmSyncJob extends BaseObject implements JobInterface
{
    public $pushId;

    public function execute($queue)
    {
        $item = CrmPush::findOne($this->pushId);
        if (!$item || $item->status === 1) {
            return;
        }

        $sync = new AmoCrmSync();

        try {
            $item->attempts++;
            $success = false;

            switch ($item->event_type) {
                case 'registered':
                    $success = (bool) $sync->syncNewStudent($item->student);
                    break;
                case 'status_changed':
                    $payload = json_decode($item->payload, true);
                    $success = $sync->syncStatusChange($item->student, $payload['new_status'] ?? 0);
                    break;
                case 'paid':
                    $success = $sync->syncPayment($item->student->studentOferta);
                    break;
            }

            if ($success) {
                $item->status = 1;
                $item->sent_at = time();
            } else {
                if ($item->attempts >= 3) {
                    $item->status = 2; // Failed
                }
            }
        } catch (\Exception $e) {
            Yii::error("CRM Job Error: " . $e->getMessage());
            if ($item->attempts >= 3) {
                $item->status = 2;
            }
        }

        $item->save(false);
    }
}
