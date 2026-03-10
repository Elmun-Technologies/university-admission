<?php

namespace tests\factories;

use common\models\Exam;
use common\components\BranchScope;

class ExamFactory
{
    public static function create($overrides = [])
    {
        $branchId = BranchScope::getBranchId() ?: 1;
        $model = new Exam();
        $model->setAttributes(array_merge([
            'branch_id' => $branchId,
            'name_uz' => 'Admin Entrance Exam ' . date('Y'),
            'status' => Exam::STATUS_ACTIVE,
            'duration_minutes' => 60,
        ], $overrides));

        if ($model->save()) {
            return $model;
        }
        throw new \Exception("Failed to save exam: " . json_encode($model->errors));
    }
}
