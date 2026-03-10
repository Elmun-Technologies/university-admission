<?php

/** @var yii\web\View $this */
/** @var common\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = Yii::t('app', 'Tizimga kirish');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login mt-3">

    <p class="text-muted text-center mb-4">
        Kirish uchun telefon raqamingiz va parolni kiriting
    </p>

    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

    <?= $form->field($model, 'username', [
        'inputOptions' => [
            'class' => 'form-control form-control-lg',
            'placeholder' => '+998901234567',
        ]
    ])->textInput(['autofocus' => true])->label(Yii::t('app', 'Telefon raqamingiz')) ?>

    <?= $form->field($model, 'password')->passwordInput([
        'class' => 'form-control form-control-lg',
        'placeholder' => '******'
    ])->label(Yii::t('app', 'Parol')) ?>

    <div class="d-flex justify-content-between align-items-center my-3">
        <?= $form->field($model, 'rememberMe')->checkbox([
            'template' => "<div class=\"form-check custom-control custom-checkbox\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
        ])->label(Yii::t('app', 'Eslab qolish / Запомнить меня')) ?>
    </div>

    <div class="d-grid gap-2 mt-4">
        <?= Html::submitButton(Yii::t('app', 'Kirish'), ['class' => 'btn btn-primary btn-lg shadow-sm', 'name' => 'login-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="text-center mt-4 pt-3 border-top">
        <p class="mb-0 text-muted">
            <?= Yii::t('app', 'Tizimda yo\'qmisiz?') ?>
        </p>
        <a href="<?= \yii\helpers\Url::to(['/auth/register']) ?>" class="btn btn-outline-secondary btn-sm mt-2 fw-bold">
            <?= Yii::t('app', 'Ro\'yxatdan o\'tish / Регистрация') ?>
        </a>
    </div>

</div>

<?php
$this->registerJs("
    document.getElementById('loginform-username').addEventListener('input', function (e) {
        if(this.value.length > 0 && this.value.indexOf('+') !== 0) {
            this.value = '+' + this.value;
        }
    });
");
?>