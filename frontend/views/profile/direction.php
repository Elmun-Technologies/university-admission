<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use frontend\widgets\StepProgress;
use yii\helpers\ArrayHelper;

$this->title = Yii::t('app', 'Yo\'nalish tanlash');
$this->params['breadcrumbs'][] = $this->title;

// Map out the direction array specifically for the frontend payload
$dirData = [];
foreach ($directions as $direction) {
    $forms = [];
    foreach ($direction->eduForms as $form) {
        $forms[] = ['id' => $form->id, 'name' => $form->name_uz];
    }

    $types = [];
    foreach ($direction->eduTypes as $type) {
        $types[] = ['id' => $type->id, 'name' => $type->name_uz];
    }

    $dirData[$direction->id] = [
        'name' => $direction->name_uz,
        'code' => $direction->code ?? '-',
        'fee' => $direction->getTuitionFeeFormatted(),
        'duration' => $direction->duration_years,
        'forms' => $forms,
        'types' => $types
    ];
}
$dirJson = json_encode($dirData);
?>

<div class="profile-direction">

    <?= StepProgress::widget(['currentStep' => 4]) ?>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <h4 class="mb-4"><i class="bi bi-journal-bookmark"></i>
                <?= Html::encode($this->title) ?>
            </h4>

            <?php $form = ActiveForm::begin(['id' => 'direction-form']); ?>

            <div class="row">
                <!-- Left Grid of Directions -->
                <div class="col-lg-7 mb-4">
                    <h5 class="text-primary mb-3">Mavjud yo'nalishlar</h5>

                    <div class="row g-3" id="directions-grid">
                        <?php foreach ($directions as $dir): ?>
                            <div class="col-md-6 direction-card-wrapper" data-id="<?= $dir->id ?>">
                                <div class="card h-100 border direction-card cursor-pointer"
                                    style="cursor: pointer; transition: 0.2s;">
                                    <div class="card-body p-3">
                                        <h6 class="card-title fw-bold text-dark mb-1">
                                            <?= Html::encode($dir->name_uz) ?>
                                        </h6>
                                        <div class="small text-muted mb-2">Kodi: <span
                                                class="badge bg-light text-dark border">
                                                <?= Html::encode($dir->code ?? '-') ?>
                                            </span></div>
                                        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                                            <span class="small fw-bold text-success">
                                                <?= $dir->getTuitionFeeFormatted() ?>
                                            </span>
                                            <span class="small text-muted"><i class="bi bi-clock"></i>
                                                <?= $dir->duration_years ?> yil
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Right Sidebar Sticky Options -->
                <div class="col-lg-5">
                    <div class="card border border-primary shadow-sm sticky-top" style="top: 100px;">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Tanlangan yo'nalish</h5>
                        </div>
                        <div class="card-body bg-light" id="selection-panel">

                            <div id="empty-state" class="text-center py-5 text-muted">
                                <i class="bi bi-hand-index-thumb fs-1 mb-2"></i>
                                <p>Ro'yxatdan yo'nalishni tanlang</p>
                            </div>

                            <div id="active-state" class="d-none">
                                <h5 id="selected-name" class="fw-bold mb-3 text-dark"></h5>

                                <div class="mb-3">
                                    <label class="fw-bold small text-muted text-uppercase mb-2">Ta'lim shakli</label>
                                    <div id="forms-container" class="d-flex gap-2 flex-wrap"></div>
                                    <?= $form->field($model, 'edu_form_id')->hiddenInput(['id' => 'hidden-form-id'])->label(false) ?>
                                </div>

                                <div class="mb-4">
                                    <label class="fw-bold small text-muted text-uppercase mb-2">Qabul turi</label>
                                    <div id="types-container" class="d-flex gap-2 flex-wrap"></div>
                                    <?= $form->field($model, 'edu_type_id')->hiddenInput(['id' => 'hidden-type-id'])->label(false) ?>
                                </div>

                                <!-- Hidden real Direction ID input -->
                                <?= $form->field($model, 'direction_id')->hiddenInput(['id' => 'hidden-dir-id'])->label(false) ?>

                                <div class="alert alert-info py-2 small mb-0">
                                    <i class="bi bi-info-circle"></i> Tanlov amalga oshirilgach, shartnoma <b>ushbu
                                        qiymatlar</b> asosida shakllantiriladi.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-5 pt-3 border-top">
                <?= Html::a('<i class="bi bi-arrow-left"></i> ' . Yii::t('app', 'Orqaga'), ['photo'], ['class' => 'btn btn-outline-secondary px-4']) ?>
                <?= Html::submitButton(Yii::t('app', 'Arizani yakunlash') . ' <i class="bi bi-check-circle"></i>', ['class' => 'btn btn-success px-4', 'id' => 'finish-btn', 'disabled' => true]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
// Advanced Vanilla JS UI handler replacing complex Ajax with embedded JSON payloads
$this->registerJs("
    const dirData = {$dirJson};
    
    // Selectors
    const cards = document.querySelectorAll('.direction-card-wrapper');
    const emptyState = document.getElementById('empty-state');
    const activeState = document.getElementById('active-state');
    const titleEl = document.getElementById('selected-name');
    const formsContainer = document.getElementById('forms-container');
    const typesContainer = document.getElementById('types-container');
    
    // Inputs
    const hiddenDir = document.getElementById('hidden-dir-id');
    const hiddenForm = document.getElementById('hidden-form-id');
    const hiddenType = document.getElementById('hidden-type-id');
    const finishBtn = document.getElementById('finish-btn');

    function checkCompleteness() {
        if(hiddenDir.value && hiddenForm.value && hiddenType.value) {
            finishBtn.disabled = false;
        } else {
            finishBtn.disabled = true;
        }
    }

    cards.forEach(wrapper => {
        wrapper.addEventListener('click', function() {
            // Remove active from all
            document.querySelectorAll('.direction-card').forEach(c => {
                c.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10', 'shadow');
            });
            
            // Add to current
            const card = this.querySelector('.direction-card');
            card.classList.add('border-primary', 'bg-primary', 'bg-opacity-10', 'shadow');
            
            const dirId = this.getAttribute('data-id');
            const data = dirData[dirId];
            
            // Reset hidden inputs
            hiddenDir.value = dirId;
            hiddenForm.value = '';
            hiddenType.value = '';
            
            // Update UI Sidebar
            emptyState.classList.add('d-none');
            activeState.classList.remove('d-none');
            titleEl.textContent = data.name;
            
            // Build Forms logic
            formsContainer.innerHTML = '';
            data.forms.forEach(f => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-outline-primary btn-sm rounded-pill form-selector';
                btn.textContent = f.name;
                btn.onclick = function() {
                    document.querySelectorAll('.form-selector').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    hiddenForm.value = f.id;
                    checkCompleteness();
                };
                formsContainer.appendChild(btn);
            });
            
            // Build Types logic
            typesContainer.innerHTML = '';
            data.types.forEach(t => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-outline-secondary btn-sm border-2 type-selector';
                btn.textContent = t.name;
                btn.onclick = function() {
                    document.querySelectorAll('.type-selector').forEach(b => {
                        b.classList.remove('active', 'btn-secondary', 'text-white');
                        b.classList.add('btn-outline-secondary');
                    });
                    this.classList.remove('btn-outline-secondary');
                    this.classList.add('active', 'btn-secondary', 'text-white');
                    hiddenType.value = t.id;
                    checkCompleteness();
                };
                typesContainer.appendChild(btn);
            });
            
            checkCompleteness();
        });
    });
");
?>