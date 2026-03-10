<?php

namespace tests\factories;

use common\models\Direction;
use common\components\BranchScope;

class DirectionFactory
{
    public static function create($overrides = [])
    {
        $branchId = BranchScope::getBranchId() ?: 1;
        $model = new Direction();
        $model->setAttributes(array_merge([
            'branch_id' => $branchId,
            'name_uz' => 'Test Direction ' . uniqid(),
            'status' => Direction::STATUS_ACTIVE,
        ], $overrides));

        if ($model->save()) {
            return $model;
        }
        throw new \Exception("Failed to save direction: " . json_encode($model->errors));
    }
}
