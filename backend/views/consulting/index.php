<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Konsalting Tashkilotlari';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="consulting-index">

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-briefcase text-success me-2"></i>Hamkor Agentliklar</h5>
            <?= Html::a('<i class="bi bi-plus-lg me-1"></i> Yangi qoshish', ['create'], ['class' => 'btn btn-primary btn-sm']) ?>
        </div>

        <div class="card-body p-0">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-hover align-middle mb-0'],
                'layout' => "<div class='table-responsive'>{items}</div><div class='card-footer bg-white border-0 d-flex justify-content-between align-items-center'>{summary}{pager}</div>",
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    [
                        'attribute' => 'name',
                        'format' => 'raw',
                        'value' => function ($model) {
                                        return Html::a('<b>' . Html::encode($model->name) . '</b>', ['update', 'id' => $model->id], ['class' => 'text-decoration-none text-primary d-block']) .
                                            Html::tag('small', 'Tel: ' . Html::encode($model->phone ?? '-'), ['class' => 'text-muted']);
                                    }
                    ],
                    'contact_person',
                    [
                        'label' => 'Jami Talabalar',
                        'format' => 'raw',
                        'value' => function ($model) {
                                        $count = $model->getStudents()->count();
                                        return '<span class="badge bg-light text-dark border">' . $count . ' ta</span>';
                                    }
                    ],
                    [
                        'label' => 'Muvaffaqiyatli',
                        'format' => 'raw',
                        'value' => function ($model) {
                                        $count = clone clone $model->getStudents();
                                        $count = $count->where(['>=', 'status', \common\models\Student::STATUS_CONTRACT_SIGNED])->count();
                                        return '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">' . $count . ' ta</span>';
                                    }
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => function ($model) {
                                        return $model->status == 1 ? '<span class="badge bg-success">Faol</span>' : '<span class="badge bg-secondary">Nofaol</span>';
                                    }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{update} {delete}',
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                            return Html::a('<i class="bi bi-pencil"></i>', $url, ['class' => 'btn btn-sm btn-light border text-primary me-1']);
                                        },
                            'delete' => function ($url, $model, $key) {
                                            return Html::a('<i class="bi bi-trash"></i>', $url, [
                                                'class' => 'btn btn-sm btn-light border text-danger',
                                                'data' => ['confirm' => 'O\'chirmoqchimisiz?', 'method' => 'post']
                                            ]);
                                        },
                        ]
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>