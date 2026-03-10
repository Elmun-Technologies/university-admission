<?php

namespace frontend\widgets;

use Yii;
use yii\bootstrap5\Widget;
use yii\helpers\Html;

/**
 * StepProgress renders a visual stepper based on predefined stages
 * mapped closely to Student::STATUS_* progression
 */
class StepProgress extends Widget
{
    /**
     * @var int Active step index (1-based)
     */
    public $currentStep = 1;

    public function run()
    {
        $steps = [
            1 => Yii::t('app', 'Shaxsiy ma\'lumotlar'),
            2 => Yii::t('app', 'Hujjatlar'),
            3 => Yii::t('app', 'Rasm'),
            4 => Yii::t('app', 'Yo\'nalish tanlash'),
        ];

        $html = '<div class="d-flex justify-content-between align-items-center mb-4 position-relative">';
        $html .= '<div class="progress position-absolute" style="top: 50%; left: 0; right: 0; transform: translateY(-50%); height: 4px; z-index: 1;">';

        $percent = (($this->currentStep - 1) / (count($steps) - 1)) * 100;
        $html .= '<div class="progress-bar bg-primary" role="progressbar" style="width: ' . $percent . '%;" aria-valuenow="' . $percent . '" aria-valuemin="0" aria-valuemax="100"></div>';
        $html .= '</div>';

        foreach ($steps as $index => $label) {
            $isCompleted = $index < $this->currentStep;
            $isCurrent = $index == $this->currentStep;

            $bgClass = $isCompleted ? 'bg-success text-white' : ($isCurrent ? 'bg-primary text-white' : 'bg-light text-muted border');
            $icon = $isCompleted ? '<i class="bi bi-check"></i>' : $index;

            $html .= '<div class="text-center position-relative" style="z-index: 2;">';
            $html .= '<div class="rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm ' . $bgClass . '" style="width: 40px; height: 40px; font-weight: bold;">' . $icon . '</div>';
            $html .= '<div class="mt-2 small d-none d-md-block" style="width: 100px; margin-left: -30px; line-height: 1.2;">' . Html::encode($label) . '</div>';
            $html .= '</div>';
        }

        $html .= '</div>';

        return $html;
    }
}
