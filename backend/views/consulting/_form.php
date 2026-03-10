<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Consulting */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="consulting-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6 mb-3">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'class' => 'form-control border-2']) ?>
        </div>
        <div class="col-md-6 mb-3">
            <?= $form->field($model, 'phone')->textInput(['maxlength' => true, 'class' => 'form-control border-2', 'placeholder' => '+998XXXXXXXXX']) ?>
        </div>
        <div class="col-md-6 mb-3">
            <?= $form->field($model, 'contact_person')->textInput(['maxlength' => true, 'class' => 'form-control border-2']) ?>
        </div>
        <div class="col-md-6 mb-3">
            <?= $form->field($model, 'status')->dropDownList([
                1 => 'Faol (Active)',
                0 => 'Nofaol (Inactive)',
            ], ['class' => 'form-select border-2']) ?>
        </div>
        <div class="col-md-12 mb-3">
            <?= $form->field($model, 'address')->textarea(['rows' => 3, 'class' => 'form-control border-2']) ?>
        </div>
    </div>

    <div class="form-group mt-4 pt-3 border-top text-end">
        <?= Html::a('Bekor qilish', ['index'], ['class' => 'btn btn-light me-2 fw-bold text-muted']) ?>
        <?= Html::submitButton('<i class="bi bi-check2-circle me-1"></i> Saqlash', ['class' => 'btn btn-primary fw-bold px-4']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>