<?php

namespace frontend\widgets;

use yii\bootstrap5\InputWidget;
use yii\helpers\Html;

/**
 * Standardizes AA 1234567 visual separation cleanly mapping to native Yii active fields
 */
class PassportInput extends InputWidget
{
    public function run()
    {
        // Extract field names dynamically
        $modelName = $this->model->formName();
        $seriesAttr = 'passport_series';
        $numberAttr = 'passport_number';

        $idSeries = Html::getInputId($this->model, $seriesAttr);
        $idNumber = Html::getInputId($this->model, $numberAttr);
        $nameSeries = Html::getInputName($this->model, $seriesAttr);
        $nameNumber = Html::getInputName($this->model, $numberAttr);

        $valSeries = Html::getAttributeValue($this->model, $seriesAttr);
        $valNumber = Html::getAttributeValue($this->model, $numberAttr);

        $html = '<div class="input-group passport-input-group">';
        $html .= Html::textInput($nameSeries, $valSeries, [
            'id' => $idSeries,
            'class' => 'form-control text-uppercase',
            'maxlength' => 2,
            'placeholder' => 'AA',
            'style' => 'max-width: 80px;'
        ]);
        $html .= Html::textInput($nameNumber, $valNumber, [
            'id' => $idNumber,
            'class' => 'form-control',
            'maxlength' => 7,
            'placeholder' => '1234567'
        ]);
        $html .= '</div>';

        // Auto focus logic natively
        $js = <<<JS
        document.getElementById('{$idSeries}').addEventListener('input', function() {
            this.value = this.value.toUpperCase().replace(/[^A-Z]/g, '');
            if(this.value.length === 2) {
                document.getElementById('{$idNumber}').focus();
            }
        });
        document.getElementById('{$idNumber}').addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
        });
        JS;

        $this->getView()->registerJs($js);

        return $html;
    }
}
