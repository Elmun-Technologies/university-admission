<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Consulting */

$this->title = 'Yangi Konsalting Qo\'shish';
$this->params['breadcrumbs'][] = ['label' => 'Konsalting Tashkilotlari', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="consulting-create">

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mt-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-briefcase me-2"></i>
                        <?= Html::encode($this->title) ?>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <?= $this->render('_form', [
                        'model' => $model,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

</div>