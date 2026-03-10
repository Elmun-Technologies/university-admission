<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Student;

$this->title = Yii::t('app', 'Mening arizam');
$this->params['breadcrumbs'][] = $this->title;

// Map Student states to visual progress index logically
$statusMap = [
    Student::STATUS_NEW => 1,
    Student::STATUS_ANKETA => 2,
    Student::STATUS_EXAM_SCHEDULED => 3,
    Student::STATUS_EXAM_PASSED => 4,
    Student::STATUS_EXAM_FAILED => 4, // Stops here internally but step reflects attempt made
    Student::STATUS_CONTRACT_SIGNED => 5,
    Student::STATUS_PAID => 6,
];
$currentStep = $statusMap[$student->status] ?? 1;

// Define Steps Configuration Array
$steps = [
    1 => ['icon' => 'person-lines-fill', 'title' => 'Anketa'],
    2 => ['icon' => 'journal-bookmark', 'title' => 'Yo\'nalish'],
    3 => ['icon' => 'calendar-check', 'title' => 'Imtihon'],
    4 => ['icon' => 'check2-square', 'title' => 'Natija'],
    5 => ['icon' => 'pen', 'title' => 'Shartnoma'],
    6 => ['icon' => 'credit-card', 'title' => 'To\'lov'],
];
?>

