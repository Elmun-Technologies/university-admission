<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;

/**
 * HealthController provides system monitoring endpoint
 */
class HealthController extends Controller
{
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $health = [
            'status' => 'ok',
            'timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
            'checks' => []
        ];

        // 1. Database Check
        try {
            $start = microtime(true);
            Yii::$app->db->createCommand("SELECT 1")->execute();
            $health['checks']['database'] = ['status' => 'ok', 'response_ms' => round((microtime(true) - $start) * 1000)];
        } catch (\Exception $e) {
            $health['status'] = 'error';
            $health['checks']['database'] = ['status' => 'error', 'message' => 'DB Connection Failed'];
        }

        // 2. Queue Worker Check
        try {
            $pending = (new \yii\db\Query())->from('{{%queue}}')->where(['done_at' => null])->count();
            $health['checks']['queue_worker'] = ['status' => 'ok', 'pending_jobs' => $pending];
        } catch (\Exception $e) {
            $health['checks']['queue_worker'] = ['status' => 'warning', 'message' => 'Queue Table Missing'];
        }

        // 3. Disk Space Check
        $free = disk_free_space("/") / (1024 * 1024 * 1024);
        $health['checks']['disk_space'] = ['status' => $free > 1 ? 'ok' : 'warning', 'free_gb' => round($free, 2)];

        // 4. Backup Check
        $logPath = Yii::getAlias('@common/runtime/backup.log');
        if (file_exists($logPath)) {
            $lastLine = trim(exec("tail -n 1 " . escapeshellarg($logPath)));
            $health['checks']['last_backup'] = ['status' => 'ok', 'info' => $lastLine];
        } else {
            $health['checks']['last_backup'] = ['status' => 'warning', 'message' => 'No backup log found'];
        }

        if ($health['status'] !== 'ok') {
            Yii::$app->response->statusCode = 503;
        }

        return $health;
    }
}
