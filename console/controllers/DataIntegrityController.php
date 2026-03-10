<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use common\models\Student;

/**
 * DataIntegrityController handles database anomaly checks and automated cleanup.
 *
 * Usage:
 * php yii data-integrity/check
 * php yii data-integrity/check --fix=1
 */
class DataIntegrityController extends Controller
{
    public $fix = false;

    public function options($actionID)
    {
        return ['fix'];
    }

    /**
     * Scans the database for anomalies and optionally fixes them.
     */
    public function actionCheck()
    {
        $this->stdout("Starting Data Integrity Check...\n", Console::FG_YELLOW);
        $issuesFound = 0;
        $fixed = 0;

        // Rule 1: Missing PINFL for non-new statuses
        $missingPinfl = Student::find()
            ->where(['and', ['status' => Student::STATUS_ANKETA], ['or', ['pinfl' => null], ['pinfl' => '']]])
            ->all();

        if (count($missingPinfl) > 0) {
            $this->stdout("- Found " . count($missingPinfl) . " students in ANKETA status with missing PINFL.\n", Console::FG_RED);
            $issuesFound += count($missingPinfl);

            if ($this->fix) {
                foreach ($missingPinfl as $student) {
                    // Revert to NEW status so they are forced to complete profile
                    $student->status = Student::STATUS_NEW;
                    $student->save(false);
                    $fixed++;
                }
                $this->stdout("  -> Fixed (Reverted to NEW status).\n", Console::FG_GREEN);
            }
        } else {
            $this->stdout("- Rule 1 (PINFL check) Passed.\n", Console::FG_GREEN);
        }

        // Rule 2: Orphaned Students (Branch deleted, though FK should prevent this, we check anyway)
        $sql = "SELECT s.id FROM {{%student}} s LEFT JOIN {{%branch}} b ON s.branch_id = b.id WHERE b.id IS NULL";
        $orphaned = Yii::$app->db->createCommand($sql)->queryAll();

        if (count($orphaned) > 0) {
            $this->stdout("- Found " . count($orphaned) . " orphaned students without valid branches.\n", Console::FG_RED);
            $issuesFound += count($orphaned);

            if ($this->fix) {
                // Hard delete or set to a generic 'lost' status. Hard delete for true orphans if FK failed.
                $ids = array_column($orphaned, 'id');
                Student::deleteAll(['id' => $ids]);
                $fixed += count($orphaned);
                $this->stdout("  -> Fixed (Deleted orphans).\n", Console::FG_GREEN);
            }
        } else {
            $this->stdout("- Rule 2 (Orphan check) Passed.\n", Console::FG_GREEN);
        }

        $this->stdout("\nSummary:\n", Console::FG_CYAN);
        $this->stdout("Issues Found: $issuesFound\n");
        if ($this->fix) {
            $this->stdout("Issues Fixed: $fixed\n", Console::FG_GREEN);
        } elseif ($issuesFound > 0) {
            $this->stdout("Run with --fix=1 to resolve issues.\n", Console::FG_YELLOW);
        }

        return $issuesFound > 0 && !$this->fix ? rtrim(Console::FG_RED) : \yii\console\ExitCode::OK;
    }
}
