<?php

namespace api\controllers;

use Yii;
use common\models\Student;

/**
 * StudentController returns individual applicant data
 */
class StudentController extends BaseRest
{
    public function actionProfile()
    {
        // For mobile app, the User represents the Student entity usually via a linked ID
        $student = Student::find()->where(['phone' => $this->user->username])->one();

        if (!$student) {
            return ['status' => 'error', 'message' => 'Student profil topilmadi'];
        }

        return [
            'id' => $student->id,
            'full_name' => $student->getFullName(),
            'status' => $student->status,
            'status_label' => $student->getStatusLabel(),
            'direction' => $student->direction->name_uz ?? '-',
            'branch' => $student->branch->name_uz ?? '-',
        ];
    }
}
