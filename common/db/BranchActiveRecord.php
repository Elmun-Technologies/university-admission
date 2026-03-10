<?php

namespace common\db;

use Yii;
use yii\db\ActiveRecord;
use common\components\BranchScope;

/**
 * BranchActiveRecord automatically enforces multi-tenancy isolation
 */
class BranchActiveRecord extends ActiveRecord
{
    /**
     * Automatically scope all queries by branch_id unless bypassed
     */
    public static function find()
    {
        $query = parent::find();

        if (!BranchScope::isBypassed() && ($branchId = BranchScope::getBranchId())) {
            $table = static::tableName();
            $schema = static::getTableSchema();
            if ($schema && isset($schema->columns['branch_id'])) {
                $query->andWhere([$table . '.branch_id' => $branchId]);
            }
        }

        return $query;
    }

    /**
     * Automatically set branch_id before saving new records
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert && ($branchId = BranchScope::getBranchId())) {
                if ($this->hasAttribute('branch_id') && empty($this->branch_id)) {
                    $this->branch_id = $branchId;
                }
            }
            return true;
        }
        return false;
    }
}
