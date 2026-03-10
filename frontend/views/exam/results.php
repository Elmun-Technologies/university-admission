<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Imtihon Natijasi');
?>

<div class="exam-results text-center py-5">

    <?php if ($attempt->is_passed) : ?>
        <div class="display-1 text-success mb-3"><i class="bi bi-check-circle-fill"></i></div>
        <h2 class="text-success fw-bold">Tabriklaymiz, siz imtihondan o'tdingiz!</h2>
    <?php else : ?>
        <div class="display-1 text-danger mb-3"><i class="bi bi-x-circle-fill"></i></div>
        <h2 class="text-danger fw-bold">Afsuski, yetarli bal to'play olmadingiz.</h2>
    <?php endif; ?>

    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h5 class="border-bottom pb-3 mb-4">Natija tafsilotlari</h5>

                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted">Imtihon yo'nalishi:</span>
                        <span class="fw-bold">
                            <?= Html::encode($attempt->exam->direction->name_uz) ?>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted">Jami savollar:</span>
                        <span class="fw-bold">
                            <?= $attempt->max_score ?> ta
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted">To'g'ri javoblar:</span>
                        <span class="fw-bold text-primary">
                            <?= $attempt->score ?> ta
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Umumiy foiz:</span>
                        <span class="h3 mb-0 <?= $attempt->is_passed ? 'text-success' : 'text-danger' ?> fw-bold">
                            <?= $attempt->getScorePercent() ?>%
                        </span>
                    </div>
                    <div class="small text-muted text-end">(Talab qilingan o'tish bali:
                        <?= $attempt->exam->passing_score ?>%)
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <a href="<?= Url::to(['/dashboard/index']) ?>" class="btn btn-primary btn-lg rounded-pill px-5 shadow">
                    Shaxsiy kabinetga qaytish <i class="bi bi-box-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>