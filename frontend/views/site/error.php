<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
?>
<div class="site-error p-5 text-center">
    <div class="error-content">
        <h1 class="display-1 fw-bold text-danger"><?= $exception->statusCode ?? 500 ?></h1>
        <h2 class="mb-4"><?= Html::encode($this->title) ?></h2>

        <p class="lead">
            <?= nl2br(Html::encode($message)) ?>
        </p>

        <p class="text-muted">
            The above error occurred while the Web server was processing your request.
            Please contact us if you think this is a server error. Thank you.
        </p>

        <div class="mt-5">
            <a href="/" class="btn btn-primary btn-lg px-5">Go to Homepage</a>
        </div>
    </div>
</div>