<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * BackupController handles database dumping and offsite storage
 */
class BackupController extends Controller
{
    /**
     * Create a compressed database backup and send to Telegram
     */
    public function actionCreate()
    {
        $db = Yii::$app->db;
        $dsnParts = $this->parseDsn($db->dsn);
        $host = $dsnParts['host'] ?? 'localhost';
        $dbName = $dsnParts['dbname'] ?? '';

        $filename = "backup_{$dbName}_" . date('Y-m-d_H-i-s') . ".sql";
        $filePath = Yii::getAlias("@runtime/{$filename}");
        $gzPath = "{$filePath}.gz";

        $this->stdout("Starting backup of {$dbName}...\n", Console::FG_YELLOW);

        // Run mysqldump
        $command = sprintf(
            'mysqldump -h%s -u%s -p%s %s > %s',
            escapeshellarg($host),
            escapeshellarg($db->username),
            escapeshellarg($db->password),
            escapeshellarg($dbName),
            escapeshellarg($filePath)
        );

        system($command, $returnVar);

        if ($returnVar !== 0) {
            $this->stderr("Error: mysqldump failed.\n", Console::FG_RED);
            return Controller::EXIT_CODE_ERROR;
        }

        // Compress
        system("gzip -f " . escapeshellarg($filePath));

        $md5 = md5_file($gzPath);
        $size = round(filesize($gzPath) / 1024 / 1024, 2);

        $this->stdout("Backup created: {$filename}.gz ({$size} MB, MD5: {$md5})\n", Console::FG_GREEN);

        // Trigger Telegram send via queue for resilience
        $branch = \common\models\Branch::find()->one();
        Yii::$app->queue->push(new \common\jobs\SendBackupToTelegramJob([
            'filePath' => $gzPath,
            'branchId' => $branch->id
        ]));

        $this->stdout("Backup queued for Telegram upload.\n", Console::FG_CYAN);

        $logMsg = date('[Y-m-d H:i:s]') . " Backup success: {$filename}.gz, Size: {$size}MB, MD5: {$md5}\n";
        file_put_contents(Yii::getAlias('@common/runtime/backup.log'), $logMsg, FILE_APPEND);

        return Controller::EXIT_CODE_NORMAL;
    }

    /**
     * Restore database from a given file
     * @param string $filePath
     */
    public function actionRestore($filePath)
    {
        if (!file_exists($filePath)) {
            $this->stderr("Error: File not found.\n", Console::FG_RED);
            return Controller::EXIT_CODE_ERROR;
        }

        if (!$this->confirm("WARNING: Current data will be OVERWRITTEN. Continue?")) {
            return Controller::EXIT_CODE_NORMAL;
        }

        $db = Yii::$app->db;
        $dsnParts = $this->parseDsn($db->dsn);
        $host = $dsnParts['host'] ?? 'localhost';
        $dbName = $dsnParts['dbname'] ?? '';

        $this->stdout("Restoring database from {$filePath}...\n", Console::FG_YELLOW);

        $path = $filePath;
        if (substr($filePath, -3) === '.gz') {
            system("gunzip -c " . escapeshellarg($filePath) . " > " . escapeshellarg(substr($filePath, 0, -3)));
            $path = substr($filePath, 0, -3);
        }

        $command = sprintf(
            'mysql -h%s -u%s -p%s %s < %s',
            escapeshellarg($host),
            escapeshellarg($db->username),
            escapeshellarg($db->password),
            escapeshellarg($dbName),
            escapeshellarg($path)
        );

        system($command, $returnVar);

        if ($returnVar === 0) {
            $this->stdout("Database successfully restored.\n", Console::FG_GREEN);
        } else {
            $this->stderr("Error: Restoration failed.\n", Console::FG_RED);
        }

        return $returnVar === 0 ? Controller::EXIT_CODE_NORMAL : Controller::EXIT_CODE_ERROR;
    }

    protected function parseDsn($dsn)
    {
        $parts = explode(':', $dsn);
        $params = explode(';', $parts[1]);
        $result = [];
        foreach ($params as $param) {
            $kv = explode('=', $param);
            if (isset($kv[1])) {
                $result[$kv[0]] = $kv[1];
            }
        }
        return $result;
    }
}
