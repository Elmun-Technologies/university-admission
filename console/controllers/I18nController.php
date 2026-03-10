<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\FileHelper;

/**
 * I18nController for auditing translations.
 */
class I18nController extends Controller
{
    /**
     * Scans the codebase for Yii::t('app', ...) calls and compares with message files.
     */
    public function actionAudit()
    {
        $this->stdout("Starting Localization Audit...\n", Console::FG_CYAN);

        $directories = [
            Yii::getAlias('@frontend'),
            Yii::getAlias('@backend'),
            Yii::getAlias('@common'),
            Yii::getAlias('@api'),
        ];

        $allKeys = [];
        $pattern = "/Yii::t\s*\(\s*['\"]app['\"]\s*,\s*['\"](.+?)['\"]\s*[,\)]/u";

        foreach ($directories as $dir) {
            $this->stdout("Scanning directory: {$dir} ... ", Console::FG_GREY);
            if (!is_dir($dir)) {
                $this->stdout("NOT FOUND\n", Console::FG_RED);
                continue;
            }

            try {
                $files = FileHelper::findFiles($dir, [
                    'only' => ['*.php'],
                    'except' => ['/messages/', '/vendor/', '/runtime/', '/web/assets/'],
                ]);
                $this->stdout("Found " . count($files) . " files.\n", Console::FG_GREY);

                foreach ($files as $file) {
                    $content = file_get_contents($file);
                    if (preg_match_all($pattern, $content, $matches)) {
                        foreach ($matches[1] as $key) {
                            $allKeys[$key] = true;
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->stdout("ERROR: " . $e->getMessage() . "\n", Console::FG_RED);
            }
        }

        $allKeys = array_keys($allKeys);
        sort($allKeys);

        $this->stdout("Found " . count($allKeys) . " unique translation keys.\n\n", Console::FG_YELLOW);

        $languages = ['uz', 'ru'];
        foreach ($languages as $lang) {
            $this->stdout("Checking language: " . strtoupper($lang) . "\n", Console::BOLD);
            $filePath = Yii::getAlias("@frontend/messages/{$lang}/app.php");

            if (!file_exists($filePath)) {
                $this->stdout("  [ERROR] Translation file not found: {$filePath}\n", Console::FG_RED);
                continue;
            }

            $currentTranslations = include($filePath);
            $missing = [];

            foreach ($allKeys as $key) {
                if (!isset($currentTranslations[$key]) || $currentTranslations[$key] === $key && $lang === 'ru') {
                    $missing[] = $key;
                }
            }

            if (empty($missing)) {
                $this->stdout("  [SUCCESS] 100% Coverage achieved!\n", Console::FG_GREEN);
            } else {
                $this->stdout("  [MISSING] " . count($missing) . " keys found:\n", Console::FG_RED);
                foreach ($missing as $mKey) {
                    $this->stdout("    - {$mKey}\n");
                }
                $this->stdout("\n");
            }
        }

        return self::EXIT_CODE_NORMAL;
    }
}
