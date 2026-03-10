<?php

namespace frontend\components;

use Yii;
use yii\base\ActionFilter;

/**
 * LanguageFilter captures the language from cookie or query param
 * and securely sets Yii::$app->language globally before actions execute.
 */
class LanguageFilter extends ActionFilter
{
    public function beforeAction($action)
    {
        // 1. Check query parameter manually triggered by switcher widget
        $lang = Yii::$app->request->get('lang');

        // 2. Fallback to cookie
        if (!$lang) {
            $lang = Yii::$app->request->cookies->getValue('lang');
        }

        // 3. Fallback to default
        if (!$lang) {
            $lang = 'uz'; // strict default
        }

        // 4. Validate securely
        if (in_array($lang, ['uz', 'ru'])) {
            Yii::$app->language = $lang;

            // Re-store in cookie for 30 days securely
            $cookie = new \yii\web\Cookie([
                'name' => 'lang',
                'value' => $lang,
                'expire' => time() + 86400 * 30,
            ]);
            Yii::$app->response->cookies->add($cookie);
        } else {
            Yii::$app->language = 'uz'; // ultimate safe fallback
        }

        return parent::beforeAction($action);
    }
}
