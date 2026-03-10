<?php

namespace common\validators;

use yii\validators\Validator;
use Yii;

/**
 * UzbekPhoneValidator ensures +998XXXXXXXXX format
 */
class UzbekPhoneValidator extends Validator
{
    public $pattern = '/^\+998\d{9}$/';
    public $message = 'Telefon raqam formati +998XXXXXXXXX bo\'lishi kerak.';

    public function validateValue($value)
    {
        if (!preg_match($this->pattern, $value)) {
            return [$this->message, []];
        }
        return null;
    }
}
