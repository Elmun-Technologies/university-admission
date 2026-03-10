<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use common\models\Student;

/* @var $this yii\web\View */
/* @var $model common\models\Student */

$this->title = $model->getFullName();
$this->params['breadcrumbs'][] = ['label' => 'Abiturientlar', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$photoUrl = $model->photo ? Yii::getAlias('@web/uploads/photos/') . $model->photo : 'https://ui-avatars.com/api/?name='.urlencode($model->getFullName());
?>
<div class="student-view">

    <!-- Header Actions natively mapped -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold">
            <img src="<?= $photoUrl ?>" class="rounded-circle shadow-sm me-2" style="width: 50px; height: 50px; object-fit: cover;">
            <?= Html::encode($this->title) ?>
        </h4>
        <div>
            <?= \frontend\widgets\StatusBadge::widget(['status' => $model->status, 'label' => $model->getStatusLabel()]) ?>
            <?= Html::a('<i class="bi bi-pencil me-1"></i> Tahrirlash', ['update', 'id' => $model->id], ['class' => 'btn btn-outline-primary ms-2']) ?>
            <?= Html::a('<i class="bi bi-trash me-1"></i> O\'chirish', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-outline-danger ms-1',
                'data' => [
                    'confirm' => 'Haqiqatan ham o\'chirmoqchimisiz?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>

    <!-- Bootstrap 5 Tabs -->
    <ul class="nav nav-tabs nav-tabs-custom mb-4 border-bottom-0" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold text-dark px-4 py-3" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab" aria-selected="true"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Shaxsiy Ma'lumot</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold text-dark px-4 py-3" id="docs-tab" data-bs-toggle="tab" data-bs-target="#docs" type="button" role="tab" aria-selected="false"><i class="bi bi-file-earmark-medical me-2 text-primary"></i>Hujjatlar</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold text-dark px-4 py-3" id="exam-tab" data-bs-toggle="tab" data-bs-target="#exam" type="button" role="tab" aria-selected="false"><i class="bi bi-laptop me-2 text-primary"></i>Imtihon</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold text-dark px-4 py-3" id="contract-tab" data-bs-toggle="tab" data-bs-target="#contract" type="button" role="tab" aria-selected="false"><i class="bi bi-wallet2 me-2 text-primary"></i>Shartnoma va To'lov</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold text-dark px-4 py-3" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab" aria-selected="false"><i class="bi bi-clock-history me-2 text-primary"></i>Tarix</button>
        </li>
    </ul>

    <div class="tab-content bg-white p-4 shadow-sm border rounded-3" id="myTabContent">
        
        <!-- Tab 1: Personal -->
        <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="small text-muted fw-bold text-uppercase">Ro'yxatdan o'tgan sana</label>
                    <div class="fs-5"><?= date('d.m.Y H:i', $model->created_at) ?></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="small text-muted fw-bold text-uppercase">Telefon</label>
                    <div class="fs-5 fw-bold text-primary"><?= Html::encode($model->phone) ?></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="small text-muted fw-bold text-uppercase">Jinsi</label>
                    <div class="fs-5"><?= $model->gender == 1 ? 'Erkak' : 'Ayol' ?></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="small text-muted fw-bold text-uppercase">Yo'nalish</label>
                    <div class="fs-5">
                        <span class="badge bg-light text-dark border"><?= Html::encode($model->direction->name_uz ?? '-') ?></span>
                        <span class="badge bg-secondary"><?= Html::encode($model->eduForm->name_uz ?? '-') ?></span>
                        <span class="badge bg-secondary"><?= Html::encode($model->eduType->name_uz ?? '-') ?></span>
                    </div>
                </div>
                
                <?php if($model->consulting_id): ?>
                <div class="col-12 mt-3 pt-3 border-top">
                    <div class="bg-light p-3 rounded" style="border-left: 4px solid #0dcaf0;">
                        <span class="small text-muted fw-bold text-uppercase block mb-1">Konsalting Agentligi</span>
                        <h6 class="mb-0 fw-bold"><?= Html::encode($model->consulting->name) ?></h6>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tab 2: Docs -->
        <div class="tab-pane fade" id="docs" role="tabpanel" aria-labelledby="docs-tab">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="small text-muted fw-bold text-uppercase">Pasport</label>
                    <div class="fs-5 text-uppercase fw-bold"><?= Html::encode($model->passport_series . ' ' . $model->passport_number) ?></div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="small text-muted fw-bold text-uppercase">JSHSHIR (PINFL)</label>
                    <div class="fs-5 font-monospace"><?= Html::encode($model->pinfl) ?></div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="small text-muted fw-bold text-uppercase">Tug'ilgan sana</label>
                    <div class="fs-5"><?= Html::encode($model->birth_date) ?></div>
                </div>
                <div class="col-12 mt-3 text-center p-5 bg-light rounded border border-dashed">
                    <i class="bi bi-file-earmark-pdf fs-1 text-muted d-block mb-3"></i>
                    <h6 class="text-muted fw-bold">Skonnerlangan Hujjatlar (Diplom/Baho) yuklanmagan</h6>
                </div>
            </div>
        </div>

        <!-- Tab 3: Exam -->
        <div class="tab-pane fade" id="exam" role="tabpanel" aria-labelledby="exam-tab">
            <?php $exam = current($model->getStudentExams()->all()); ?>
            <?php if(!$exam): ?>
                <div class="alert alert-warning border-0"><i class="bi bi-info-circle me-2"></i>Abiturient hali imtihon topshirmagan yoki imtihon belgilamagan.</div>
            <?php else: ?>
                <div class="row text-center mb-4">
                    <div class="col-md-4">
                        <div class="p-4 bg-light rounded shadow-sm border">
                            <h6 class="text-muted fw-bold mb-2">Imtihon Sanasi</h6>
                            <h4><?= date('d.m.Y H:i', strtotime($exam->examDate->exam_date . ' ' . $exam->examDate->start_time)) ?></h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4 bg-light rounded shadow-sm border">
                            <h6 class="text-muted fw-bold mb-2">Holat</h6>
                            <h4><span class="badge bg-<?= $exam->status == 1 ? 'success' : 'secondary' ?>"><?= $exam->status == 1 ? 'Tugatgan' : 'Kutilmoqda' ?></span></h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4 rounded shadow-sm <?= $exam->score >= 60 ? 'bg-success text-white' : 'bg-danger text-white' ?>">
                            <h6 class="opacity-75 fw-bold mb-2 text-white">To'plagan Bali</h6>
                            <h2 class="mb-0 fw-bold"><?= $exam->score ?: '0' ?>%</h2>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Tab 4: Contract -->
        <div class="tab-pane fade" id="contract" role="tabpanel" aria-labelledby="contract-tab">
            <?php if($model->status < Student::STATUS_EXAM_PASSED): ?>
                <div class="alert alert-danger border-0"><i class="bi bi-exclamation-triangle me-2"></i>Abiturient shartnoma bosqichiga yetib kelmagan.</div>
            <?php else: ?>
                <?php $oferta = $model->studentOferta; ?>
                <?php if(!$oferta): ?>
                    <?= Html::a('<i class="bi bi-file-earmark-plus"></i> Shartnoma Yaratish', ['/contract/generate', 'id' => $model->id], ['class' => 'btn btn-primary', 'data-method' => 'post']) ?>
                <?php else: ?>
                    <div class="row">
                        <div class="col-md-6 border-end">
                            <h5 class="fw-bold mb-4">Shartnoma Holati</h5>
                            <table class="table table-borderless table-sm">
                                <tr><th class="text-muted w-50">Shartnoma raqami:</th><td class="fw-bold text-dark"><?= Html::encode($oferta->contract_number) ?></td></tr>
                                <tr><th class="text-muted">Imzolangan sana:</th><td><?= $oferta->signed_at ? date('d.m.Y H:i', $oferta->signed_at) : '<span class="badge bg-warning text-dark">Imzolanmagan</span>' ?></td></tr>
                                <tr><th class="text-muted">Umumiy summa:</th><td class="fs-5 text-primary fw-bold"><?= number_format($oferta->contract_amount, 0, '', ' ') ?> UZS</td></tr>
                                <tr><th class="text-muted">To'langan:</th><td class="fs-5 text-success fw-bold"><?= number_format($oferta->payment_amount, 0, '', ' ') ?> UZS</td></tr>
                            </table>
                            <div class="mt-4">
                                <?= Html::a('<i class="bi bi-printer me-2"></i>Yuklab olish (PDF)', ['/contract/download-pdf', 'id' => $oferta->id], ['class' => 'btn btn-outline-dark']) ?>
                            </div>
                        </div>
                        <div class="col-md-6 px-4">
                            <h5 class="fw-bold mb-4 text-success"><i class="bi bi-cash me-2"></i>To'lov Kiritish</h5>
                            <!-- Partial payment form logically mapped over native inputs -->
                            <form action="<?= Url::to(['/contract/update-payment', 'id' => $oferta->id]) ?>" method="post">
                                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold">To'lov miqdori (UZS)</label>
                                    <input type="number" name="amount" class="form-control border-2 form-control-lg fw-bold text-success" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold">Sana</label>
                                    <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold">To'lov turi / Izoh</label>
                                    <input type="text" name="method" class="form-control" placeholder="Masalan: Bank o'tkazmasi, raqam #1255">
                                </div>
                                <button type="submit" class="btn btn-success fw-bold w-100 py-2"><i class="bi bi-check-circle me-2"></i>Saqlash</button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Tab 5: Timeline -->
        <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
            <h5 class="fw-bold mb-4">Tarix va O'zgarishlar</h5>
            <div class="timeline ps-3" style="border-left: 2px solid #e9ecef;">
                <?php 
                $history = json_decode($model->status_history ?? '[]', true); 
                if(empty($history)): 
                ?>
                    <div class="text-muted small ms-3">Hali o'zgarishlar tarixi mavjud emas.</div>
                <?php else: ?>
                    <?php 
                    $history = array_reverse($history); // Newest at top
                    foreach($history as $h): 
                        $statusObj = new Student(); // Temp just physically for mapping label natively if we wanted to
                    ?>
                    <div class="position-relative mb-4 ms-4">
                        <div class="position-absolute bg-primary rounded-circle" style="width: 14px; height: 14px; left: -31px; top: 5px; border: 3px solid #fff; box-shadow: 0 0 0 2px #0d6efd;"></div>
                        <div class="card border-0 shadow-sm bg-light">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-primary">Holat: <?= Html::encode($h['to']) ?></span>
                                    <small class="text-muted fw-bold"><i class="bi bi-clock me-1"></i><?= date('d.m.Y H:i', $h['time']) ?></small>
                                </div>
                                <?php if(!empty($h['note'])): ?>
                                    <p class="mb-1 text-dark small" style="line-height:1.4">"<?= Html::encode($h['note']) ?>"</p>
                                <?php endif; ?>
                                <small class="text-muted" style="font-size:0.7rem">Muallif ID: <?= Html::encode($h['user_id']) ?></small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <button class="btn btn-outline-primary btn-sm mt-4 status-btn fw-bold" data-id="<?= $model->id ?>" data-current="<?= $model->status ?>">
                <i class="bi bi-plus-circle me-1"></i> Yangi Holat Kiritish
            </button>
        </div>
        
    </div>
</div>

<style>
.nav-tabs-custom .nav-link { border: none; border-bottom: 3px solid transparent; color: #6c757d; }
.nav-tabs-custom .nav-link:hover { color: #212529; }
.nav-tabs-custom .nav-link.active { border-bottom-color: #0d6efd; background: transparent; color: #0d6efd !important; }
</style>
