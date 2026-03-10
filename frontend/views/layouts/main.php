<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\AppAsset;
use frontend\widgets\LanguageSwitcher;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

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
            background-color: #f8f9fa;
        }

        .navbar-brand {
            font-weight: 700;
            display: flex;
            align-items: center;
        }

        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .main-container {
            min-height: calc(100vh - 140px);
            margin-top: 30px;
        }

        .footer {
            background-color: #fff;
            border-top: 1px solid #eaeaea;
            padding: 20px 0;
        }
    </style>
</head>

<body class="d-flex flex-column h-100">
    <?php $this->beginBody() ?>

    <div id="global-loader">
        <div class="spinner-custom"></div>
    </div>

    <header>
        <?php
        NavBar::begin([
            'brandLabel' => '🎓 ' . Yii::$app->name,
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar navbar-expand-md navbar-light bg-white fixed-top',
            ],
        ]);

        $menuItems = [
            ['label' => Yii::t('app', 'Bosh sahifa'), 'url' => ['/site/index']],
        ];

        if (Yii::$app->user->isGuest) {
            $menuItems[] = ['label' => Yii::t('app', 'Yordam'), 'url' => ['/help/index']];
            $menuItems[] = ['label' => Yii::t('app', 'Ro\'yxatdan o\'tish'), 'url' => ['/auth/register']];
            $menuItems[] = ['label' => Yii::t('app', 'Kirish'), 'url' => ['/auth/login'], 'options' => ['class' => 'fw-bold text-primary']];
        } else {
            $menuItems[] = ['label' => Yii::t('app', 'Yordam'), 'url' => ['/help/index']];
            $menuItems[] = ['label' => Yii::t('app', 'Mening arizam'), 'url' => ['/dashboard/index']];
            $menuItems[] = '<li>'
                . Html::beginForm(['/auth/logout'], 'post', ['class' => 'd-flex'])
                . Html::submitButton(
                    Yii::t('app', 'Chiqish') . ' (' . Yii::$app->user->identity->first_name . ')',
                    ['class' => 'btn btn-link logout text-decoration-none text-danger']
                )
                . Html::endForm()
                . '</li>';
        }

        echo Nav::widget([
            'options' => ['class' => 'navbar-nav ms-auto mb-2 mb-md-0 d-flex align-items-center'],
            'items' => $menuItems,
        ]);

        // Add Language Switcher
        echo '<div class="ms-md-3 mt-2 mt-md-0">';
        echo LanguageSwitcher::widget();
        echo '</div>';

        NavBar::end();
        ?>
    </header>

    <main role="main" class="flex-shrink-0 main-container">
        <div class="container mt-5 pt-3">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= Alert::widget() ?>

            <!-- Main Content Injection -->
            <?= $content ?>

        </div>
    </main>

    <footer class="footer mt-auto py-3 text-muted">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <span class="fw-bold">&copy;
                        <?= Html::encode(Yii::$app->name) ?>
                        <?= date('Y') ?>
                    </span><br>
                    <small>Barcha huquqlar himoyalangan / Все права защищены</small>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="d-flex justify-content-center justify-content-md-end gap-3">
                        <a href="#" class="text-decoration-none text-muted">📞 </a>
                        <a href="#" class="text-decoration-none text-muted">✈️ Telegram</a>
                        <a href="#" class="text-decoration-none text-muted">📸 Instagram</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage();