<div class="dashboard-index">

    <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
        <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center shadow-sm"
            style="width: 50px; height: 50px; font-size: 24px;">
            <?= strtoupper(substr($student->first_name, 0, 1)) ?>
        </div>
        <div class="ms-3">
            <h4 class="mb-0 fw-bold">
                <?= Html::encode($student->getFullName()) ?>
            </h4>
            <span class="text-muted">Abiturient ID: <span class="fw-bold text-dark">
                    <?= str_pad($student->id, 5, '0', STR_PAD_LEFT) ?>
                </span></span>
        </div>

        <div class="ms-auto text-end">
            <div class="small text-muted text-uppercase fw-bold mb-1">Mavjud Holat</div>
            <div>
                <?= $student->getStatusBadge() ?>
            </div>
        </div>
    </div>

    <!-- Massive Horizontal Process Stepper Visual -->
    <div class="card shadow-sm border-0 mb-4 bg-light overflow-hidden">
        <div class="card-body p-4 p-md-5 position-relative">
            <div class="progress position-absolute" style="top: 50%; left: 5%; right: 5%; height: 4px; z-index: 1;">
                <?php $percent = (($currentStep - 1) / (count($steps) - 1)) * 100; ?>
                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $percent ?>%;"></div>
            </div>

            <div class="d-flex justify-content-between position-relative" style="z-index: 2;">
                <?php foreach ($steps as $index => $step):
                    $isCompleted = $index < $currentStep;
                    $isCurrent = $index == $currentStep;
                    $bgClass = $isCompleted ? 'bg-success text-white' : ($isCurrent ? 'bg-primary text-white shadow' : 'bg-white text-muted border border-2');
                    ?>
                    <div class="text-center" style="width: 80px;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto <?= $bgClass ?>"
                            style="width: 45px; height: 45px; font-size: 1.2rem; transition: 0.3s;">
                            <?php if ($isCompleted): ?>
                                <i class="bi bi-check-lg"></i>
                            <?php else: ?>
                                <i class="bi bi-<?= $step['icon'] ?>"></i>
                            <?php endif; ?>
                        </div>
                        <div class="mt-2 small fw-bold <?= $isCurrent ? 'text-primary' : 'text-muted' ?>">
                            <?= $step['title'] ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Call To Action Bound specifically to active $student->status -->
        <div class="col-lg-8">
            <div class="card shadow border-primary border-2 h-100">
                <div class="card-body p-4 p-md-5 text-center d-flex flex-column justify-content-center">
                    <?php if ($student->status == Student::STATUS_NEW): ?>
                        <i class="bi bi-card-checklist text-primary mb-3" style="font-size: 3rem;"></i>
                        <h4 class="fw-bold">Arizangizni to'ldiring</h4>
                        <p class="text-muted mb-4">Ariza topshirishni yakunlash uchun shaxsiy ma'lumotlaringizni to'ldiring,
                            hujjatlaringizni yuklang va yo'nalish tanlang.</p>
                        <a href="<?= Url::to(['/profile/index']) ?>"
                            class="btn btn-primary btn-lg rounded-pill px-5 mx-auto">To'ldirishni boshlash <i
                                class="bi bi-arrow-right"></i></a>

                    <?php elseif ($student->status == Student::STATUS_ANKETA): ?>
                        <i class="bi bi-calendar-check text-primary mb-3" style="font-size: 3rem;"></i>
                        <h4 class="fw-bold">Imtihonga yoziling</h4>
                        <p class="text-muted mb-4">Arizangiz qabul qilindi! Endi o'zingiz tanlagan yo'nalish bo'yicha kirish
                            imtihoniga sana belgilashingiz kerak.</p>
                        <a href="<?= Url::to(['/exam/schedule']) ?>"
                            class="btn btn-primary btn-lg rounded-pill px-5 mx-auto">Imtihon sanasini tanlash <i
                                class="bi bi-arrow-right"></i></a>

                    <?php elseif ($student->status == Student::STATUS_EXAM_SCHEDULED): ?>
                        <i class="bi bi-laptop text-primary mb-3" style="font-size: 3rem;"></i>
                        <h4 class="fw-bold">Imtihonga tayyorlaning</h4>
                        <p class="text-muted mb-2">Sizning imtihoningiz belgilandi.</p>
                        <p class="fw-bold fs-5 mb-4 text-dark">
                            <?= date('d.m.Y H:i', strtotime($examAttempt->examDate->exam_date . ' ' . $examAttempt->examDate->start_time)) ?>
                        </p>
                        <a href="<?= Url::to(['/exam/start', 'id' => $examAttempt->id]) ?>"
                            class="btn btn-primary btn-lg rounded-pill px-5 mx-auto">Imtihonni boshlash <i
                                class="bi bi-rocket-takeoff"></i></a>

                    <?php elseif ($student->status == Student::STATUS_EXAM_FAILED): ?>
                        <i class="bi bi-x-octagon text-danger mb-3" style="font-size: 3rem;"></i>
                        <h4 class="fw-bold text-danger">Imtihondan o'ta olmadingiz</h4>
                        <p class="text-muted mb-4">Afsuski, siz yetarli ball to'play olmadingiz. Keyingi yil qayta urinib
                            ko'ring.</p>
                        <a href="<?= Url::to(['/exam/results', 'id' => $examAttempt->id]) ?>"
                            class="btn btn-outline-danger rounded-pill px-5 mx-auto">Natijalarni ko'rish</a>

                    <?php elseif ($student->status == Student::STATUS_EXAM_PASSED): ?>
                        <i class="bi bi-file-earmark-check text-success mb-3" style="font-size: 3rem;"></i>
                        <h4 class="fw-bold text-success">Tabriklaymiz! Siz qabul qilindingiz.</h4>
                        <p class="text-muted mb-4">Davom etish uchun ommaviy ofertani o'qib, elektron tarzda imzolashingiz
                            kerak.</p>
                        <a href="<?= Url::to(['/contract/view']) ?>"
                            class="btn btn-success btn-lg rounded-pill px-5 mx-auto"><i class="bi bi-pen"></i> Shartnomani
                            tuzish</a>

                    <?php elseif ($student->status == Student::STATUS_CONTRACT_SIGNED): ?>
                        <i class="bi bi-credit-card text-primary mb-3" style="font-size: 3rem;"></i>
                        <h4 class="fw-bold">To'lovni amalga oshiring</h4>
                        <p class="text-muted mb-4">Shartnoma tuzildi. Talabalar safiga qo'shilish uchun to'lovni tasdiqlang.
                        </p>
                        <a href="<?= Url::to(['/payment/index']) ?>"
                            class="btn btn-primary btn-lg rounded-pill px-5 mx-auto"><i class="bi bi-wallet2"></i>
                            To'lash</a>

                    <?php elseif ($student->status == Student::STATUS_PAID): ?>
                        <i class="bi bi-stars text-warning mb-3" style="font-size: 4rem;"></i>
                        <h3 class="fw-bold text-primary">Siz Rasman Talabasiz!</h3>
                        <p class="text-muted fs-5 mb-0">Hujjatlaringiz va to'lov muvaffaqiyatli qabul qilindi. Ta'lim
                            muassasamizga xush kelibsiz.</p>

                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar Summary Mappings -->
        <div class="col-lg-4">
            <h6 class="text-uppercase text-muted fw-bold small mb-3">Tafsilotlar</h6>

            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body">
                    <div class="small text-muted mb-1">Tanlangan Muassasa / Filial</div>
                    <div class="fw-bold text-dark"><i class="bi bi-building"></i>
                        <?= Html::encode($student->branch->name_uz ?? 'Toshkent bosh filiali') ?>
                    </div>
                </div>
            </div>

            <?php if ($student->direction_id): ?>
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body">
                        <div class="small text-muted mb-1">Yo'nalish</div>
                        <div class="fw-bold text-dark text-truncate"
                            title="<?= Html::encode($student->direction->name_uz) ?>">
                            <i class="bi bi-bookmark-check"></i>
                            <?= Html::encode($student->direction->name_uz) ?>
                        </div>
                        <div class="text-muted small mt-1">
                            <?= $student->eduForm->name_uz ?? '' ?> /
                            <?= $student->eduType->name_uz ?? '' ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($oferta): ?>
                <div class="card shadow-sm border-0 bg-primary bg-opacity-10 border-start border-4 border-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="small text-muted fw-bold text-uppercase">Shartnoma To'lovi</div>
                            <div>
                                <?= $oferta->getPaymentStatusLabel() ?>
                            </div>
                        </div>
                        <h5 class="fw-bold text-primary mb-0">
                            <?= number_format($oferta->payment_amount, 0, '', ' ') ?> UZS
                        </h5>
                        <?php if ($oferta->contract_number): ?>
                            <div class="small text-muted mt-2">Shartnoma: #
                                <?= Html::encode($oferta->contract_number) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>