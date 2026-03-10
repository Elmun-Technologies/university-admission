<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Yo\'nalishlar';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="direction-index">

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-journal-bookmark text-primary me-2"></i>Fakultet va Yo'nalishlar
            </h5>
            <?= Html::a('<i class="bi bi-plus-lg me-1"></i> Yo\'nalish O\'chish', ['create'], ['class' => 'btn btn-primary btn-sm']) ?>
        </div>

        <div class="card-body p-0">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-hover align-middle mb-0'],
                'layout' => "<div class='table-responsive'>{items}</div><div class='card-footer bg-white border-0 d-flex justify-content-between align-items-center'>{summary}{pager}</div>",
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    [
                        'attribute' => 'name_uz',
                        'format' => 'raw',
                        'value' => function ($model) {
                                        return Html::a(Html::encode($model->name_uz), ['update', 'id' => $model->id], ['class' => 'fw-bold text-dark text-decoration-none d-block']) .
                                            Html::tag('small', Html::encode($model->name_ru), ['class' => 'text-muted']);
                                    }
                    ],
                    [
                        'attribute' => 'tuition_fee',
                        'label' => 'Kontrakt',
                        'value' => function ($model) {
                                        return number_format($model->tuition_fee, 0, '', ' ') . ' UZS';
                                    }
                    ],
                    [
                        'attribute' => 'duration_years',
                        'label' => 'Davomiyligi',
                        'value' => function ($model) {
                                        return $model->duration_years . ' yil';
                                    }
                    ],
                    [
                        'label' => 'Ta\'lim shakllari',
                        'format' => 'raw',
                        'value' => function ($model) {
                                        $str = '';
                                        foreach ($model->eduForms as $form) {
                                            $str .= '<span class="badge bg-light text-dark border me-1">' . $form->name_uz . '</span>';
                                        }
                                        return $str ?: '<span class="text-muted small">Kiritilmagan</span>';
                                    }
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => function ($model) {
                                        $checked = $model->status === 1 ? 'checked' : '';
                                        return '<div class="form-check form-switch ms-3">
                                      <input class="form-check-input status-toggle" type="checkbox" role="switch" data-id="' . $model->id . '" ' . $checked . ' style="cursor:pointer; transform: scale(1.2);">
                                    </div>';
                                    }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{update} {delete}',
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                            return Html::a('<i class="bi bi-pencil"></i>', $url, ['class' => 'btn btn-sm btn-light border me-1 text-primary']);
                                        },
                            'delete' => function ($url, $model, $key) {
                                            return Html::a('<i class="bi bi-trash"></i>', $url, [
                                                'class' => 'btn btn-sm btn-light border text-danger',
                                                'data' => ['confirm' => 'Haqiqatan ham o\'chirasizmi?', 'method' => 'post']
                                            ]);
                                        },
                        ]
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>

<?php
$js = <<<JS
$('.status-toggle').on('change', function() {
    let id = $(this).data('id');
    let csrfParam = $('meta[name="csrf-param"]').attr('content');
    let csrfToken = $('meta[name="csrf-token"]').attr('content');
    let payload = {};
    payload[csrfParam] = csrfToken;
    
    $.post('/direction/toggle-status?id=' + id, payload)
        .fail(function() {
            alert('Xatolik yuz berdi!');
            location.reload(); // Revert safely physically
        });
});
JS;
$this->registerJs($js);
?>