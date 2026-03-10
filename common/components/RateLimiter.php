<?php

namespace common\components;

use Yii;
use yii\base\Component;
use yii\web\HttpException;

/**
 * RateLimiter helps prevent brute force and API abuse
 */
class RateLimiter extends Component
{
    /**
     * Check if user is locked out from login
     * @param string $username
     * @return bool
     */
    public static function checkLoginLockout($username)
    {
        $key = "login_fails_" . md5($username);
        $fails = Yii::$app->cache->get($key) ?: 0;

        if ($fails >= 5) {
            return true;
        }
        return false;
    }

    /**
     * Increment login failures
     */
    public static function addLoginFail($username)
    {
        $key = "login_fails_" . md5($username);
        $fails = Yii::$app->cache->get($key) ?: 0;
        Yii::$app->cache->set($key, $fails + 1, 900); // 15 minute lockout
    }

    /**
     * Clear login failures on success
     */
    public static function clearLoginFails($username)
    {
        Yii::$app->cache->delete("login_fails_" . md5($username));
    }
}
