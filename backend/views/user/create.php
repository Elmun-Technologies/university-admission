<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\UserForm */

$this->title = 'Yangi Xodim Qo\'shish';
$this->params['breadcrumbs'][] = ['label' => 'Xodimlar', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <!-- Reusing the update.php content mostly as it contains the form logic -->
    <?= $this->render('update', [
        'model' => $model,
    ]) ?>

</div>