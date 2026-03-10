<?php

use yii\helpers\Html;
use common\models\Student;

// Simple modal structure natively pushed
?>
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-arrow-left-right text-primary me-2"></i>Holatni
                    o'zgartirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="statusForm">
                    <input type="hidden" name="student_id" id="status-form-student-id">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
                        value="<?= Yii::$app->request->csrfToken ?>">

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Yangi Holat <span class="text-danger">*</span></label>
                        <select class="form-select border-2" name="new_status" id="new_status_select" required>
                            <option value="">-- Tanlang --</option>
                            <!-- Hardcoded mapping for simplification, ideally populated dynamically based on current student -->
                            <option value="<?= Student::STATUS_NEW ?>">Yangi (New)</option>
                            <option value="<?= Student::STATUS_ANKETA ?>">Anketa To'liq</option>
                            <option value="<?= Student::STATUS_EXAM_SCHEDULED ?>">Imtihon belgilandi</option>
                            <option value="<?= Student::STATUS_EXAM_PASSED ?>">Imtihondan o'tdi</option>
                            <option value="<?= Student::STATUS_EXAM_FAILED ?>">Imtihondan yiqildi</option>
                            <option value="<?= Student::STATUS_CONTRACT_SIGNED ?>">Shartnoma tuzildi</option>
                            <option value="<?= Student::STATUS_PAID ?>">To'lov Qildi</option>
                            <option value="<?= Student::STATUS_REJECTED ?>" class="text-danger">Rad etildi (Qaytarildi)
                            </option>
                        </select>
                        <div class="form-text text-muted">Eslatma: Tizim faqat ruxsat etilgan bosqichlarga o'tishni
                            qabul qiladi.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Izoh (majburiy emas)</label>
                        <textarea class="form-control" name="note" rows="3"
                            placeholder="Masalan: To'lov tekshirildi va tasdiqlandi"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                <button type="button" class="btn btn-primary fw-bold" id="saveStatusBtn">Saqlash</button>
            </div>
        </div>
    </div>
</div>