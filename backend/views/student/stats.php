<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $organized array */
/* @var $dirMap array */
/* @var $statuses array */

$this->title = 'Abiturientlar Statistikasi';
$this->params['breadcrumbs'][] = ['label' => 'Abiturientlar', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-stats">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Qabul Statistikasi (Yo'nalishlar kesimida)</h5>
            <?= Html::a('<i class="bi bi-arrow-left"></i> Ro\'yxatga qaytish', ['index'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Yo'nalish</th>
                            <?php foreach ($statuses as $statusId => $label) : ?>
                                <th class="text-center" style="font-size: 13px;"><?= Html::encode($label) ?></th>
                            <?php endforeach; ?>
                            <th class="text-center bg-primary text-white">Jami</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $grandTotal = 0;
                        $statusTotals = array_fill_keys(array_keys($statuses), 0);

                        foreach ($dirMap as $dirId => $dirName) :
                            $dirTotal = 0;
                            ?>
                        <tr>
                            <td class="fw-bold"><?= Html::encode($dirName) ?></td>
                            <?php foreach ($statuses as $statusId => $label) :
                                $count = $organized[$dirId][$statusId] ?? 0;
                                $dirTotal += $count;
                                $statusTotals[$statusId] += $count;
                                ?>
                                <td class="text-center"><?= $count > 0 ? ('<span class="badge bg-secondary">' . $count . '</span>') : '<span class="text-muted">-</span>' ?></td>
                            <?php endforeach; ?>
                            <td class="text-center fw-bold bg-light"><?= $dirTotal ?></td>
                        </tr>
                            <?php
                            $grandTotal += $dirTotal;
                        endforeach;
                        ?>
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <th>UMUMIY JAMI</th>
                            <?php foreach ($statuses as $statusId => $label) : ?>
                                <th class="text-center"><?= $statusTotals[$statusId] ?></th>
                            <?php endforeach; ?>
                            <th class="text-center fs-5"><?= $grandTotal ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white text-muted small text-end">
            Ma'lumotlar joriy vaqt holatiga shakllantirildi: <?= date('d.m.Y H:i:s') ?>
        </div>
    </div>
</div>
