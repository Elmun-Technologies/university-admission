<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/* @var $this yii\web\View */
/* @var $student common\models\Student */
/* @var $prefMap array */

$this->title = Yii::t('app', 'Xabarnoma sozlamalari');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Mening arizam'), 'url' => ['/dashboard/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profile-notifications container my-4">
    <div class="row w-100">
        <!-- Sidebar Navigation -->
        <div class="col-md-3 mb-4">
            <div class="list-group shadow-sm">
                <a href="<?= \yii\helpers\Url::to(['/profile/personal']) ?>" class="list-group-item list-group-item-action py-3">
                    <i class="fas fa-user text-muted me-2"></i> <?= Yii::t('app', 'Shaxsiy ma\'lumotlar') ?>
                </a>
                <a href="<?= \yii\helpers\Url::to(['/profile/documents']) ?>" class="list-group-item list-group-item-action py-3">
                    <i class="fas fa-id-card text-muted me-2"></i> <?= Yii::t('app', 'Hujjatlar') ?>
                </a>
                <a href="<?= \yii\helpers\Url::to(['/profile/photo']) ?>" class="list-group-item list-group-item-action py-3">
                    <i class="fas fa-camera text-muted me-2"></i> <?= Yii::t('app', 'Rasm') ?>
                </a>
                 <a href="<?= \yii\helpers\Url::to(['/profile/direction']) ?>" class="list-group-item list-group-item-action py-3">
                    <i class="fas fa-university text-muted me-2"></i> <?= Yii::t('app', 'Yo\'nalish tanlash') ?>
                </a>
                <a href="<?= \yii\helpers\Url::to(['/profile/notifications']) ?>" class="list-group-item list-group-item-action active py-3">
                    <i class="fas fa-bell me-2"></i> <?= Yii::t('app', 'Xabarnomalar') ?>
                </a>
            </div>
        </div>

        <!-- Content Area -->
        <div class="col-md-9">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 text-primary fw-bold">
                        <i class="fas fa-bell me-2"></i> <?= Html::encode($this->title) ?>
                    </h5>
                </div>
                <div class="card-body p-4">
                    
                    <p class="text-muted mb-4">
                        <?= Yii::t('app', 'Imtihon, shartnoma va to\'lovlar haqida qaysi usullar orqali xabar olishni xohlasangiz, shularni yoqing.') ?>
                    </p>

                    <?php $form = ActiveForm::begin(['id' => 'notifications-form']); ?>

                    <div class="list-group mb-4">
                        
                        <!-- SMS -->
                        <div class="list-group-item py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><i class="fas fa-sms text-primary me-2"></i> SMS</h6>
                                <small class="text-muted"><?= Html::encode($student->phone) ?> raqamiga yuboriladi</small>
                            </div>
                            <div class="form-check form-switch fs-4">
                                <?= Html::checkbox("Notifications[sms]", $prefMap['sms']->is_enabled, ['class' => 'form-check-input', 'id' => 'notif-sms']) ?>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="list-group-item py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><i class="fas fa-envelope text-danger me-2"></i> Email</h6>
                                <small class="text-muted"><?= $student->email ? Html::encode($student->email) : Yii::t('app', 'Email kiritilmagan') ?></small>
                            </div>
                            <div class="form-check form-switch fs-4">
                                <?= Html::checkbox("Notifications[email]", $prefMap['email']->is_enabled, ['class' => 'form-check-input', 'id' => 'notif-email', 'disabled' => empty($student->email)]) ?>
                            </div>
                        </div>

                        <!-- Telegram -->
                        <div class="list-group-item py-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <h6 class="mb-1"><i class="fab fa-telegram text-info me-2"></i> Telegram <span class="badge bg-<?= $prefMap['telegram']->telegram_id ? 'success' : 'warning' ?> ms-2"><?= $prefMap['telegram']->telegram_id ? Yii::t('app', 'Ulangan') : Yii::t('app', 'Ulanmagan') ?></span></h6>
                                    <small class="text-muted"><?= Yii::t('app', 'Telegram botimiz orqali tezkor xabarlar oling') ?></small>
                                </div>
                                <div class="form-check form-switch fs-4">
                                    <?= Html::checkbox("Notifications[telegram]", $prefMap['telegram']->is_enabled, ['class' => 'form-check-input', 'id' => 'notif-telegram']) ?>
                                </div>
                            </div>
                            
                            <?php if (!$prefMap['telegram']->telegram_id) : ?>
                            <div class="alert alert-info mt-3 mb-0">
                                <strong><?= Yii::t('app', 'Qanday ulanadi?') ?></strong>
                                <ol class="mb-0 mt-2">
                                    <li>Telegramda <strong><a href="https://t.me/beruniy_qabul_bot" target="_blank">@beruniy_qabul_bot</a></strong> ni toping va <code>/start</code> bosing.</li>
                                    <li>Botga <strong><?= Html::encode($prefMap['telegram']->telegram_code) ?></strong> kodini yuboring.</li>
                                </ol>
                            </div>
                            <?php else : ?>
                            <div class="alert alert-success mt-3 mb-0 py-2 border-0">
                                <i class="fas fa-check-circle me-1"></i> Telegram hisobingiz muvaffaqiyatli ulangan.
                            </div>
                            <?php endif; ?>
                        </div>

                    </div>

                    <div class="text-end">
                        <?= Html::submitButton('<i class="fas fa-save me-1"></i> ' . Yii::t('app', 'Saqlash'), ['class' => 'btn btn-primary btn-lg px-4 fw-bold shadow-sm']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>
            </div>
        </div>
    </div>
</div>
