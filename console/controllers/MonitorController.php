<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use common\models\Student;
use common\models\StudentExam;
use common\models\StudentOferta;
use common\models\Branch;

class MonitorController extends Controller
{
    public function actionHealthCheck()
    {
        $errors = [];

        // 1. DB Connection
        try {
            Yii::$app->db->createCommand('SELECT 1')->queryScalar();
        } catch (\Exception $e) {
            $errors[] = "DB Connection Failed: " . $e->getMessage();
        }

        // 2. Queue Worker (Check if process exists or last job processed)
        // For simplicity, check if yii queue/info works
        try {
            Yii::$app->queue->info();
        } catch (\Exception $e) {
            $errors[] = "Queue Component Unresponsive";
        }

        // 3. Disk Space
        $freeSpace = disk_free_space("/") / (1024 * 1024 * 1024);
        if ($freeSpace < 10) {
            $errors[] = "Low Disk Space: " . round($freeSpace, 2) . " GB left";
        }

        if (!empty($errors)) {
            $message = "🚨 *Health Check Alert*\n\n" . implode("\n", $errors);
            Yii::$app->telegram->sendMessage($message);
            $this->stdout($message . "\n", Console::FG_RED);
            return Controller::EXIT_CODE_ERROR;
        }

        $this->stdout("System Health: OK\n", Console::FG_GREEN);
        return Controller::EXIT_CODE_NORMAL;
    }

    public function actionStats()
    {
        $branches = Branch::find()->all();
        $report = "📊 *Daily Stats Summary (" . date('d.m.Y') . ")*\n\n";

        foreach ($branches as $branch) {
            $studentsCount = Student::find()->where(['branch_id' => $branch->id])->count();
            $examsToday = StudentExam::find()
                ->innerJoinWith('student s')
                ->where(['s.branch_id' => $branch->id])
                ->andWhere(['>=', 'student_exam.created_at', strtotime('today')])
                ->count();

            $report .= "🏢 *{$branch->name_uz}*\n";
            $report .= "- Students: $studentsCount\n";
            $report .= "- Exams Today: $examsToday\n\n";
        }

        Yii::$app->telegram->sendMessage($report);
        $this->stdout($report . "\n");
    }
}
