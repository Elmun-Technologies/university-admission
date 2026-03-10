<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\AppAsset;
use frontend\widgets\LanguageSwitcher;
use yii\bootstrap5\Html;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title>
        <?= Html::encode($this->title) ?>
    </title>
    <?php $this->head() ?>
    <style>
        body {
            font-family: 'Inter', 'Roboto', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .auth-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: none;
        }

        .auth-header {
            background: #fff;
            padding: 30px;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
        }

        .auth-body {
            padding: 40px;
        }

        .lang-floater {
            position: absolute;
            top: 20px;
            right: 20px;
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center h-100">
    <?php $this->beginBody() ?>

    <div class="lang-floater">
        <?= LanguageSwitcher::widget() ?>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <?= Alert::widget() ?>
                <div class="card auth-card">
                    <div class="auth-header">
                        <h2 class="mb-0 fw-bold text-primary">🎓
                            <?= Html::encode(Yii::$app->name) ?>
                        </h2>
                        <p class="text-muted mt-2 mb-0">Qabul Tizimi / Система Приема</p>
                    </div>
                    <div class="card-body auth-body">
                        <?= $content ?>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="<?= \yii\helpers\Url::to(['/site/index']) ?>" class="text-muted text-decoration-none">
                        &larr;
                        <?= Yii::t('app', 'Bosh sahifaga qaytish') ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage();
