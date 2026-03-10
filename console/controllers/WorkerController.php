<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * WorkerController provides queue statistics and management commands
 */
class WorkerController extends Controller
{
    /**
     * Start the queue worker (simplified wrapper for built-in queue/listen)
     * This is useful for Docker entrypoints.
     */
    public function actionStart()
    {
        $this->stdout("Starting queue worker...\n", Console::FG_GREEN);
        return Yii::$app->runAction('queue/listen', ['verbose' => true]);
    }

    /**
     * Show queue statistics (pending/done/failed)
     */
    public function actionStatus()
    {
        $tableName = Yii::$app->queue->tableName;

        $waiting = (new \yii\db\Query())
            ->from($tableName)
            ->where(['pushed_at' => null]) // Technically wait is reserved_at null and done_at null
            ->count();

        // Standard yii2-queue DB structure check
        $total = (new \yii\db\Query())->from($tableName)->count();
        $done = (new \yii\db\Query())->from($tableName)->where(['not', ['done_at' => null]])->count();
        $pending = (new \yii\db\Query())->from($tableName)->where(['done_at' => null])->count();

        $this->stdout("Queue Status:\n", Console::BOLD);
        $this->stdout(" - Total jobs: " . $total . "\n");
        $this->stdout(" - Done jobs: " . $done . "\n", Console::FG_CYAN);
        $this->stdout(" - Pending: " . $pending . "\n", Console::FG_YELLOW);
    }
}
