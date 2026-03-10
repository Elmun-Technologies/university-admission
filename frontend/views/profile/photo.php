<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use frontend\widgets\StepProgress;

$this->title = Yii::t('app', 'Rasm yuklash');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="profile-photo">

    <?= StepProgress::widget(['currentStep' => 3]) ?>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4 text-center">
            <h4 class="mb-4"><i class="bi bi-camera"></i>
                <?= Html::encode($this->title) ?>
            </h4>

            <p class="text-muted mb-4">
                Tizim uchun 3x4 o'lchamdagi, oq fonda olingan sifatli rasmingizni yuklang.<br>
                Maksimal hajm 3MB. Rasm avtomatik tarzda kesilib to'g'irlanadi.
            </p>

            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

            <div class="d-flex flex-column align-items-center mb-4">
                <div id="photo-preview-container"
                    class="rounded overflow-hidden border bg-light d-flex align-items-center justify-content-center mb-3 shadow-sm"
                    style="width: 200px; height: 250px; object-fit: cover;">
                    <?php if ($model->photo) : ?>
                        <img src="/<?= Html::encode($model->photo) ?>" alt="Current Photo"
                            style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else : ?>
                        <i class="bi bi-person text-secondary" style="font-size: 5rem;"></i>
                    <?php endif; ?>
                </div>

                <div class="custom-file w-100" style="max-width: 300px;">
                    <?= $form->field($model, 'photo')->fileInput([
                        'accept' => 'image/jpeg, image/png',
                        'class' => 'form-control',
                        'id' => 'photo-upload'
                    ])->label(false) ?>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-5">
                <?= Html::a('<i class="bi bi-arrow-left"></i> ' . Yii::t('app', 'Orqaga'), ['documents'], ['class' => 'btn btn-outline-secondary px-4']) ?>
                <?= Html::submitButton(Yii::t('app', 'Keyingi qadam') . ' <i class="bi bi-arrow-right"></i>', ['class' => 'btn btn-primary px-4', 'id' => 'upload-btn']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
// Native vanilla JS FileReader for live instant preview
$this->registerJs("
    document.getElementById('photo-upload').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            // Check size limits
            if(file.size > 3 * 1024 * 1024) {
                alert('Fayl hajmi 3MB dan oshmasligi kerak!');
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const container = document.getElementById('photo-preview-container');
                container.innerHTML = '<img src=\"' + e.target.result + '\" style=\"width: 100%; height: 100%; object-fit: cover;\">';
            };
            reader.readAsDataURL(file);
        }
    });
");
?>