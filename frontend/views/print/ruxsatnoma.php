<?php

use yii\helpers\Html;

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Imtihon Ruxsatnomasi</title>
    <style>
        body { font-family: dejavusans, sans-serif; font-size: 14px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .logo { width: 80px; height: 80px; margin-bottom: 10px; }
        .title { font-size: 20px; font-weight: bold; margin-bottom: 5px; }
        .subtitle { font-size: 16px; color: #555; }
        
        .content-box { border: 1px solid #ccc; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        
        table.info-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table.info-table td { padding: 8px; vertical-align: top; border-bottom: 1px dashed #eee; }
        table.info-table td.label { font-weight: bold; width: 40%; color: #333; }
        
        .photo-box { width: 120px; height: 160px; border: 1px solid #999; text-align: center; overflow: hidden; float: right; margin-left: 15px; }
        .photo-box img { width: 100%; height: auto; }
        
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #777; border-top: 1px solid #ccc; padding-top: 10px; }
        
        .qr-code { text-align: center; margin-top: 20px; }
    </style>
</head>
<body>

    <div class="header">
        <div class="title">BERUNIY UNIVERSITETI</div>
        <div class="subtitle">IMTIHONGA RUXSATNOMA</div>
    </div>

    <div class="content-box">
        <?php if ($student->photo) : ?>
        <div class="photo-box">
            <!-- Ensure mPDF can read the absolute path or full URL. We use a relative path trick if webroot available, or base64. 
                 Assuming full URL works better in yii2 advanced for PDF generation if running behind nginx. 
                 Using local relative path if possible -->
            <img src="<?= Yii::getAlias('@frontend/web/') . $student->photo ?>" alt="Photo">
        </div>
        <?php endif; ?>

        <table class="info-table">
            <tr>
                <td class="label">Abituriyent ID:</td>
                <td><strong><?= str_pad($student->id, 6, '0', STR_PAD_LEFT) ?></strong></td>
            </tr>
            <tr>
                <td class="label">F.I.O.:</td>
                <td><?= Html::encode($student->getFullName()) ?></td>
            </tr>
            <tr>
                <td class="label">JSHSHIR (PINFL):</td>
                <td><?= Html::encode($student->pinfl) ?></td>
            </tr>
            <tr>
                <td class="label">Yo'nalish:</td>
                <td><?= Html::encode($student->direction->name_uz ?? 'Noma\'lum') ?></td>
            </tr>
            <tr>
                <td class="label">Ta'lim tili:</td>
                <td><?= Html::encode($student->direction->language->name ?? 'O\'zbek') ?></td>
            </tr>
            <tr>
                <td class="label">Ta'lim shakli:</td>
                <td><?= Html::encode($student->eduForm->name_uz ?? 'Kunduzgi') ?></td>
            </tr>
        </table>
        
        <div style="clear: both;"></div>
    </div>

    <div class="content-box" style="background-color: #f9f9f9;">
        <h3 style="margin-top:0;">Imtihon ma'lumotlari:</h3>
        <table class="info-table">
            <tr>
                <td class="label">Imtihon sanasi:</td>
                <td><strong>15 Avgust, 2026 yil</strong> (Namuna)</td>
            </tr>
            <tr>
                <td class="label">Kirish vaqti:</td>
                <td><strong>08:30</strong> (Kechikkanlar kiritilmaydi)</td>
            </tr>
            <tr>
                <td class="label">Manzil:</td>
                <td>Toshkent sh., Chilonzor tumani, Beruniy ko'chasi, 1-uy. (Asosiy bino)</td>
            </tr>
        </table>
        <p style="color: red; font-size: 13px; margin-top: 15px;">
            <em>Eslatma: Imtihonga kelishda ushbu ruxsatnoma va shaxsingizni tasdiqlovchi hujjat (Pasport/ID karta) bo'lishi shart! Telefon va boshqa aloqa vositalarini olib kirish qat'iyan man etiladi.</em>
        </p>
    </div>

    <div class="qr-code">
        <!-- mPDF supports basic barcode/QR natively if configured, but we can output a generic placeholder or dynamic text -->
        <barcode code="<?= 'ID:' . $student->id . '|PINFL:' . $student->pinfl ?>" type="QR" class="barcode" size="1.2" error="M" disableborder="1" />
        <br><small>Hujjat haqiqiyligini tekshirish uchun QR kod</small>
    </div>

    <div class="footer">
        Hujjat shakllantirilgan vaqt: <?= date('d.m.Y H:i') ?> | Ruxsatnoma ID: <?= md5($student->id . $student->pinfl . time()) ?>
    </div>

</body>
</html>
