<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\widgets\MaskedInput;
use frontend\widgets\StepProgress;

$this->title = Yii::t('app', 'Shaxsiy ma\'lumotlar');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="profile-personal">

    <?= StepProgress::widget(['currentStep' => 1]) ?>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <h4 class="mb-4">
                <?= Html::encode($this->title) ?>
            </h4>

            <?php $form = ActiveForm::begin(); ?>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-4 mb-3">
                    <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-4 mb-3">
                    <?= $form->field($model, 'middle_name')->textInput(['maxlength' => true]) ?>
                </div>
            </div>

            <div class="row bg-light rounded p-3 mb-4 mx-0">
                <div class="col-md-4 mb-3">
                    <?= $form->field($model, 'first_name_ru')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-4 mb-3">
                    <?= $form->field($model, 'last_name_ru')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-4 mb-3">
                    <?= $form->field($model, 'middle_name_ru')->textInput(['maxlength' => true]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <?= $form->field($model, 'birth_date')->textInput(['type' => 'date']) ?>
                </div>
                <div class="col-md-4 mb-3">
                    <?= $form->field($model, 'gender')->dropDownList([
                        1 => Yii::t('app', 'Erkak / Мужчина'),
                        2 => Yii::t('app', 'Ayol / Женщина')
                    ], ['prompt' => '---']) ?>
                </div>
            </div>

            <div class="row mt-3 border-top pt-4">
                <div class="col-md-6 mb-3">
                    <?= $form->field($model, 'phone')->textInput(['readonly' => true, 'class' => 'form-control bg-light']) ?>
                </div>
                <div class="col-md-6 mb-3">
                    <?= $form->field($model, 'phone2')->widget(MaskedInput::class, [
                        'mask' => '+\9\98999999999',
                    ]) ?>
                </div>
                <div class="col-md-6 mb-3">
                    <?= $form->field($model, 'email')->textInput(['type' => 'email']) ?>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <?= Html::submitButton(Yii::t('app', 'Keyingi qadam') . ' <i class="bi bi-arrow-right"></i>', ['class' => 'btn btn-primary px-4']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>