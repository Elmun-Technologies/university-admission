<?php

namespace frontend\widgets;

use yii\bootstrap5\Widget;
use yii\helpers\Html;

/**
 * Common layout helper for displaying standardized Bootstrap Badges for Student Statuses
 */
class StatusBadge extends Widget
{
    public $status;
    public $label;

    public function run()
    {
        $class = 'bg-secondary';

        switch ($this->status) {
            case \common\models\Student::STATUS_NEW:
                $class = 'bg-light text-dark border';
                break;
            case \common\models\Student::STATUS_ANKETA:
                $class = 'bg-info text-dark';
                break;
            case \common\models\Student::STATUS_EXAM_SCHEDULED:
            case \common\models\Student::STATUS_CONTRACT_SIGNED:
                $class = 'bg-primary';
                break;
            case \common\models\Student::STATUS_EXAM_PASSED:
            case \common\models\Student::STATUS_PAID:
                $class = 'bg-success';
                break;
            case \common\models\Student::STATUS_EXAM_FAILED:
            case \common\models\Student::STATUS_REJECTED:
                $class = 'bg-danger';
                break;
        }

        return Html::tag('span', Html::encode($this->label), [
            'class' => "badge $class shadow-sm px-2 py-1"
        ]);
    }
}
