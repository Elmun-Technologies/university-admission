<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * ConfigController provides configuration validation and smoke tests.
 */
class ConfigController extends Controller
{
    /**
     * Validates the application configuration and environment.
     * @return int
     */
    public function actionValidate()
    {
        $this->stdout("--- 🔍 Validating Configuration ---\n", Console::FG_CYAN);

        $requiredEnv = [
            'DB_HOST', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD',
            'JWT_SECRET', 'COOKIE_VALIDATION_KEY'
        ];

        $errors = 0;

        // 1. Check Env Variables
        foreach ($requiredEnv as $env) {
            if (!getenv($env)) {
                $this->stderr("❌ Missing ENV: $env\n", Console::FG_RED);
                $errors++;
            } else {
                $this->stdout("✅ ENV $env is set\n");
            }
        }

        // 2. Check Database Connection
        try {
            Yii::$app->db->open();
            $this->stdout("✅ Database connection successful\n", Console::FG_GREEN);
        } catch (\Exception $e) {
            $this->stderr("❌ Database connection failed: " . $e->getMessage() . "\n", Console::FG_RED);
            $errors++;
        }

        // 3. Check Writable Directories
        $directories = ['@runtime', '@webroot/assets', '@common/runtime'];
        foreach ($directories as $alias) {
            $path = Yii::getAlias($alias);
            if (!is_writable($path)) {
                $this->stderr("❌ Directory not writable: $path\n", Console::FG_RED);
                $errors++;
            } else {
                $this->stdout("✅ Directory writable: $alias\n");
            }
        }

        if ($errors === 0) {
            $this->stdout("--- 🎉 Validation Passed! ---\n", Console::FG_GREEN);
            return ExitCode::OK;
        } else {
            $this->stderr("--- ❌ Validation Failed ($errors errors) ---\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
}
