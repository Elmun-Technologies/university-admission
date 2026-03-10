<?php

namespace frontend\widgets;

use Yii;
use yii\bootstrap5\Widget;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Renders twin clickable flag buttons defining active user language
 */
class LanguageSwitcher extends Widget
{
    public function run()
    {
        $currentLang = Yii::$app->language;
        $urlParams = Yii::$app->request->get();

        // Build links maintaining current route
        $urlUz = array_merge([''], $urlParams, ['lang' => 'uz']);
        $urlRu = array_merge([''], $urlParams, ['lang' => 'ru']);

        $btnUz = Html::a(
            '🇺🇿 UZ',
            $urlUz,
            ['class' => 'btn btn-sm ' . ($currentLang === 'uz' ? 'btn-primary' : 'btn-outline-secondary')]
        );

        $btnRu = Html::a(
            '🇷🇺 RU',
            $urlRu,
            ['class' => 'btn btn-sm ' . ($currentLang === 'ru' ? 'btn-primary' : 'btn-outline-secondary')]
        );

        return Html::tag('div', $btnUz . "\n" . $btnRu, ['class' => 'btn-group', 'role' => 'group']);
    }
}
