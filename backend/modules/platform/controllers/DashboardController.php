<?php

namespace backend\modules\platform\controllers;

use Yii;
use yii\web\Controller;

/**
 * Default controller for the `platform` module
 */
class DashboardController extends Controller
{
    /**
     * Renders the platform dashboard view for the module
     * @return string
     */
    public function actionIndex()
    {
        // Mock data for university instances
        // In production, this would fetch from a 'university' table in a central DB
        $universities = [
            [
                'id' => 1,
                'name' => 'Tashkent University',
                'domain' => 'tashkent.university-admission.uz',
                'student_count' => 1250,
                'status' => 'Active',
                'last_activity' => '2 minutes ago',
            ],
            [
                'id' => 2,
                'name' => 'Samarkand State',
                'domain' => 'samarkand.university-admission.uz',
                'student_count' => 840,
                'status' => 'Active',
                'last_activity' => '15 minutes ago',
            ],
        ];

        return $this->render('index', [
            'universities' => $universities,
            'totalStudents' => 2090,
            'activeUniversities' => 2,
        ]);
    }
}
