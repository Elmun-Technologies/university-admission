<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\StudentOferta;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Shartnomalar va To\'lovlar';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contract-index">

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-wallet2 text-success me-2"></i>Moliya va Shartnomalar</h5>
        </div>

        <div class="card-body p-0">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-hover align-middle mb-0'],
                'layout' => "<div class='table-responsive'>{items}</div><div class='card-footer bg-white border-0 d-flex justify-content-between align-items-center'>{summary}{pager}</div>",
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    [
                        'attribute' => 'contract_number',
                        'format' => 'raw',
                        'value' => function ($model) {
                                        return Html::a('<b>#' . $model->contract_number . '</b>', ['/student/view', 'id' => $model->student_id, '#' => 'contract'], ['class' => 'text-decoration-none text-primary']);
                                    }
                    ],
                    [
                        'label' => 'Abiturient',
                        'value' => function ($model) {
                                        return $model->student->getFullName();
                                    }
                    ],
                    [
                        'label' => 'Yo\'nalish',
                        'value' => function ($model) {
                                        return $model->student->direction->name_uz ?? '-';
                                    }
                    ],
                    [
                        'attribute' => 'contract_amount',
                        'value' => function ($model) {
                                        return number_format($model->contract_amount, 0, '', ' ') . ' UZS';
                                    }
                    ],
                    [
                        'attribute' => 'payment_amount',
                        'format' => 'raw',
                        'value' => function ($model) {
                                        $percent = $model->contract_amount > 0 ? round(($model->payment_amount / $model->contract_amount) * 100) : 0;
                                        $color = $percent >= 100 ? 'success' : ($percent > 0 ? 'warning' : 'danger');

                                        $html = '<div class="d-flex align-items-center">';
                                        $html .= '<span class="fw-bold me-2">' . number_format($model->payment_amount, 0, '', ' ') . '</span>';
                                        $html .= '<span class="badge bg-' . $color . '">' . $percent . '%</span>';
                                        $html .= '</div>';
                                        return $html;
                                    }
                    ],
                    [
                        'attribute' => 'payment_status',
                        'format' => 'raw',
                        'value' => function ($model) {
                                        switch ($model->payment_status) {
                                            case StudentOferta::PAYMENT_PENDING:
                                                return '<span class="badge bg-secondary">Kutilmoqda</span>';
                                            case StudentOferta::PAYMENT_PARTIAL:
                                                return '<span class="badge bg-warning text-dark">Qisman to\'landi</span>';
                                            case StudentOferta::PAYMENT_PAID:
                                                return '<span class="badge bg-success">To\'liq to\'landi</span>';
                                            case StudentOferta::PAYMENT_CANCELLED:
                                                return '<span class="badge bg-danger">Bekor qilindi</span>';
                                        }
                                        return '-';
                                    }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{pdf} {pay}',
                        'buttons' => [
                            'pdf' => function ($url, $model, $key) {
                                            return Html::a('<i class="bi bi-file-pdf text-danger bg-light p-1 rounded"></i>', ['download-pdf', 'id' => $model->id], ['class' => 'btn btn-sm text-decoration-none', 'title' => 'PDF Yuklab olish', 'data-pjax' => 0]);
                                        },
                            'pay' => function ($url, $model, $key) {
                                            return Html::a('<i class="bi bi-credit-card text-success bg-light p-1 rounded"></i>', ['/student/view', 'id' => $model->student_id, '#' => 'contract'], ['class' => 'btn btn-sm text-decoration-none', 'title' => 'To\'lov kiritish']);
                                        },
                        ]
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>