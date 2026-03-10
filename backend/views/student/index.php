<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use common\models\Student;

/* @var $this yii\web\View */
/* @var $searchModel common\models\StudentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Abiturientlar';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-index">

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-people text-primary me-2"></i>Abiturientlar Boshqaruvi</h5>
            <div>
                <?= Html::a(
                    '<i class="bi bi-file-earmark-excel me-1"></i> Export (Excel)',
                    array_merge(['export'], Yii::$app->request->queryParams),
                    ['class' => 'btn btn-outline-success btn-sm me-2', 'data-pjax' => 0]
                ) ?>
                <?= Html::a('<i class="bi bi-plus-lg"></i> Yangi qoshish', ['create'], ['class' => 'btn btn-primary btn-sm']) ?>
            </div>
        </div>

        <div class="card-body p-0">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'tableOptions' => ['class' => 'table table-hover align-middle mb-0'],
                'layout' => "<div class='table-responsive'>{items}</div><div class='card-footer bg-white border-0 d-flex justify-content-between align-items-center'>{summary}{pager}</div>",
                'pager' => [
                    'class' => \yii\bootstrap5\LinkPager::class,
                    'options' => ['class' => 'pagination mb-0']
                ],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    [
                        'attribute' => 'photo',
                        'format' => 'raw',
                        'filter' => false,
                        'value' => function ($model) {
                        $url = $model->photo ? Yii::getAlias('@web/uploads/photos/') . $model->photo : 'https://ui-avatars.com/api/?name=' . urlencode($model->getFullName());
                        return Html::img($url, ['class' => 'rounded-circle shadow-sm', 'style' => 'width: 40px; height: 40px; object-fit: cover;']);
                    }
                    ],
                    [
                        'attribute' => 'first_name',
                        'label' => 'F.I.SH',
                        'format' => 'raw',
                        'value' => function ($model) {
                        return Html::a(Html::encode($model->getFullName()), ['view', 'id' => $model->id], ['class' => 'text-dark fw-bold text-decoration-none']);
                    }
                    ],
                    'phone',
                    [
                        'attribute' => 'direction_id',
                        'value' => 'direction.name_uz',
                        // In reality, this filter should build a dropdown ArrayHelper::map
                    ],
                    [
                        'attribute' => 'edu_form_id',
                        'value' => 'eduForm.name_uz',
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'filter' => [
                            Student::STATUS_NEW => 'Yangi',
                            Student::STATUS_ANKETA => 'Anketa',
                            Student::STATUS_EXAM_SCHEDULED => 'Imtihon Kutmoqda',
                            Student::STATUS_EXAM_PASSED => 'Imtihondan O\'tdi',
                            Student::STATUS_CONTRACT_SIGNED => 'Shartnoma',
                            Student::STATUS_PAID => 'To\'ladi'
                        ],
                        'value' => function ($model) {
                        return \frontend\widgets\StatusBadge::widget(['status' => $model->status, 'label' => $model->getStatusLabel()]);
                    }
                    ],
                    [
                        'label' => 'Bali',
                        'value' => function ($model) {
                        $ex = $model->getStudentExams()->one();
                        return $ex ? $ex->score . '%' : '-';
                    }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view} {update} {status} {delete}',
                        'buttons' => [
                            'status' => function ($url, $model, $key) {
                            return Html::a('<i class="bi bi-arrow-left-right text-warning"></i>', '#', [
                                'class' => 'btn btn-sm btn-light border status-btn ms-1',
                                'title' => 'Holatni o\'zgartirish',
                                'data-id' => $model->id,
                                'data-current' => $model->status
                            ]);
                        },
                        ]
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>

<!-- Modal Container included efficiently -->
<?= $this->render('_status_modal') ?>

<?php
$js = <<<JS
$('.status-btn').on('click', function(e) {
    e.preventDefault();
    let id = $(this).data('id');
    $('#status-form-student-id').val(id);
    
    // An AJAX call could map allowed states, but here we simplify assumption 
    // that the modal fetches partial or handles it via JS mapped to constants.
    
    $('#statusModal').modal('show');
});

$('#saveStatusBtn').on('click', function() {
    let btn = $(this);
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
    $.post('/student/change-status', $('#statusForm').serialize())
        .done(function(res) {
            if(res.success) {
                location.reload();
            } else {
                alert(res.message);
                btn.prop('disabled', false).html('Saqlash');
            }
        });
});
JS;
$this->registerJs($js);
?>