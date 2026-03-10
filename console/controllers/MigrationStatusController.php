<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * MigrationStatusController provides enhanced migration tracking.
 */
class MigrationStatusController extends Controller
{
    /**
     * Shows the status of all migrations.
     */
    public function actionIndex()
    {
        $this->stdout("--- 📜 Migration Status ---\n", Console::FG_CYAN);
        
        $migrations = (new \yii\db\Query())
            ->from('{{%migration}}')
            ->orderBy(['apply_time' => SORT_DESC])
            ->limit(20)
            ->all();

        foreach ($migrations as $m) {
            $date = date('Y-m-d H:i:s', $m['apply_time']);
            $this->stdout("[$date] {$m['version']}\n");
        }
        
        $this->stdout("\nRun 'php yii migrate/history' for full history.\n");
    }

    /**
     * Shows pending migrations.
     */
    public function actionPending()
    {
        $this->stdout("--- ⏳ Pending Migrations ---\n", Console::FG_CYAN);
        passthru('php yii migrate/new');
    }
}
