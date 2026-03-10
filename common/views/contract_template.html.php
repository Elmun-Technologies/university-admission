<?php
/* @var $oferta common\models\StudentOferta */
/* @var $student common\models\Student */
/* @var $direction common\models\Direction */
/* @var $branch common\models\Branch */
/* @var $eduForm common\models\EduForm */

$sum = number_format($oferta->contract_amount, 0, '', ' ');
$logoUrl = $branch->getLogoUrl() ?? Yii::getAlias('@web/images/logo.png'); // Fallback natively
?>
<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: "dejavusans", sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
        }

        h1,
        h2,
        h3,
        h4,
        h5 {
            text-align: center;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header-tbl {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-justify {
            text-align: justify;
        }

        .mb-2 {
            margin-bottom: 15px;
        }

        .mb-4 {
            margin-bottom: 30px;
        }

        .fw-bold {
            font-weight: bold;
        }

        table.parties {
            width: 100%;
            margin-top: 40px;
        }

        table.parties td {
            width: 50%;
            vertical-align: top;
        }
    </style>
</head>

<body>

    <table class="header-tbl">
        <tr>
            <td style="width: 20%;">
                <!-- Assuming logo exists physically, otherwise fallback -->
                <img src="<?= $logoUrl ?>" height="60" alt="Logo">
            </td>
            <td style="width: 80%; text-align: center;">
                <h2 style="font-size: 14pt; margin: 0;">
                    <?= mb_strtoupper($branch->name_uz ?? 'Oliy Ta\'lim Muassasasi') ?>
                </h2>
                <div style="font-size: 9pt; margin-top: 5px;">
                    <?= $branch->address ?? 'Manzil kiritilmagan' ?>
                </div>
                <div style="font-size: 9pt;">Tel:
                    <?= $branch->phone ?? '+998' ?> | Email: info@university.edu.uz
                </div>
            </td>
        </tr>
    </table>

    <h3 style="margin-top: 20px; margin-bottom: 5px;">OFERTA SHARTNOMA №
        <?= htmlspecialchars($oferta->contract_number) ?>
    </h3>
    <div class="text-center fw-bold" style="font-size: 10pt; color: #555;">Ta'lim xizmatlarini ko'rsatish bo'yicha</div>

    <table style="width: 100%; margin-top: 20px; margin-bottom: 20px;">
        <tr>
            <td style="text-align: left; width: 50%;">Toshkent sh.</td>
            <td style="text-align: right; width: 50%;">
                <?= date('d.m.Y', $oferta->created_at) ?> yil
            </td>
        </tr>
    </table>

    <div class="text-justify mb-2">
        Bir tomondan
        <?= htmlspecialchars($branch->name_uz ?? 'Oliy ta\'lim muassasasi') ?> (keyingi o‘rinlarda "Ta'lim muassasasi"
        deb nomlanadi) rahbari, ikkinchi tomondan <b>
            <?= htmlspecialchars($student->getFullName()) ?>
        </b> (keyingi o‘rinlarda "Talaba" deb nomlanadi) quyidagilar to‘g‘risida ushbu shartnomani tuzdilar:
    </div>

    <h4 class="mb-2">1. Shartnoma Predmeti</h4>
    <div class="text-justify mb-2">
        1.1. Ta'lim muassasasi Talabani <b>
            <?= htmlspecialchars($direction->name_uz ?? '-') ?>
        </b> yo'nalishi bo'yicha <b>
            <?= htmlspecialchars($eduForm->name_uz ?? '-') ?>
        </b> ta'lim shaklida o'qitish majburiyatini oladi.
        <br>
        1.2. O'quv yili uchun to'lov miqdori: <b>
            <?= $sum ?> (
            <?= \Yii::$app->formatter->asSpellout($oferta->contract_amount) ?>) so'm
        </b> etib belgilanadi.
    </div>

    <h4 class="mb-2">2. Tomonlarning Huquq va Majburiyatlari</h4>
    <div class="text-justify mb-2">
        2.1. Ta'lim muassasasi davlat ta'lim standartlariga muvofiq sifatli ta'lim berishi shart.
        <br>
        2.2. Talaba o'quv tartib-qoidalariga qat'iy rioya qilishi va belgilangan to'lovlarni o'z vaqtida amalga
        oshirishi shart.
    </div>

    <h4 class="mb-2">3. To'lov shartlari</h4>
    <div class="text-justify mb-4">
        3.1. Talaba
        <?= $sum ?> so'm to'lovni shartnoma raqami orqali Ta'lim muassasasining hisob raqamiga o'tkazadi.
    </div>

    <table class="parties">
        <tr>
            <td>
                <b>Ta'lim Muassasasi:</b><br>
                <?= htmlspecialchars($branch->name_uz ?? '') ?><br>
                Manzil:
                <?= htmlspecialchars($branch->address ?? '') ?><br>
                STIR: 123456789<br>
                <br><br>
                Imzo: _____________________ (Muhr)
            </td>
            <td style="padding-left: 20px;">
                <b>Talaba:</b><br>
                F.I.SH:
                <?= htmlspecialchars($student->getFullName()) ?><br>
                Pasport:
                <?= htmlspecialchars($student->passport_series . ' ' . $student->passport_number) ?><br>
                JSHSHIR:
                <?= htmlspecialchars($student->pinfl) ?><br>
                <br>
                <b>Raqamli Tarzda Tasdiqlangan</b><br>
                <?php if ($oferta->signed_at) : ?>
                    <span style="border: 1px solid #28a745; color: #28a745; padding: 2px 5px; font-weight: bold;">
                        Tasdiqlandi:
                        <?= date('d.m.Y H:i:s', $oferta->signed_at) ?>
                    </span>
                <?php else : ?>
                    Imzolanmagan
                <?php endif; ?>
            </td>
        </tr>
    </table>

</body>

</html>