<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;

/**
 * ReportController structures specialized deep dives into datasets.
 */
class ReportController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        // Bound specifically to users possessing the viewReport role logic
                        'roles' => ['viewReport', 'superAdmin'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Primary visual analytics
     */
    public function actionDashboard()
    {
        // Fetch complex aggregates dynamically
        $db = Yii::$app->db;

        // Example: Directions pie chart
        $directionPie = $db->createCommand("
            SELECT d.name_uz, COUNT(s.id) as cnt 
            FROM student s
            JOIN direction d ON s.direction_id = d.id
            GROUP BY d.id
            ORDER BY cnt DESC
            LIMIT 5
        ")->queryAll();

        // Example: Consulting rankings
        $consultingStats = $db->createCommand("
            SELECT c.name, COUNT(cs.id) as total_students, SUM(cs.commission_amount) as total_commission
            FROM consulting_student cs
            JOIN consulting c ON cs.consulting_id = c.id
            GROUP BY c.id
            ORDER BY total_students DESC
            LIMIT 5
        ")->queryAll();

        return $this->render('dashboard', [
            'directionPie' => $directionPie,
            'consultingStats' => $consultingStats
        ]);
    }

    public function actionStudents()
    {
        return $this->render('students');
    }
    public function actionExams()
    {
        return $this->render('exams');
    }
    public function actionPayments()
    {
        return $this->render('payments');
    }
    public function actionConsulting()
    {
        return $this->render('consulting');
    }
}
