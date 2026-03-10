<?php

namespace common\components;

use Yii;
use yii\base\Component;
use common\models\Branch;
use common\models\Direction;

/**
 * CacheManager provides centralized cache access and invalidation logic
 */
class CacheManager extends Component
{
    /**
     * Get branch settings with 1 hour cache
     */
    public static function getBranchData($branchId)
    {
        return Yii::$app->cache->getOrSet("branch_data_{$branchId}", function () use ($branchId) {
            return Branch::find()->where(['id' => $branchId])->asArray()->one();
        }, 3600);
    }

    /**
     * Get direction list for a branch with 30 min cache
     */
    public static function getDirectionList($branchId)
    {
        return Yii::$app->cache->getOrSet("direction_list_{$branchId}", function () use ($branchId) {
            return Direction::find()->where(['branch_id' => $branchId])->asArray()->all();
        }, 1800);
    }

    /**
     * Invalidate directions cache for a branch
     */
    public static function invalidateDirections($branchId)
    {
        Yii::$app->cache->delete("direction_list_{$branchId}");
    }

    /**
     * Invalidate branch data
     */
    public static function invalidateBranch($branchId)
    {
        Yii::$app->cache->delete("branch_data_{$branchId}");
    }
}
