<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\StudentOferta;

/* @var $this yii\web\View */
/* @var $model common\models\StudentOferta */

$this->title = 'Shartnoma #' . $model->contract_number;
$this->params['breadcrumbs'][] = ['label' => 'Shartnomalar', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contract-view">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold text-dark"><i class="bi bi-file-earmark-text text-primary me-2"></i>Shartnoma
            Tafsilotlari</h4>
        <div>
            <?= Html::a('<i class="bi bi-file-pdf me-1"></i> PDF Yuklab olish', ['download-pdf', 'id' => $model->id], ['class' => 'btn btn-outline-danger', 'data-pjax' => 0]) ?>
            <?= Html::a('<i class="bi bi-person me-1"></i> Abiturient Profili', ['/student/view', 'id' => $model->student_id], ['class' => 'btn btn-outline-primary ms-2']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold">Asosiy Ma'lumotlar</h6>
                </div>
                <div class="card-body p-0">
                    <?= DetailView::widget([
                        'model' => $model,
                        'options' => ['class' => 'table table-hover mb-0'],
                        'attributes' => [
                            'contract_number',
                            [
                                'label' => 'Abiturient',
                                'value' => $model->student->getFullName(),
                            ],
                            [
                                'label' => 'Yo\'nalish',
                                'value' => $model->student->direction->name_uz ?? '-',
                            ],
                            [
                                'attribute' => 'contract_amount',
                                'value' => number_format($model->contract_amount, 0, '', ' ') . ' UZS',
                            ],
                            [
                                'attribute' => 'signed_at',
                                'format' => 'datetime',
                            ],
                            [
                                'attribute' => 'created_at',
                                'format' => 'datetime',
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-success text-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-cash-coin me-2"></i>To'lov Holati</h6>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-success mb-1">
                            <?= number_format($model->payment_amount, 0, '', ' ') ?> UZS
                        </h2>
                        <span class="text-muted small">Jami to'langan summa</span>
                    </div>

                    <?php
                    $percent = $model->contract_amount > 0 ? round(($model->payment_amount / $model->contract_amount) * 100) : 0;
                    ?>
                    <div class="progress mb-3" style="height: 10px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= $percent ?>%"
                            aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex justify-content-between small fw-bold mb-4">
                        <span>To'lov:
                            <?= $percent ?>%
                        </span>
                        <span>Qoldi:
                            <?= number_format($model->contract_amount - $model->payment_amount, 0, '', ' ') ?> UZS
                        </span>
                    </div>

                    <div class="bg-light p-3 rounded mb-4 border">
                        <h6 class="fw-bold small mb-2">Oxirgi to'lov ma'lumotlari:</h6>
                        <ul class="list-unstyled small mb-0 lh-lg">
                            <li><i class="bi bi-calendar-check me-2"></i>Sana: <b>
                                    <?= $model->payment_date ?: '-' ?>
                                </b></li>
                            <li><i class="bi bi-credit-card me-2"></i>Usul: <b>
                                    <?= Html::encode($model->payment_method ?: '-') ?>
                                </b></li>
                        </ul>
                    </div>

                    <button class="btn btn-success w-100 fw-bold" data-bs-toggle="modal" data-bs-target="#paymentModal">
                        <i class="bi bi-plus-circle me-2"></i>Yangi To'lov Kiritish
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Payment Modal (simplified wrapper for the same logic in Student view) -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold">To'lov Kiritish</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="<?= \yii\helpers\Url::to(['update-payment', 'id' => $model->id]) ?>" method="post">
                <div class="modal-body p-4">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
                        value="<?= Yii::$app->request->csrfToken ?>">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Summa (UZS)</label>
                        <input type="number" name="amount"
                            class="form-control form-control-lg fw-bold text-success border-2" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Sana</label>
                        <input type="date" name="date" class="form-control border-2" value="<?= date('Y-m-d') ?>"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">To'lov turi / Izoh</label>
                        <input type="text" name="method" class="form-control border-2"
                            placeholder="Masalan: Bank #9988">
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Yopish</button>
                    <button type="submit" class="btn btn-success fw-bold px-4">Saqlash</button>
                </div>
            </form>
        </div>
    </div>
</div>