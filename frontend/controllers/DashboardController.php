<?php

namespace frontend\controllers;

use common\models\Student;
use common\models\StudentOferta;
use common\models\StudentExam;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * DashboardController represents the root entry after login.
 * Orchestrates the massive stepping guide UI logic visually.
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
        $user = Yii::$app->user->identity;
        $student = Student::findOne(['created_by' => $user->id]);

        // Failsafe: if somehow student doesn't exist, create an empty one mapped to User
        if (!$student) {
            $student = new Student();
            $student->first_name = $user->first_name;
            $student->last_name = $user->last_name;
            $student->phone = $user->phone;
            $student->branch_id = $user->branch_id;
            $student->status = Student::STATUS_NEW;
            $student->created_by = $user->id;
            $student->save(false);
        }

        // Fetch connected entities logically bounds to display summaries
        $oferta = StudentOferta::findOne(['student_id' => $student->id]);
        $examAttempt = StudentExam::findOne(['student_id' => $student->id]);

        return $this->render('index', [
            'student' => $student,
            'oferta' => $oferta,
            'examAttempt' => $examAttempt,
        ]);
    }
}
