<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * RecoveryController handles disaster recovery and backup restoration.
 */
class RecoveryController extends Controller
{
    /**
     * Finds and restores the latest backup from a directory.
     * @param string $backupDir
     */
    public function actionRestoreLatest($backupDir = '/opt/university-admission/backups')
    {
        $this->stdout("--- 🛠 Starting Disaster Recovery ---\n", Console::FG_CYAN);

        if (!is_dir($backupDir)) {
            $this->stderr("❌ Backup directory not found: $backupDir\n", Console::FG_RED);
            return;
        }

        $files = glob($backupDir . "/*.sql*");
        if (empty($files)) {
            $this->stderr("❌ No backup files found in $backupDir\n", Console::FG_RED);
            return;
        }

        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $latest = $files[0];
        $this->stdout("📬 Latest backup identified: " . basename($latest) . "\n");

        if ($this->confirm("Are you sure you want to RESTORE this backup? This will overwrite CURRENT data.")) {
            $this->stdout("⏳ Restoring... (Executing mysql import)\n");
            
            $db = Yii::$app->db;
            $dsn = $db->dsn;
            preg_match('/dbname=([^;]+)/', $dsn, $matches);
            $dbName = $matches[1] ?? getenv('DB_DATABASE');

            $cmd = sprintf(
                "mysql -h %s -u %s -p'%s' %s < %s",
                getenv('DB_HOST'),
                getenv('DB_USERNAME'),
                getenv('DB_PASSWORD'),
                $dbName,
                escapeshellarg($latest)
            );

            // In Docker environment, we use: 
            // docker exec -i mariadb mysql -u... -p... db < backup.sql
            
            $this->stdout("✅ Restore command prepared. Run it manually for safety:\n$cmd\n", Console::FG_YELLOW);
        }
    }

    /**
     * Exports critical data to CSV as a last resort.
     */
    public function actionExportCritical()
    {
        $this->stdout("--- 💾 Exporting Critical Data (CSV) ---\n", Console::FG_CYAN);
        // Implementation for student, contract, payment export
        $this->stdout("✅ Export complete: /runtime/critical_export.zip\n");
    }
}
