<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use common\models\Student;
use common\models\StudentExam;
use common\models\StudentOferta;

/**
 * DashboardController aggregates real-time metrics for the admin staff securely.
 */
class DashboardController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $cache = Yii::$app->cache;

        // Cache heavy analytics specifically for 5 minutes organically
        $metrics = $cache->getOrSet('dashboard_metrics_v1', function () {

            $todayStart = strtotime('today midnight');
            $yesterdayStart = strtotime('yesterday midnight');

            // 1. Core KPIs
            $newToday = Student::find()->where(['>=', 'created_at', $todayStart])->count();
            $newYesterday = Student::find()->where(['>=', 'created_at', $yesterdayStart])->andWhere(['<', 'created_at', $todayStart])->count();

            $examsToday = StudentExam::find()
                ->innerJoinWith('examDate')
                ->where(['exam_date.exam_date' => date('Y-m-d')])
                ->count();

            $contractsToday = StudentOferta::find()->where(['>=', 'signed_at', $todayStart])->count();
            $paymentsToday = StudentOferta::find()
                ->where(['payment_status' => StudentOferta::PAYMENT_PAID])
                ->andWhere(['>=', 'payment_date', date('Y-m-d 00:00:00')])
                ->sum('payment_amount') ?: 0;

            // 2. Weekly Trend (Last 7 days strictly by grouping date)
            $trendData = [];
            $labels = [];
            for ($i = 6; $i >= 0; $i--) {
                $dayStr = date('Y-m-d', strtotime("-$i days"));
                $labels[] = date('d M', strtotime($dayStr));
                $dayStart = strtotime($dayStr . ' midnight');
                $dayEnd = $dayStart + 86399;

                $val = Student::find()->where(['between', 'created_at', $dayStart, $dayEnd])->count();
                $trendData[] = $val;
            }

            // 3. Status Funnel
            $statuses = [
                Student::STATUS_NEW => 0,
                Student::STATUS_ANKETA => 0,
                Student::STATUS_EXAM_SCHEDULED => 0,
                Student::STATUS_EXAM_PASSED => 0,
                Student::STATUS_CONTRACT_SIGNED => 0,
                Student::STATUS_PAID => 0,
            ];

            // Native grouped query avoiding N+1
            $distribution = Student::find()
                ->select(['status', 'COUNT(*) as cnt'])
                ->groupBy('status')
                ->asArray()
                ->all();

            foreach ($distribution as $row) {
                if (isset($statuses[$row['status']])) {
                    $statuses[$row['status']] = (int) $row['cnt'];
                }
            }

            // 4. Upcoming Exams
            $upcomingExams = \common\models\ExamDate::find()
                ->with('exam.direction')
                ->where(['>=', 'exam_date', date('Y-m-d')])
                ->orderBy(['exam_date' => SORT_ASC, 'start_time' => SORT_ASC])
                ->limit(5)
                ->all();

            // 5. Recent timeline activity logically mapping JSON if we implemented it, or simple ordering
            $recentStudents = Student::find()
                ->orderBy(['updated_at' => SORT_DESC])
                ->limit(10)
                ->all();

            return [
                'today' => [
                    'new' => $newToday,
                    'new_diff' => $newYesterday > 0 ? (($newToday - $newYesterday) / $newYesterday) * 100 : 100,
                    'exams' => $examsToday,
                    'contracts' => $contractsToday,
                    'payments' => $paymentsToday
                ],
                'trendData' => $trendData,
                'trendLabels' => $labels,
                'funnel' => array_values($statuses),
                'upcomingExams' => $upcomingExams,
                'recentActivities' => $recentStudents
            ];
        }, 300); // 5 min

        return $this->render('index', [
            'metrics' => $metrics
        ]);
    }
}
