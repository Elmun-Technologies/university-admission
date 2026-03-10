<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\Region;
use common\models\District;
use frontend\widgets\StepProgress;

$this->title = Yii::t('app', 'Hujjatlar va Manzil');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="profile-documents">

    <?= StepProgress::widget(['currentStep' => 2]) ?>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <h4 class="mb-4"><i class="bi bi-file-earmark-person"></i>
                <?= Html::encode($this->title) ?>
            </h4>

            <?php $form = ActiveForm::begin(['id' => 'documents-form']); ?>

            <h5 class="text-primary mb-3 mt-2">
                <?= Yii::t('app', 'Pasport ma\'lumotlari / Паспортные данные') ?>
            </h5>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <?= $form->field($model, 'passport_series')->textInput(['maxlength' => 2, 'placeholder' => 'AA', 'class' => 'form-control text-uppercase']) ?>
                </div>
                <div class="col-md-5 mb-3">
                    <?= $form->field($model, 'passport_number')->textInput(['maxlength' => 7, 'placeholder' => '1234567']) ?>
                </div>
                <div class="col-md-4 mb-3">
                    <?= $form->field($model, 'pinfl')->textInput(['maxlength' => 14, 'placeholder' => '12345678901234']) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 mb-3">
                    <?= $form->field($model, 'passport_given_by')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-4 mb-3">
                    <?= $form->field($model, 'passport_given_date')->textInput(['type' => 'date']) ?>
                </div>
            </div>

            <h5 class="text-primary mb-3 mt-4 border-top pt-4">
                <?= Yii::t('app', 'Yashash manzili / Адрес проживания') ?>
            </h5>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <?php
                    $regions = ArrayHelper::map(Region::find()->all(), 'id', 'name_uz');
                    echo $form->field($model, 'region_id')->dropDownList($regions, ['prompt' => '---']);
                    ?>
                </div>
                <div class="col-md-6 mb-3">
                    <?php
                    // Typically an AJAX dropdown cascade, mapping empty locally for layout 
                    $districts = $model->region_id ? ArrayHelper::map(District::find()->where(['region_id' => $model->region_id])->all(), 'id', 'name_uz') : [];
                    echo $form->field($model, 'district_id')->dropDownList($districts, ['prompt' => '---']);
                    ?>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <?= $form->field($model, 'address')->textarea(['rows' => 2]) ?>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <?= Html::a('<i class="bi bi-arrow-left"></i> ' . Yii::t('app', 'Orqaga'), ['personal'], ['class' => 'btn btn-outline-secondary px-4']) ?>
                <?= Html::submitButton(Yii::t('app', 'Keyingi qadam') . ' <i class="bi bi-arrow-right"></i>', ['class' => 'btn btn-primary px-4']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
// Example simple cascade loader 
$this->registerJs("
    $('#student-region_id').on('change', function() {
        var regionId = $(this).val();
        // Typically a fetch to API endpoint internally returning options
        // For demonstration, we'll assume a basic structure.
    });
");
?>