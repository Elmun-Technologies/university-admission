<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\User;

$this->title = 'Xodimlar va Huquqlar';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-shield-lock text-danger me-2"></i>Tizim Foydalanuvchilari</h5>
            <?= Html::a('<i class="bi bi-person-plus-fill me-1"></i> Yangi qoshish', ['create'], ['class' => 'btn btn-primary btn-sm']) ?>
        </div>

        <div class="card-body p-0">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-hover align-middle mb-0'],
                'layout' => "<div class='table-responsive'>{items}</div><div class='card-footer bg-white border-0 d-flex justify-content-between align-items-center'>{summary}{pager}</div>",
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    [
                        'attribute' => 'username',
                        'format' => 'raw',
                        'value' => function ($model) {
                                        $name = $model->employee ? $model->employee->first_name . ' ' . $model->employee->last_name : '-';
                                        return Html::a('<b>' . $model->username . '</b>', ['update', 'id' => $model->id], ['class' => 'text-decoration-none text-dark d-block']) .
                                            Html::tag('small', $name, ['class' => 'text-muted']);
                        }
                    ],
                    'email:email',
                    [
                        'label' => 'Rol (Huquq)',
                        'format' => 'raw',
                        'value' => function ($model) {
                                        $roles = Yii::$app->authManager->getRolesByUser($model->id);
                                        $str = '';
                            foreach ($roles as $role) {
                                $color = $role->name == 'superAdmin' ? 'danger' : 'primary';
                                $str .= '<span class="badge bg-' . $color . ' me-1">' . $role->description . '</span>';
                            }
                                        return $str ?: '<span class="badge bg-secondary">Biriktirilmagan</span>';
                        }
                    ],
                    [
                        'label' => 'Filial',
                        'value' => function ($model) {
                                        return $model->branch ? $model->branch->name_uz : 'Barcha (Global)';
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => function ($model) {
                                        return $model->status == User::STATUS_ACTIVE ?
                                            '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Faol</span>' :
                                            '<span class="badge bg-danger">Bloklangan</span>';
                        }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{update} {delete}',
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                            return Html::a('<i class="bi bi-pencil"></i>', $url, ['class' => 'btn btn-sm btn-light border text-primary me-1']);
                            },
                            'delete' => function ($url, $model, $key) {
                                if ($model->id === 1) {
                                    return '';
                                }
                                            return Html::a('<i class="bi bi-trash"></i>', $url, [
                                                'class' => 'btn btn-sm btn-light border text-danger',
                                                'data' => ['confirm' => 'O\'chirmoqchimisiz?', 'method' => 'post']
                                            ]);
                            },
                        ]
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>