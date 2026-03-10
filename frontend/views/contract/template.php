<?php

/** @var $data array Flattened data prepared by Model */
use yii\helpers\Html;

?>
<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8">
    <title>Contract
        <?= Html::encode($data['contract_number']) ?>
    </title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 14pt;
            line-height: 1.5;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 2cm;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: bold;
        }

        .mb-4 {
            margin-bottom: 20px;
        }

        .mb-2 {
            margin-bottom: 10px;
        }

        .mt-5 {
            margin-top: 40px;
        }

        .flex-between {
            display: flex;
            justify-content: space-between;
        }

        .stamp-box {
            border: 1px solid #000;
            padding: 20px;
            text-align: center;
            color: #10b981;
            font-weight: bold;
            border-radius: 5px;
            width: 250px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <button class="no-print" onclick="window.print()"
        style="padding: 10px 20px; background: #3b82f6; color: white; border: none; border-radius: 5px; cursor: pointer; margin-bottom: 20px;">
        Chop etish (Print)
    </button>

    <div class="text-center mb-4">
        <h2 class="fw-bold">SHARTNOMA №
            <?= Html::encode($data['contract_number']) ?>
        </h2>
        <p>Oliy Ta'lim Muassasasiga O'qishga Qabul Qilish To'g'risida</p>
    </div>

    <div class="flex-between mb-4">
        <div>Toshkent shahri</div>
        <div>
            <?= Html::encode($data['created_at']) ?>
        </div>
    </div>

    <p style="text-indent: 1.5cm; text-align: justify;">
        Bir tomondan <b>
            <?= Html::encode($data['branch']) ?>
        </b> (keyingi o'rinlarda "Ta'lim muassasasi" deb nomlanadi) va ikkinchi tomondan fuqaro <b>
            <?= Html::encode($data['full_name']) ?>
        </b> (keyingi o'rinlarda "Talaba" deb nomlanadi) ushbu ommaviy ofertani imzolab hujjat kuchga kirishini
        tasdiqlashdi.
    </p>

    <h4 class="fw-bold mt-5 mb-2">I. TO'LOV VA TA'LIM SHARTLARI</h4>
    <p>1.1. Belgilangan yo'nalish: <b>
            <?= Html::encode($data['direction']) ?>
        </b></p>
    <p>1.2. Muvaffaqiyatli qabul qilingan abiturient uchun yillik ta'lim to'lovi: <b>
            <?= Html::encode($data['amount']) ?> UZS
        </b></p>

    <div class="flex-between mt-5 pt-5" style="border-top: 1px solid #000;">
        <div style="width: 45%;">
            <p class="fw-bold mb-2">"Ta'lim muassasasi"</p>
            <p>
                <?= Html::encode($data['branch']) ?>
            </p>
            <br>
            <p>M.O'. / Imzo: ________________</p>
        </div>
        <div style="width: 45%;">
            <p class="fw-bold mb-2">"Talaba"</p>
            <p>F.I.Sh:
                <?= Html::encode($data['full_name']) ?>
            </p>
            <p>Pasport:
                <?= Html::encode($data['passport']) ?>
            </p>
            <p>JSHSHIR:
                <?= Html::encode($data['pinfl']) ?>
            </p>

            <div class="stamp-box">
                ELEKTRON IMZOLANDI<br>
                <span style="font-size: 10pt; color: #475569; font-weight: normal;">Sana:
                    <?= Html::encode($data['created_at']) ?>
                </span>
            </div>
        </div>
    </div>
</body>

</html>