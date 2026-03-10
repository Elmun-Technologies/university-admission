<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Elektron Shartnoma');
$this->params['breadcrumbs'][] = $this->title;

$isSigned = !empty($oferta->signed_at);
?>

<div class="contract-view">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-lg-5">

            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <h3 class="mb-0 text-primary fw-bold">🎓 OMMAVIY OFERTA</h3>
                <div class="text-end">
                    <div class="text-muted small">Shartnoma raqami:</div>
                    <h5 class="fw-bold mb-0">
                        <?= Html::encode($oferta->contract_number) ?>
                    </h5>
                </div>
            </div>

            <!-- Fake Legal Contract Text Block Template -->
            <div class="contract-text bg-light p-4 rounded border mb-4"
                style="max-height: 400px; overflow-y: auto; font-family: 'Times New Roman', serif;">
                <h5 class="text-center fw-bold text-uppercase mb-4">Oliy Ta'lim Muassasasiga O'qishga Qabul Qilish
                    To'g'risida<br>SHARTNOMA</h5>
                <p>Bir tomondan <b>
                        <?= Html::encode($student->branch->name_uz ?? 'Oliy ta\'lim muassasasi') ?>
                    </b> (keyingi o'rinlarda "Ta'lim muassasasi" deb nomlanadi) va ikkinchi tomondan fuqaro <b>
                        <?= Html::encode($student->getFullName()) ?>
                    </b> (keyingi o'rinlarda "Talaba" deb nomlanadi) quyidagilar bo'yicha ushbu shartnomani tuzdilar:
                </p>

                <h6 class="fw-bold mt-4">1. SHARTNOMA PREDMETI</h6>
                <p>1.1. Ta'lim muassasasi Talabani <b>
                        <?= Html::encode($student->direction->name_uz ?? '-') ?>
                    </b> yo'nalishi bo'yicha o'qitish majburiyatini, Talaba esa o'qitish uchun belgilangan to'lovni o'z
                    vaqtida to'lash majburiyatini oladi.</p>
                <p>1.2. Ta'lim muddati: <b>
                        <?= $student->direction->duration_years ?? 4 ?> yil
                    </b>.</p>

                <h6 class="fw-bold mt-4">2. TO'LOV SHARTLARI</h6>
                <p>2.1. O'qish uchun to'lov miqdori: <b>
                        <?= Html::encode($oferta->payment_amount) ?> UZS
                    </b> etib belgilanadi.</p>

                <hr class="my-4">

                <div class="row">
                    <div class="col-6">
                        <span class="fw-bold">Talaba rekvizitlari:</span><br>
                        F.I.Sh:
                        <?= Html::encode($student->getFullName()) ?><br>
                        Pasport:
                        <?= Html::encode($student->passport_series . $student->passport_number) ?><br>
                        JSHSHIR:
                        <?= Html::encode($student->pinfl) ?><br>
                        Telefon:
                        <?= Html::encode($student->phone) ?>
                    </div>
                </div>
            </div>

            <?php if ($isSigned): ?>
                <div class="alert alert-success d-flex align-items-center mb-0">
                    <i class="bi bi-shield-check fs-2 me-3"></i>
                    <div>
                        <h6 class="fw-bold mb-1">Shartnoma imzolangan</h6>
                        <span class="small">Imzolangan sana:
                            <?= date('d.m.Y H:i', $oferta->signed_at) ?>
                        </span>
                    </div>
                    <div class="ms-auto">
                        <a href="<?= Url::to(['download']) ?>" target="_blank"
                            class="btn btn-outline-success border-2 fw-bold">
                            <i class="bi bi-printer"></i> Chop etish / PDF
                        </a>
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <a href="<?= Url::to(['/payment/index']) ?>"
                        class="btn btn-primary btn-lg rounded-pill px-5 shadow">To'lov sahifasiga o'tish <i
                            class="bi bi-arrow-right"></i></a>
                </div>
            <?php else: ?>
                <?php $form = ActiveForm::begin(['action' => ['sign']]); ?>
                <div class="bg-primary bg-opacity-10 border border-primary p-4 rounded mb-4">
                    <div class="form-check custom-checkbox">
                        <input class="form-check-input" type="checkbox" id="agreeCheckbox" name="agree_terms" required>
                        <label class="form-check-label fw-bold ms-2" for="agreeCheckbox" style="cursor: pointer;">
                            Men ushbu ommaviy oferta shartlari bilan to'liq tanishib chiqdim va rozi ekanligimni
                            tasdiqlayman. (I agree to the terms)
                        </label>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="<?= Url::to(['/dashboard/index']) ?>" class="btn btn-outline-secondary px-4"><i
                            class="bi bi-arrow-left"></i> Bekor qilish</a>
                    <button type="submit" class="btn btn-success btn-lg px-5 shadow-sm" id="signButton" disabled>
                        <i class="bi bi-pen"></i> Shartnomani imzolash
                    </button>
                </div>
                <?php ActiveForm::end(); ?>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php if (!$isSigned): ?>
    <?php $this->registerJs("
    document.getElementById('agreeCheckbox').addEventListener('change', function() {
        document.getElementById('signButton').disabled = !this.checked;
    });
"); ?>
<?php endif; ?>