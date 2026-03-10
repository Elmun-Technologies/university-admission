<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use common\models\Student;
use common\models\Branch;

/**
 * StatsController pre-computes heavy analytics for dashboard
 */
class StatsController extends Controller
{
    /**
     * Compute all dashboard stats for all branches
     */
    public function actionCompute()
    {
        $branches = Branch::find()->all();
        foreach ($branches as $branch) {
            $this->stdout("Computing stats for branch: {$branch->name_uz}...\n", Console::FG_YELLOW);

            $this->computeStudentStats($branch->id);
            // Add more aggregations as needed
        }

        $this->stdout("Done.\n", Console::FG_GREEN);
    }

    protected function computeStudentStats($branchId)
    {
        $distribution = Student::find()
            ->select(['status', 'COUNT(*) as cnt'])
            ->where(['branch_id' => $branchId])
            ->groupBy('status')
            ->asArray()
            ->all();

        $data = [];
        foreach ($distribution as $row) {
            $data[$row['status']] = (int) $row['cnt'];
        }

        $this->saveToCache($branchId, 'students_by_status', $data);
    }

    protected function saveToCache($branchId, $slug, $data)
    {
        $exists = (new \yii\db\Query())
            ->from('{{%stats_cache}}')
            ->where(['branch_id' => $branchId, 'slug' => $slug])
            ->one();

        if ($exists) {
            Yii::$app->db->createCommand()->update('{{%stats_cache}}', [
                'data_json' => json_encode($data),
                'computed_at' => time()
            ], ['id' => $exists['id']])->execute();
        } else {
            Yii::$app->db->createCommand()->insert('{{%stats_cache}}', [
                'branch_id' => $branchId,
                'slug' => $slug,
                'data_json' => json_encode($data),
                'computed_at' => time()
            ])->execute();
        }
    }

    /**
     * Run ANALYZE TABLE on all tables to update optics stats
     */
    public function actionOptimize()
    {
        $dbName = Yii::$app->db->createCommand("SELECT DATABASE()")->queryScalar();
        $tables = Yii::$app->db->getSchema()->getTableNames();

        foreach ($tables as $table) {
            $this->stdout("Analyzing table: {$table}... ");
            Yii::$app->db->createCommand("ANALYZE TABLE `{$table}`")->execute();
            $this->stdout("OK\n", Console::FG_GREEN);
        }
    }
}
