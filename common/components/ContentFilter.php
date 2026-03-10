<?php

namespace common\components;

use Yii;
use yii\base\Component;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

/**
 * ContentFilter provides global sanitization for user-supplied data
 */
class ContentFilter extends Component
{
    /**
     * Deeply sanitizes string or array to prevent XSS
     */
    public static function sanitize($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::sanitize($value);
            }
            return $data;
        }

        if (is_string($data)) {
            // Strip harmful tags while keeping basic formatting if needed
            return HtmlPurifier::process($data);
        }

        return $data;
    }

    /**
     * Stricter version for plain text fields
     */
    public static function cleanText($data)
    {
        if (is_string($data)) {
            return Html::encode(strip_tags($data));
        }
        return $data;
    }
}
