<?php

namespace common\components;

/**
 * BranchScope stores the current branch context for the request
 */
class BranchScope
{
    private static $_branchId;
    private static $_bypass = false;

    public static function setBranchId($id)
    {
        self::$_branchId = $id;
    }

    public static function getBranchId()
    {
        return self::$_branchId;
    }

    public static function setBypassMode($status)
    {
        self::$_bypass = $status;
    }

    public static function isBypassed()
    {
        return self::$_bypass;
    }
}
