<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Imtihonni belgilash');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="exam-schedule">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <h4 class="mb-2"><i class="bi bi-calendar-check text-primary"></i>
                <?= Html::encode($this->title) ?>
            </h4>
            <p class="text-muted border-bottom pb-3">Yo'nalishingiz: <b>
                    <?= Html::encode($exam->direction->name_uz) ?>
                </b></p>

            <div class="alert alert-info border-0 bg-opacity-10 py-3">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-info-circle fs-4 me-3 text-info"></i>
                    <div>
                        <h6 class="mb-0 fw-bold">Imtihon qoidalari</h6>
                        <span class="small">Davomiylik:
                            <?= $exam->duration_minutes ?> daqiqa | O'tish bali:
                            <?= $exam->passing_score ?>%
                        </span>
                    </div>
                </div>
            </div>

            <h5 class="mt-4 mb-3">Mavjud sanalar:</h5>

            <?php if (empty($dates)) : ?>
                <div class="text-center py-5 text-muted border rounded bg-light">
                    <i class="bi bi-calendar-x fs-1"></i>
                    <p class="mt-2">Hozirda bo'sh joylar yoki kelgusi imtihon sanalari mavjud emas.<br>Iltimos, keyinroq
                        qayta urining.</p>
                </div>
            <?php else : ?>
                <div class="list-group">
                    <?php foreach ($dates as $date) : ?>
                        <div
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                            <div>
                                <h5 class="mb-1 text-primary fw-bold">
                                    <i class="bi bi-calendar-date me-2"></i>
                                    <?= date('d.m.Y', strtotime($date->exam_date)) ?>
                                </h5>
                                <p class="mb-0 text-muted">Boshlanish vaqti: <b>
                                        <?= date('H:i', strtotime($date->start_time)) ?>
                                    </b></p>
                                <span class="badge bg-light text-dark border mt-2">Bo'sh joylar:
                                    <?= $date->getSlotsAvailable() ?>
                                </span>
                            </div>

                            <?= Html::a(
                                'Tanlash <i class="bi bi-chevron-right"></i>',
                                ['register', 'id' => $date->id],
                                [
                                    'class' => 'btn btn-primary px-4 rounded-pill shadow-sm',
                                    'data' => [
                                        'confirm' => Yii::t('app', "Siz aynan shu sanaga yozilmoqchimisiz? Tasdiqlaysizmi?"),
                                        'method' => 'post',
                                    ],
                                ]
                            )
                            ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="mt-4 pb-2">
                <a href="<?= Url::to(['/dashboard/index']) ?>" class="btn btn-outline-secondary"><i
                        class="bi bi-arrow-left"></i> Orqaga</a>
            </div>
        </div>
    </div>
</div>