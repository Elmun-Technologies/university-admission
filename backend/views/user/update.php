<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Branch;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\UserForm */
/* @var $form yii\widgets\ActiveForm */

$this->title = $model->isNewRecord() ? 'Yangi Xodim qo\'shish' : 'Tahrirlash: ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Xodimlar', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Map Roles natively from DB securely bypassing direct array mappings
$roles = ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'description');
$branches = ArrayHelper::map(Branch::find()->all(), 'id', 'name_uz');
?>

<div class="user-form">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold">Xodim Anketasi</h5>
                </div>
                <div class="card-body p-4 bg-light bg-opacity-50">
                    <?php $form = ActiveForm::begin(); ?>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <?= $form->field($model, 'first_name')->textInput(['class' => 'form-control border-2']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'last_name')->textInput(['class' => 'form-control border-2']) ?>
                        </div>

                        <div class="col-md-6 mt-4">
                            <?= $form->field($model, 'username')->textInput(['class' => 'form-control border-2 font-monospace']) ?>
                        </div>
                        <div class="col-md-6 mt-4">
                            <?= $form->field($model, 'email')->textInput(['class' => 'form-control border-2']) ?>
                        </div>

                        <div class="col-md-6 mt-4">
                            <?= $form->field($model, 'phone')->textInput(['class' => 'form-control border-2', 'placeholder' => '+998901234567']) ?>
                        </div>
                        <div class="col-md-6 mt-4">
                            <?php
                            $passOptions = ['class' => 'form-control border-2'];
                            if (!$model->isNewRecord()) {
                                $passOptions['placeholder'] = 'O\'zgartirish uchun yangi parol kiriting';
                            }
                            echo $form->field($model, 'password')->passwordInput($passOptions);
                            ?>
                        </div>

                        <div class="col-12 mt-4 pt-3 border-top">
                            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-shield-check me-2"></i>Ruxsat va
                                Huquqlar</h6>
                        </div>

                        <div class="col-md-6">
                            <?= $form->field($model, 'role')->dropDownList($roles, ['prompt' => '-- Rolni tanlang --', 'class' => 'form-select border-2']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'branch_id')->dropDownList($branches, ['prompt' => '-- Filial biriktirish (Global uchun bo\'sh) --', 'class' => 'form-select border-2']) ?>
                        </div>
                        <div class="col-md-6 mt-4">
                            <?= $form->field($model, 'status')->dropDownList([
                                10 => 'Faol (Active)',
                                9 => 'Bloklangan (Inactive)'
                            ], ['class' => 'form-select border-2']) ?>
                        </div>
                    </div>

                    <div class="mt-5 text-end border-top pt-4">
                        <?= Html::a('Bekor qilish', ['index'], ['class' => 'btn btn-light me-2 fw-bold text-muted']) ?>
                        <?= Html::submitButton('<i class="bi bi-check2-circle me-1"></i> Saqlash', ['class' => 'btn btn-primary fw-bold px-4']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4 d-none d-lg-block">
            <div class="card bg-primary text-white shadow-sm border-0 rounded-3 p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-info-circle me-2"></i>Eslatma</h5>
                <p class="small text-white-50 lh-lg text-justify">
                    Xodim qo'shish davomida <strong>Rol (Huquq)</strong> juda ehtiyotkorlik bilan tanlanishi kerak.
                    Tizimda mavjud bo'lgan RBAC tizimi yordamida har bir rol o'z chegaralariga ega.
                </p>
                <ul class="small text-white-50 ps-3 lh-lg mb-0 mt-3 border-top pt-3 border-opacity-25 border-light">
                    <li><strong class="text-white">Admin:</strong> Barcha filiallarni ko'ra oladi va qisman o'zgartira
                        oladi.</li>
                    <li><strong class="text-white">Manager:</strong> O'z filialida Talabalar ulanishlarni o'zgartira
                        oladi.</li>
                    <li><strong class="text-white">Cashier:</strong> Faqatgina to'lovlarni qabul qilish huquqiga ega.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>