<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use common\models\CrmPush;
use common\components\AmoCrmSync;

/**
 * CrmController processes the synchronization queue for external CRM
 */
class CrmController extends Controller
{
    /**
     * Process pending CRM push records (Cron task)
     */
    public function actionProcess()
    {
        $pending = CrmPush::find()
            ->where(['status' => 0])
            ->andWhere(['<', 'attempts', 3])
            ->limit(50)
            ->all();

        if (empty($pending)) {
            $this->stdout("No pending records to sync.\n");
            return Controller::EXIT_CODE_NORMAL;
        }

        $sync = new AmoCrmSync();

        foreach ($pending as $item) {
            $this->stdout("Processing item ID: {$item->id}... ", Console::FG_YELLOW);

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
                    $this->stdout("OK\n", Console::FG_GREEN);
                } else {
                    $this->stdout("FAILED (API Return False)\n", Console::FG_RED);
                }
            } catch (\Exception $e) {
                $this->stdout("ERROR: " . $e->getMessage() . "\n", Console::FG_RED);
                Yii::error("CRM Push Error: " . $e->getMessage());
            }

            $item->save(false);
        }
    }

    /**
     * Show queue statistics
     */
    public function actionStatus()
    {
        $stats = [
            'Pending' => CrmPush::find()->where(['status' => 0])->count(),
            'Sent' => CrmPush::find()->where(['status' => 1])->count(),
            'Failed' => CrmPush::find()->where(['status' => 2])->orWhere(['>=', 'attempts', 3])->count(),
        ];

        $this->stdout("CRM Sync Queue Statistics:\n", Console::BOLD);
        foreach ($stats as $label => $count) {
            $this->stdout(" - {$label}: {$count}\n");
        }
    }
}
