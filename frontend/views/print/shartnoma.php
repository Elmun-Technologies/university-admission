<?php

use yii\helpers\Html;

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Talabalar Kvartirasi Shartnomasi</title>
    <style>
        body { font-family: dejavusans, serif; font-size: 12px; line-height: 1.5; text-align: justify; }
        .header { text-align: center; margin-bottom: 20px; }
        .title { font-size: 16px; font-weight: bold; margin-bottom: 15px; text-transform: uppercase; }
        .parties { font-weight: bold; margin-bottom: 15px; }
        .section-title { font-weight: bold; text-align: center; margin-top: 20px; margin-bottom: 10px; }
        .signature-box { width: 100%; margin-top: 50px; }
        .signature-box td { width: 50%; vertical-align: top; }
        .party-name { font-weight: bold; text-align: center; margin-bottom: 15px; }
    </style>
</head>
<body>

    <div class="header">
        <div class="title">Ta'lim xizmatlarini ko'rsatish bo'yicha <br> SHARTNOMA № <?= str_pad($student->id, 6, '0', STR_PAD_LEFT) ?></div>
        <div>Toshkent sh. <span style="float:right;">"___" ________ 20__ y.</span></div>
    </div>

    <p style="text-indent: 30px;">
        Nizom asosida ish yurituvchi, bundan buyon "Ta'lim muassasasi" deb yuritiladigan "Beruniy Universiteti" MChJ nomidan Rektor bir tomondan, va bundan buyon "Talaba" deb yuritiladigan <strong><?= Html::encode($student->getFullName()) ?></strong> ikkinchi tomondan, quyidagilar haqida mazkur shartnomani tuzdilar:
    </p>

    <div class="section-title">1. SHARTNOMA PREDMETI</div>
    <p style="text-indent: 30px;">
        1.1. Ta'lim muassasasi Talabani <strong><?= Html::encode($student->direction->name_uz ?? '') ?></strong> yo'nalishi bo'yicha <strong><?= Html::encode($student->eduForm->name_uz ?? '') ?></strong> ta'lim shaklida o'qitish majburiyatini oladi.
    </p>
    <p style="text-indent: 30px;">
        1.2. Talaba ta'lim muassasasining ichki tartib-qoidalariga rioya qilish va ta'lim xizmatlari uchun belgilangan to'lovni o'z vaqtida amalga oshirish majburiyatini oladi.
    </p>

    <div class="section-title">2. TO'LOV MIQDORI VA SHARTLARI</div>
    <p style="text-indent: 30px;">
        2.1. Bir o'quv yili uchun ta'lim xizmatlari narxi O'zbekiston Respublikasi Vazirlar Mahkamasi tomonidan belgilangan bazaviy hisoblash miqdoriga asosan belgilanadi (Yoki ta'sischilar qarori bilan).
    </p>
    <p style="text-indent: 30px;">
        2.2. To'lov shartnoma imzolangan kundan boshlab bank orqali, plastik karta yoxud elektron to'lov tizimlari (Payme, Click va h.k.) orqali amalga oshiriladi. To'lov 100% yoki semestrlar bo'yicha bo'lib to'lanishi mumkin.
    </p>

    <div class="section-title">3. TOMONLARNING HUQUQ VA MAJBURIYATLARI</div>
    <p style="text-indent: 30px;">
        3.1. Ta'lim muassasasi Dasturlar asosida yuqori malakali o'qituvchilarni jalb qilgan holda sifatli ta'lim berish, talabaning bilim olishi uchun zarur shart-sharoitlarni yaratish majburiyatini oladi.
    </p>
    <p style="text-indent: 30px;">
        3.2. Talaba darslarga uzilishsiz qatnashish, o'tilgan materiallarni o'zlashtirish va muassasa mulkiga ehtiyotkorona munosabatda bo'lish majburiyatini oladi.
    </p>

    <div class="section-title">4. QO'SHIMCHA SHARTLAR VA NIZOLARNI HAL QILISH</div>
    <p style="text-indent: 30px;">
        4.1. Mazkur shartnoma imzolangan kundan boshlab kuchga kiradi va tomonlar o'z majburiyatlarini to'liq bajarguniga qadar amal qiladi.
    </p>
    <p style="text-indent: 30px;">
        4.2. Shartnomani bajarish davrida yuzaga keladigan barcha nizolar muloqot orqali hal etiladi. Kelishuvga erishilmagan taqdirda, O'zbekiston Respublikasi qonunchiligiga muvofiq tegishli iqtisodiy sud orqali hal etiladi.
    </p>

    <!-- Signatures -->
    <table class="signature-box">
        <tr>
            <td style="padding-right: 20px;">
                <div class="party-name">TA'LIM MUASSASASI</div>
                <p><strong>"Beruniy Universiteti" MChJ</strong><br>
                Manzil: Toshkent sh., Chilonzor t.<br>
                STIR: 123456789<br>
                MFO: 00014<br>
                H/R: 20208000000000000001<br>
                Xizmat ko'rsatuvchi bank: AJ "Xalq Banki"</p>
                <p>Rektor: ________________ (M.O'.)</p>
            </td>
            <td style="padding-left: 20px;">
                <div class="party-name">TALABA</div>
                <p><strong>F.I.O.:</strong> <?= Html::encode($student->getFullName()) ?><br>
                <strong>Manzil:</strong> <?= Html::encode($student->address) ?><br>
                <strong>Pasport:</strong> <?= Html::encode($student->passport_series . $student->passport_number) ?><br>
                <strong>Berilgan sana:</strong> <?= Html::encode($student->passport_given_date) ?><br>
                <strong>Kim tomonidan berilgan:</strong> <?= Html::encode($student->passport_given_by) ?><br>
                <strong>JSHSHIR (PINFL):</strong> <?= Html::encode($student->pinfl) ?><br>
                <strong>Telefon:</strong> <?= Html::encode($student->phone) ?></p>
                <p>Imzo: ________________</p>
            </td>
        </tr>
    </table>

</body>
</html>
