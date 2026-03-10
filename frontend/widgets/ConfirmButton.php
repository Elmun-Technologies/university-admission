<?php

namespace frontend\widgets;

use yii\bootstrap5\Widget;
use yii\helpers\Html;

/**
 * ConfirmButton displays a standard action button that triggers a SweetAlert/Native Confirm before executing POST
 */
class ConfirmButton extends Widget
{
    public $text = 'Saqlash';
    public $url;
    public $confirmMessage = 'Haqiqatan ham ishonchingiz komilmi?';
    public $btnClass = 'btn btn-primary';
    public $icon = '';

    public function run()
    {
        $content = $this->icon ? "<i class=\"{$this->icon}\"></i> " . Html::encode($this->text) : Html::encode($this->text);

        return Html::a($content, $this->url, [
            'class' => $this->btnClass,
            'data' => [
                'confirm' => $this->confirmMessage,
                'method' => 'post',
            ],
        ]);
    }
}
