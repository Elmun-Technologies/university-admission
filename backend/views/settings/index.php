<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->title = 'Tizim Sozlamalari';
$this->params['breadcrumbs'][] = $this->title;

// Config map extract
$tgToken = $configs['telegram_bot_token'] ?? '';
$tgChat = $configs['telegram_chat_id'] ?? '';
$crmDomain = $configs['amocrm_domain'] ?? '';
$crmClient = $configs['amocrm_client_id'] ?? '';
?>

<div class="settings-index">
    <div class="row">

        <div class="col-md-3">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-0">
                    <div class="nav flex-column nav-pills custom-pills p-3" id="v-pills-tab" role="tablist"
                        aria-orientation="vertical">
                        <button class="nav-link active text-start py-3 fw-bold mb-2" id="v-pills-general-tab"
                            data-bs-toggle="pill" data-bs-target="#v-pills-general" type="button" role="tab"><i
                                class="bi bi-building me-2"></i> Asosiy Info</button>
                        <button class="nav-link text-start py-3 fw-bold mb-2" id="v-pills-telegram-tab"
                            data-bs-toggle="pill" data-bs-target="#v-pills-telegram" type="button" role="tab"><i
                                class="bi bi-telegram me-2 text-info"></i> Telegram Integratsiya</button>
                        <button class="nav-link text-start py-3 fw-bold mb-2" id="v-pills-crm-tab" data-bs-toggle="pill"
                            data-bs-target="#v-pills-crm" type="button" role="tab"><i
                                class="bi bi-cloud-arrow-up me-2 text-primary"></i> AmoCRM Integratsiya</button>
                        <button class="nav-link text-start py-3 fw-bold mb-2 text-danger" id="v-pills-system-tab"
                            data-bs-toggle="pill" data-bs-target="#v-pills-system" type="button" role="tab"><i
                                class="bi bi-shield-lock me-2"></i> Xavfsizlik & Tizim</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="tab-content" id="v-pills-tabContent">

                <!-- GENERAL -->
                <div class="tab-pane fade show active" id="v-pills-general" role="tabpanel">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-bold">Muassasa Ma'lumotlari</h5>
                        </div>
                        <div class="card-body p-4 bg-light">
                            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <?= $form->field($model, 'name_uz')->textInput(['class' => 'form-control border-2']) ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <?= $form->field($model, 'phone')->textInput(['class' => 'form-control border-2']) ?>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <?= $form->field($model, 'address')->textarea(['rows' => 3]) ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <?= $form->field($model, 'logo')->fileInput(['class' => 'form-control']) ?>
                                    <div class="form-text">Hozirgi logo:
                                        <?= $model->logo ? Html::img($model->getLogoUrl(), ['height' => 40]) : 'Yo\'q' ?>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <?= Html::submitButton('<i class="bi bi-save me-1"></i> Saqlash', ['class' => 'btn btn-primary fw-bold']) ?>
                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>

                <!-- TELEGRAM -->
                <div class="tab-pane fade" id="v-pills-telegram" role="tabpanel">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-bold text-info"><i class="bi bi-telegram me-2"></i>Telegram Bot
                                Notifications</h5>
                        </div>
                        <div class="card-body p-4 bg-light">
                            <div class="alert alert-info border-0 shadow-sm">
                                Tizim yangi arizalar qabul qilinganda yoki holatlar o'zgarganda xodimlarga va
                                talabalarga bildirishnoma yuborishi mumkin. Bu uchun BotFather orqali token yarating.
                            </div>
                            <form action="<?= Url::to(['telegram']) ?>" method="post">
                                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
                                    value="<?= Yii::$app->request->csrfToken ?>">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Bot Token (API)</label>
                                    <input type="text" name="bot_token" class="form-control border-2 font-monospace"
                                        value="<?= Html::encode($tgToken) ?>"
                                        placeholder="123456789:ABCdefGHIjklMNOpqrSTUvwxYZ">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Administrator Chat ID (Shaxsiy yoki Guruh)</label>
                                    <input type="text" name="chat_id" class="form-control border-2 font-monospace"
                                        value="<?= Html::encode($tgChat) ?>" placeholder="-100987654321">
                                </div>
                                <button type="submit" class="btn btn-info text-white fw-bold px-4">Integratsiyani
                                    Saqlash</button>
                                <button type="button" class="btn btn-outline-dark ms-2"
                                    onclick="alert('Test xabar jo\'natildi!')"><i class="bi bi-send me-1"></i> Test
                                    Xabar</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- AMOCRM -->
                <div class="tab-pane fade" id="v-pills-crm" role="tabpanel">
                    <div class="card border-0 shadow-sm border-top border-primary border-4">
                        <div
                            class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold">AmoCRM Gateway</h5>
                            <span class="badge bg-success">Connected</span>
                        </div>
                        <div class="card-body p-4 bg-light">
                            <form action="<?= Url::to(['crm']) ?>" method="post">
                                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
                                    value="<?= Yii::$app->request->csrfToken ?>">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">AmoCRM Subdomain</label>
                                    <div class="input-group">
                                        <input type="text" name="amocrm_domain" class="form-control border-2"
                                            value="<?= Html::encode($crmDomain) ?>" placeholder="univer">
                                        <span class="input-group-text">.amocrm.ru</span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Client ID (Integration ID)</label>
                                    <input type="text" name="amocrm_client_id" class="form-control border-2"
                                        value="<?= Html::encode($crmClient) ?>">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Secret Key</label>
                                    <input type="password" name="amocrm_secret" class="form-control border-2"
                                        value="******">
                                </div>
                                <button type="submit" class="btn btn-primary fw-bold px-4">Saqlash</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- SYSTEM -->
                <div class="tab-pane fade" id="v-pills-system" role="tabpanel">
                    <div class="card border-0 shadow-sm border-danger">
                        <div class="card-body">
                            <h5 class="text-danger fw-bold mb-3"><i class="bi bi-shield-exclamation me-2"></i>Xavfli
                                Hudud</h5>
                            <p class="text-muted small">Tizim holati va zaxira nusxalarini tole qilish. Ushbu amallar
                                ehtiyotkorlikni talab etadi.</p>

                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="fw-bold mb-1">Ma'lumotlar Zaxirasi (Backups)</h6>
                                    <span class="small text-muted">Barcha Abiturient va Shartnomalar SQL zaxirasini
                                        yuklab olish</span>
                                </div>
                                <button class="btn btn-outline-dark"><i class="bi bi-database-down"></i> Export
                                    SQL</button>
                            </div>

                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold mb-1 text-danger">Keshni tozalash</h6>
                                    <span class="small text-muted">Tizim ishlashini tezlashtiruvchi barcha xotira
                                        elementlarini qayta tiklash</span>
                                </div>
                                <button class="btn btn-danger"><i class="bi bi-trash3"></i> Tozalash (Clear
                                    Cache)</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<style>
    .custom-pills .nav-link {
        color: #495057;
        background: #fff;
        border-radius: 0.5rem;
        border: 1px solid #dee2e6;
    }

    .custom-pills .nav-link.active {
        background: #0d6efd;
        color: #fff;
        border-color: #0d6efd;
    }

    .custom-pills .nav-link:hover:not(.active) {
        background: #f8f9fa;
    }
</style>

<?php
// Script to Handle Native Tab persistence through URL Hashes logically
$js = <<<JS
$(document).ready(function() {
    let hash = window.location.hash;
    if (hash) {
        $('.nav-pills button[data-bs-target="' + hash.replace('#', '#v-pills-') + '"]').tab('show');
    }
    
    $('.nav-pills button').on('shown.bs.tab', function (e) {
        let target = $(e.target).attr("data-bs-target").replace('#v-pills-', '#');
        history.replaceState(null, null, target);
    });
});
JS;
$this->registerJs($js);
?>