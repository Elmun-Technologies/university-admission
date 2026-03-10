<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Direction;
use common\models\EduForm;
use common\models\EduType;

/* @var $this yii\web\View */
/* @var $model common\models\Direction */

$this->title = $model->isNewRecord ? 'Yangi Yo\'nalish yaratish' : 'Tahrirlash: ' . $model->name_uz;
$this->params['breadcrumbs'][] = ['label' => 'Yo\'nalishlar', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Ensure Bootstrap 5 form styling mapping
?>
<div class="direction-form">

    <div class="row">
        <!-- 5-Tab Extensible Form Builder natively -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                    <ul class="nav nav-tabs nav-tabs-custom" id="directionTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active fw-bold px-4 py-3 text-dark" id="basic-tab"
                                data-bs-toggle="tab" data-bs-target="#basic" type="button"><i
                                    class="bi bi-info-circle me-2 text-primary"></i>Asosiy</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold px-4 py-3 text-dark" id="desc-tab" data-bs-toggle="tab"
                                data-bs-target="#desc" type="button"><i
                                    class="bi bi-text-paragraph me-2 text-primary"></i>Ta'rif</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold px-4 py-3 text-dark" id="forms-tab" data-bs-toggle="tab"
                                data-bs-target="#forms" type="button"><i
                                    class="bi bi-card-checklist me-2 text-primary"></i>Ta'lim Shakli</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold px-4 py-3 text-dark" id="types-tab" data-bs-toggle="tab"
                                data-bs-target="#types" type="button"><i
                                    class="bi bi-mortarboard me-2 text-primary"></i>Qabul Turi</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold px-4 py-3 text-dark" id="exams-tab" data-bs-toggle="tab"
                                data-bs-target="#exams" type="button"><i
                                    class="bi bi-list-ol me-2 text-primary"></i>Fanlar</button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4 bg-light bg-opacity-50">
                    <?php $form = ActiveForm::begin([
                        'options' => ['id' => 'direction-form']
                    ]); ?>

                    <div class="tab-content" id="directionTabsContent">

                        <!-- TAB 1: BASIC -->
                        <div class="tab-pane fade show active" id="basic" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <?= $form->field($model, 'name_uz', ['options' => ['class' => 'mb-3']])->textInput(['maxlength' => true, 'class' => 'form-control border-2', 'placeholder' => "Masalan: Dasturiy injiniring"]) ?>
                                </div>
                                <div class="col-md-12">
                                    <?= $form->field($model, 'name_ru', ['options' => ['class' => 'mb-3']])->textInput(['maxlength' => true]) ?>
                                </div>
                                <div class="col-md-4">
                                    <?= $form->field($model, 'code', ['options' => ['class' => 'mb-3']])->textInput(['maxlength' => true, 'placeholder' => '60610100']) ?>
                                </div>
                                <div class="col-md-4">
                                    <?= $form->field($model, 'duration_years', ['options' => ['class' => 'mb-3']])->textInput(['type' => 'number', 'step' => '0.5']) ?>
                                </div>
                                <div class="col-md-4">
                                    <?= $form->field($model, 'tuition_fee', ['options' => ['class' => 'mb-3']])->textInput(['type' => 'number', 'placeholder' => '15000000']) ?>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: DESC -->
                        <div class="tab-pane fade" id="desc" role="tabpanel">
                            <div class="alert alert-light border shadow-sm small text-muted"><i
                                    class="bi bi-info-circle me-2"></i>Abiturientlarga yo'nalish haqida qisqacha
                                ma'lumot saytda ko'rsatiladi.</div>
                            <?= $form->field($model, 'description_uz', ['options' => ['class' => 'mb-4']])->textarea(['rows' => 4]) ?>
                            <?= $form->field($model, 'description_ru', ['options' => ['class' => 'mb-3']])->textarea(['rows' => 4]) ?>
                        </div>

                        <!-- TAB 3: FORMS -->
                        <div class="tab-pane fade" id="forms" role="tabpanel">
                            <div class="row">
                                <?php
                                // Mocked data injection mapping logic
                                $availableForms = EduForm::find()->all();
                                // Assume we have a method getEduFormIds()
                                $selectedForms = !$model->isNewRecord ? \yii\helpers\ArrayHelper::getColumn($model->eduForms, 'id') : [];
                                ?>
                                <?php foreach ($availableForms as $formOption) : ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check border rounded p-3 bg-white shadow-sm">
                                            <input class="form-check-input ms-1" type="checkbox" name="EduForms[]"
                                                value="<?= $formOption->id ?>" id="form-<?= $formOption->id ?>"
                                                <?= in_array($formOption->id, $selectedForms) ? 'checked' : '' ?>
                                            style="transform: scale(1.3);">
                                            <label class="form-check-label fw-bold ms-3 w-100" style="cursor:pointer;"
                                                for="form-<?= $formOption->id ?>">
                                                <?= Html::encode($formOption->name_uz) ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- TAB 4: TYPES -->
                        <div class="tab-pane fade" id="types" role="tabpanel">
                            <div class="row">
                                <?php
                                $availableTypes = EduType::find()->all();
                                $selectedTypes = !$model->isNewRecord ? \yii\helpers\ArrayHelper::getColumn($model->eduTypes, 'id') : [];
                                ?>
                                <?php foreach ($availableTypes as $typeOption) : ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check border rounded p-3 bg-white shadow-sm">
                                            <input class="form-check-input ms-1" type="checkbox" name="EduTypes[]"
                                                value="<?= $typeOption->id ?>" id="type-<?= $typeOption->id ?>"
                                                <?= in_array($typeOption->id, $selectedTypes) ? 'checked' : '' ?>
                                            style="transform: scale(1.3);">
                                            <label class="form-check-label fw-bold ms-3 w-100" style="cursor:pointer;"
                                                for="type-<?= $typeOption->id ?>">
                                                <?= Html::encode($typeOption->name_uz) ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- TAB 5: EXAM SUBJECTS (jQuery Sortable Native Map) -->
                        <div class="tab-pane fade" id="exams" role="tabpanel">
                            <div class="alert alert-primary bg-opacity-10 border-0 text-primary fw-bold"><i
                                    class="bi bi-arrows-move me-2"></i>Fanlarni sudrab o'zaro joylashuvini sozlashingiz
                                mumkin (Asosiy fanlar tepada bo'lishi kerak).</div>

                            <!-- Assuming simple array of Subject IDs submitted -->
                            <ul id="sortable-subjects"
                                class="list-group list-group-flush border shadow-sm rounded mb-4 bg-white"
                                style="min-height: 100px;">
                                <!-- Mocked Subjects already linked -->
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3"
                                    style="cursor: move;">
                                    <div><i class="bi bi-grid-3x3-gap text-muted me-3"></i><span
                                            class="fw-bold">Matematika</span></div>
                                    <input type="hidden" name="Subjects[]" value="1">
                                    <button type="button" class="btn btn-sm btn-light text-danger remove-sub"><i
                                            class="bi bi-x-lg"></i></button>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3"
                                    style="cursor: move;">
                                    <div><i class="bi bi-grid-3x3-gap text-muted me-3"></i><span
                                            class="fw-bold">Fizika</span></div>
                                    <input type="hidden" name="Subjects[]" value="2">
                                    <button type="button" class="btn btn-sm btn-light text-danger remove-sub"><i
                                            class="bi bi-x-lg"></i></button>
                                </li>
                            </ul>

                            <div class="input-group mb-3 shadow-sm">
                                <select class="form-select border-2" id="new-subject-select">
                                    <option value="">-- Fan qo'shish --</option>
                                    <option value="3">Ona tili</option>
                                    <option value="4">Tarix</option>
                                    <option value="5">Chet tili</option>
                                </select>
                                <button class="btn btn-outline-primary fw-bold px-4" type="button" id="add-sub-btn"><i
                                        class="bi bi-plus-lg"></i> Qo'shish</button>
                            </div>
                        </div>

                    </div>

                    <div class="mt-4 pt-4 border-top text-end">
                        <?= Html::a('Bekor qilish', ['index'], ['class' => 'btn btn-light me-2']) ?>
                        <?= Html::submitButton('<i class="bi bi-check-circle me-1"></i> Saqlash', ['class' => 'btn btn-primary fw-bold px-5']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>

        <!-- Frontend Preview Area -->
        <div class="col-lg-4 d-none d-lg-block">
            <h6 class="text-muted fw-bold mb-3 text-uppercase small"><i class="bi bi-display me-2"></i>Frontend
                Ko'rinishi</h6>
            <div class="card shadow border-0" style="border-radius: 15px; overflow: hidden;">
                <div class="bg-primary pt-4 pb-5 px-4 position-relative text-white text-center">
                    <div class="position-absolute end-0 top-0 mt-3 me-3"><i class="bi bi-award fs-3 opacity-50"></i>
                    </div>
                    <div class="bg-white text-primary rounded-circle d-flex justify-content-center align-items-center mx-auto mb-3 fw-bold fs-4 shadow-sm"
                        style="width: 60px; height: 60px; line-height: 1;">
                        <span id="preview-letter">D</span>
                    </div>
                    <h5 class="fw-bold mb-1" id="preview-name">Dasturiy Injiniring</h5>
                    <span class="badge bg-white text-primary rounded-pill mb-2 px-3" id="preview-code">60610100</span>
                </div>
                <!-- Arch overlay matching Frontend portal styling -->
                <div class="bg-white rounded-top" style="margin-top: -20px; padding: 25px;">
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="text-muted small">O'qish muddati</span>
                        <span class="fw-bold" id="preview-duration">4 yil</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="text-muted small">Kontrakt (yiliga)</span>
                        <span class="fw-bold text-success" id="preview-fee">15 000 000 UZS</span>
                    </div>
                    <button class="btn btn-outline-primary w-100 fw-bold border-2" disabled>Tanlash</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-tabs-custom .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: #6c757d;
    }

    .nav-tabs-custom .nav-link:hover {
        color: #212529;
    }

    .nav-tabs-custom .nav-link.active {
        border-bottom-color: #0d6efd;
        background: transparent;
        color: #0d6efd !important;
    }
</style>

<?php
// Inject Sortable
$this->registerJsFile('https://code.jquery.com/ui/1.13.2/jquery-ui.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$js = <<<JS
// Sortable Logic
$(function() {
    $("#sortable-subjects").sortable({
        placeholder: "list-group-item bg-light border-dashed",
        cursor: "move"
    });
    $("#sortable-subjects").disableSelection();
});

// Remove subject
$(document).on('click', '.remove-sub', function() {
    $(this).closest('li').remove();
});

// Add subject
$('#add-sub-btn').on('click', function() {
    let sel = $('#new-subject-select');
    let val = sel.val();
    let text = sel.find("option:selected").text();
    
    if(!val) return;
    
    // Check if exists
    if($('#sortable-subjects input[value="'+val+'"]').length > 0) {
        alert("Bu fan allaqachon qo'shilgan!"); return;
    }
    
    $('#sortable-subjects').append(`
        <li class="list-group-item d-flex justify-content-between align-items-center py-3 bg-warning bg-opacity-10" style="cursor: move;">
            <div><i class="bi bi-grid-3x3-gap text-muted me-3"></i><span class="fw-bold">\${text}</span></div>
            <input type="hidden" name="Subjects[]" value="\${val}">
            <button type="button" class="btn btn-sm btn-light text-danger remove-sub"><i class="bi bi-x-lg"></i></button>
        </li>
    `);
    
    sel.val(''); // reset
});

// Real-time Visual Preview syncing mapped logically
function syncPreview() {
    let name = $('#direction-name_uz').val();
    let code = $('#direction-code').val();
    let duration = $('#direction-duration_years').val();
    let fee = $('#direction-tuition_fee').val();
    
    if(name) {
        $('#preview-name').text(name);
        $('#preview-letter').text(name.charAt(0).toUpperCase());
    }
    if(code) $('#preview-code').text(code);
    if(duration) $('#preview-duration').text(duration + ' yil');
    if(fee) {
        // Native vanilla format mapping
        $('#preview-fee').text(parseInt(fee).toLocaleString('ru-RU') + ' UZS');
    }
}

$('#direction-form input').on('input', syncPreview);
// Run once on load
setTimeout(syncPreview, 100);
JS;

$this->registerJs($js);
?>