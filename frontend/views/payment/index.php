<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Student;

$this->title = Yii::t('app', 'To\'lovni amalga oshirish');
$this->params['breadcrumbs'][] = $this->title;

$isPaid = $oferta && $oferta->payment_status == \common\models\StudentOferta::PAYMENT_PAID;
?>

<div class="payment-index">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-lg-5">
            <h4 class="mb-4 text-primary fw-bold"><i class="bi bi-credit-card me-2"></i>
                <?= Html::encode($this->title) ?>
            </h4>

            <?php if ($isPaid) : ?>
                <div class="text-center py-5">
                    <div class="display-1 text-success mb-3"><i class="bi bi-check-circle-fill"></i></div>
                    <h2 class="text-success fw-bold">To'lov muvaffaqiyatli amalga oshirilgan!</h2>
                    <p class="text-muted fs-5 mt-2">Shartnoma bo'yicha to'lov qabul qilindi. Ariza to'liq yakunlandi.</p>
                    <div class="mt-4">
                        <a href="<?= Url::to(['/dashboard/index']) ?>" class="btn btn-primary rounded-pill px-4 shadow-sm">
                            Shaxsiy kabinetga qaytish
                        </a>
                    </div>
                </div>
            <?php else : ?>
                <div class="row gx-lg-5">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">Shartnoma tafsilotlari</h5>
                        <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                            <span class="text-muted">Shartnoma raqami:</span>
                            <span class="fw-bold">
                                <?= Html::encode($oferta->contract_number ?? '-') ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                            <span class="text-muted">F.I.Sh:</span>
                            <span class="fw-bold">
                                <?= Html::encode($student->getFullName()) ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                            <span class="text-muted">Yo'nalish:</span>
                            <span class="fw-bold text-end">
                                <?= Html::encode($student->direction->name_uz ?? '-') ?>
                            </span>
                        </div>
                        <div
                            class="d-flex justify-content-between align-items-center bg-light p-3 rounded mt-4 border border-primary border-opacity-25">
                            <span class="text-muted fs-5">Jami to'lov:</span>
                            <span class="fs-3 fw-bold text-primary">
                                <?= number_format($oferta->payment_amount ?? 0, 0, '', ' ') ?> UZS
                            </span>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">To'lov usulini tanlang</h5>

                        <div class="d-grid gap-3">
                            <a href="<?= Url::to(['initiate']) ?>"
                                class="btn btn-outline-primary payment-btn d-flex align-items-center p-3 text-start">
                                <div class="bg-primary bg-opacity-10 p-3 rounded me-3 text-primary">
                                    <i class="bi bi-wallet2 fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Click / Payme (Mock)</h6>
                                    <small class="text-muted">Plastik karta orqali darhol to'lash</small>
                                </div>
                                <i class="bi bi-chevron-right ms-auto fs-5"></i>
                            </a>

                            <div class="border rounded p-3 text-muted bg-light d-flex align-items-center opacity-75">
                                <div class="bg-secondary bg-opacity-10 p-3 rounded me-3 text-secondary">
                                    <i class="bi bi-bank fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Bank o'tkazmasi</h6>
                                    <small class="text-muted">Kvitansiya orqali kassa orqali (Tez kunda)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<style>
    .payment-btn {
        transition: 0.2s;
        border-width: 2px;
    }

    .payment-btn:hover {
        background-color: rgba(13, 110, 253, 0.05);
        transform: translateY(-2px);
    }
</style>