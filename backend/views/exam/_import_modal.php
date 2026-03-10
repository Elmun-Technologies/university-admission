<?php

use yii\helpers\Html;

?>
<!-- Import Modal Native Scaffold -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-bottom border-primary border-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-excel text-success me-2"></i>Savollarni
                    Excel orqali yuklash</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 bg-light bg-opacity-50">
                <div class="row">
                    <div class="col-md-5 border-end">
                        <div class="alert alert-primary bg-white shadow-sm small">
                            <strong><i class="bi bi-info-circle me-1"></i> Namunaviy Fayl (Template)</strong><br>
                            Ushbu qolipni yuklab, unga savollarni to'g'ri kiritishingiz shart. Boshqa formatlar rad
                            etiladi. Noto'g'ri qatorlar xato beradi.
                            <?= Html::a('<i class="bi bi-download"></i> Shablonni yuklab olish', ['/files/shablon_savollar.xlsx'], ['class' => 'btn btn-outline-primary btn-sm mt-3 w-100 fw-bold', 'target' => '_blank']) ?>
                        </div>
                    </div>

                    <div class="col-md-7 ps-4">
                        <form id="importForm" enctype="multipart/form-data">
                            <input type="hidden" name="subject_id" id="import_subject_id">
                            <input type="hidden" name="exam_id" id="import_exam_id" value="<?= $exam->id ?? '' ?>">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
                                value="<?= Yii::$app->request->csrfToken ?>">

                            <h6 class="fw-bold mb-3"><span class="text-primary me-2">1.</span>Faylni tanlang</h6>

                            <div class="file-upload-wrapper text-center border rounded p-4 bg-white shadow-sm border-dashed"
                                style="position:relative; cursor:pointer;"
                                onclick="document.getElementById('excel_file').click();">
                                <i class="bi bi-cloud-arrow-up text-primary display-5 mb-2"></i>
                                <h6 class="fw-bold text-dark">Fayl tanlash (Click)</h6>
                                <p class="small text-muted mb-0" id="file_name_display">Faqat .xlsx yoki .xls fayllar
                                </p>
                                <input type="file" name="excel_file" id="excel_file" class="d-none" accept=".xls,.xlsx"
                                    required>
                            </div>

                            <div class="progress mt-4 d-none" id="importProgress" style="height: 15px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                    role="progressbar" style="width: 100%">Tahlil qilinmoqda...</div>
                            </div>

                            <!-- Error container mapping natively -->
                            <div id="importErrors" class="alert alert-danger mt-3 d-none small"
                                style="max-height:150px; overflow-y:auto;"></div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                <button type="button" class="btn btn-success fw-bold px-4" id="runImportBtn"><i
                        class="bi bi-play-circle me-1"></i> Boshlash</button>
            </div>
        </div>
    </div>
</div>

<style>
    .border-dashed {
        border: 2px dashed #0d6efd !important;
    }

    .file-upload-wrapper:hover {
        background-color: #f8f9fa !important;
        border-color: #198754 !important;
    }
</style>

<script>
    // Logic Native mappings injecting UI state changes gracefully
    document.getElementById('excel_file').addEventListener('change', function (e) {
        let name = e.target.files[0] ? e.target.files[0].name : "Faqat .xlsx yoki .xls fayllar";
        document.getElementById('file_name_display').textContent = name;
    });

    document.getElementById('runImportBtn').addEventListener('click', function () {
        let fileInput = document.getElementById('excel_file');
        let subjectId = document.getElementById('import_subject_id').value;

        if (!fileInput.files.length) {
            alert("Iltimos, avval Excel faylini tanlang!"); return;
        }

        let btn = this;
        let progress = document.getElementById('importProgress');
        let errBox = document.getElementById('importErrors');

        btn.disabled = true;
        progress.classList.remove('d-none');
        errBox.classList.add('d-none');
        errBox.innerHTML = '';

        let formData = new FormData(document.getElementById('importForm'));

        fetch('/exam/import-questions', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                progress.classList.add('d-none');
                btn.disabled = false;

                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    errBox.classList.remove('d-none');
                    errBox.innerHTML = `<strong>Xatolik:</strong> \${data.message}<hr class="my-2">`;
                    if (data.errors) {
                        let ul = document.createElement('ul');
                        ul.className = 'mb-0 ps-3';
                        data.errors.forEach(e => {
                            ul.innerHTML += `<li>\${e}</li>`;
                        });
                        errBox.appendChild(ul);
                    }
                }
            })
            .catch(err => {
                progress.classList.add('d-none');
                btn.disabled = false;
                alert("Server xatosi yuz berdi");
            });
    });
</script>