<?php

namespace api\components;

use Yii;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use common\models\User;

/**
 * JwtAuth handles token generation and validation
 */
class JwtAuth
{
    private static function getSecret()
    {
        return getenv('JWT_SECRET') ?: 'uni_admission_secret_key_2026';
    }

    public static function generateToken($userId)
    {
        $payload = [
            'iss' => 'university-admission',
            'iat' => time(),
            'exp' => time() + (3600 * 24), // 24 hours
            'sub' => $userId,
        ];

        return JWT::encode($payload, self::getSecret(), 'HS256');
    }

    public static function validateToken($token)
    {
        try {
            $decoded = JWT::decode($token, new Key(self::getSecret(), 'HS256'));
            return User::findOne($decoded->sub);
        } catch (\Exception $e) {
            return null;
        }
    }
}
