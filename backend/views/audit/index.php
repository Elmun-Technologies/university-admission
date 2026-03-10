<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Audit Log';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="audit-index">
    <h1>
        <?= Html::encode($this->title) ?>
    </h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'created_at',
                'format' => ['datetime', 'php:Y-m-d H:i:s'],
                'label' => 'Vaqti',
            ],
            [
                'attribute' => 'action',
                'label' => 'Amal',
                'contentOptions' => ['style' => 'font-weight:bold;'],
            ],
            [
                'attribute' => 'entity_type',
                'label' => 'Turi',
            ],
            [
                'attribute' => 'entity_id',
                'label' => 'ID',
            ],
            [
                'attribute' => 'old_value',
                'label' => 'Eski qiymat',
                'value' => function ($model) {
                        return $model['old_value'];
                    }
            ],
            [
                'attribute' => 'new_value',
                'label' => 'Yangi qiymat',
                'value' => function ($model) {
                        return $model['new_value'];
                    }
            ],
            [
                'attribute' => 'ip_address',
                'label' => 'IP',
            ],
            [
                'attribute' => 'user_id',
                'label' => 'Foydalanuvchi',
                'value' => function ($model) {
                        return $model['user_id'] ?: 'System';
                    }
            ]
        ],
    ]); ?>
</div>