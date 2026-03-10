<?php

/** @var yii\web\View $this */
/** @var frontend\models\RegisterForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\captcha\Captcha;

$this->title = Yii::t('app', 'Ro\'yxatdan o\'tish');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup mt-3">

    <p class="text-muted text-center mb-4">
        <?= Yii::t('app', 'Qabul tizimiga kirish uchun ma\'lumotlaringizni kiriting') ?>
    </p>

    <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'first_name')->textInput(['autofocus' => true, 'placeholder' => 'Alisher']) ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'last_name')->textInput(['placeholder' => 'Karimov']) ?>
        </div>
    </div>

    <?= $form->field($model, 'phone', [
        'inputOptions' => [
            'class' => 'form-control',
            'placeholder' => '+998901234567',
        ]
    ])->textInput() ?>

    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'password')->passwordInput(['placeholder' => '******']) ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'password_confirm')->passwordInput(['placeholder' => '******']) ?>
        </div>
    </div>

    <div class="mt-2">
        <?= $form->field($model, 'verifyCode')->widget(Captcha::class, [
            'template' => '<div class="row"><div class="col-5">{image}</div><div class="col-7">{input}</div></div>',
            'captchaAction' => '/auth/captcha',
            'options' => ['class' => 'form-control', 'placeholder' => Yii::t('app', 'Kodni kiriting')],
        ]) ?>
    </div>

    <div class="d-grid gap-2 mt-4">
        <?= Html::submitButton(Yii::t('app', 'Ro\'yxatdan o\'tish'), ['class' => 'btn btn-primary btn-lg shadow-sm', 'name' => 'signup-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="text-center mt-4 pt-3 border-top">
        <p class="mb-0 text-muted">
            Ro'yxatdan o'tganmisiz? / Есть аккаунт?
        </p>
        <a href="<?= \yii\helpers\Url::to(['/auth/login']) ?>" class="btn btn-outline-secondary btn-sm mt-2 fw-bold">
            <?= Yii::t('app', 'Kirish / Войти') ?>
        </a>
    </div>
</div>

<?php
// Simple vanilla JS to enforce phone prefix immediately for UX
$this->registerJs("
    document.getElementById('registerform-phone').addEventListener('input', function (e) {
        if(this.value.length === 0 || this.value.indexOf('+998') !== 0) {
            this.value = '+998';
        }
        // Restrict length
        if(this.value.length > 13) {
            this.value = this.value.substring(0,13);
        }
    });
    // On focus ensuring it exists
    document.getElementById('registerform-phone').addEventListener('focus', function (e) {
        if(this.value.length === 0) {
            this.value = '+998';
        }
    });
");
?>