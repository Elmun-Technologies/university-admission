<?php

use yii\helpers\Html;
use common\models\Student;

$this->title = 'Tizim Dashboardi';
$this->params['breadcrumbs'][] = $this->title;

// Extract natively parsed metrics avoiding view logic
$m = $metrics['today'];
?>

<!-- KPI Row -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="card bg-primary text-white shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-uppercase text-white-50 small mb-2">Yangi Arizalar</h6>
                        <h2 class="mb-0 fw-bold">
                            <?= Html::encode($m['new']) ?>
                        </h2>
                    </div>
                    <div class="fs-1 text-white-50"><i class="bi bi-people"></i></div>
                </div>
                <div class="mt-3 small">
                    <span
                        class="<?= $m['new_diff'] >= 0 ? 'text-success bg-white bg-opacity-25' : 'text-danger bg-white bg-opacity-25' ?> rounded px-2 py-1">
                        <i class="bi <?= $m['new_diff'] >= 0 ? 'bi-arrow-up' : 'bi-arrow-down' ?>"></i>
                        <?= number_format(abs($m['new_diff']), 1) ?>%
                    </span> kechagi kunga nisbatan
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card bg-info text-white shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-uppercase text-white-50 small mb-2">Bugungi Imtihonlar</h6>
                        <h2 class="mb-0 fw-bold">
                            <?= Html::encode($m['exams']) ?> ta
                        </h2>
                    </div>
                    <div class="fs-1 text-white-50"><i class="bi bi-laptop"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card bg-success text-white shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-uppercase text-white-50 small mb-2">Tuzilgan Shartnomalar</h6>
                        <h2 class="mb-0 fw-bold">
                            <?= Html::encode($m['contracts']) ?> ta
                        </h2>
                    </div>
                    <div class="fs-1 text-white-50"><i class="bi bi-file-earmark-check"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card bg-warning text-dark shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-uppercase opacity-75 small mb-2 text-dark">Tushum (Bugun)</h6>
                        <h3 class="mb-0 fw-bold">
                            <?= number_format($m['payments'] / 1000000, 1) ?> MLN
                        </h3>
                    </div>
                    <div class="fs-1 opacity-50"><i class="bi bi-cash-stack text-dark"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Charting bounded natively -->
    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-graph-up text-primary me-2"></i>Qabul Trendi (Haftalik)</h6>
            </div>
            <div class="card-body">
                <canvas id="trendChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Funnel Visualization natively rendering simple DOM bars -->
    <div class="col-12 col-xl-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-funnel text-primary me-2"></i>Konversiya Voronkasi</h6>
            </div>
            <div class="card-body py-4">
                <?php
                $labels = ['Yangi Arizalar', 'Anketa To\'liq', 'Imtihon belgilandi', 'Imtihondan o\'tdi', 'Shartnoma tuzildi', 'To\'lov qildi'];
                $colors = ['bg-secondary', 'bg-info', 'bg-primary', 'bg-success', 'bg-warning', 'bg-success'];
                $maxItems = max(max($metrics['funnel']), 1); // prevent / 0
                
                foreach ($metrics['funnel'] as $idx => $val):
                    $pct = ($val / $maxItems) * 100;
                    $class = $colors[$idx] ?? 'bg-primary';
                    ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted fw-bold">
                                <?= $labels[$idx] ?>
                            </span>
                            <span class="fw-bold">
                                <?= $val ?>
                            </span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar <?= $class ?>" role="progressbar" style="width: <?= $pct ?>%;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-calendar-event text-primary me-2"></i>Yaqinlashayotgan
                    Imtihonlar</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Sana</th>
                            <th>Yo'nalish</th>
                            <th>Joylar</th>
                            <th>Holat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($metrics['upcomingExams'])): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Rejalashtirilgan imtihonlar yo'q</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($metrics['upcomingExams'] as $ed): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">
                                            <?= date('d.m.Y', strtotime($ed->exam_date)) ?>
                                        </div>
                                        <small class="text-muted">
                                            <?= date('H:i', strtotime($ed->start_time)) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?= Html::encode($ed->exam->direction->name_uz ?? '-') ?>
                                    </td>
                                    <td>
                                        <!-- Assume a method getRegisteredCount() exists, fallback to max -->
                                        <span class="badge bg-light text-dark border">
                                            <?= $ed->max_participants ?>
                                        </span>
                                    </td>
                                    <td><span class="badge bg-primary">Rejalashtirilgan</span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card shadow-sm border-0 mt-4 mt-lg-0">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-activity text-primary me-2"></i>So'nggi O'zgarishlar</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach ($metrics['recentActivities'] as $act): ?>
                        <div class="list-group-item py-3">
                            <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                <h6 class="mb-0 small fw-bold">
                                    <?= Html::encode($act->getFullName()) ?>
                                </h6>
                                <small class="text-muted" style="font-size:0.7rem">
                                    <?= Yii::$app->formatter->asRelativeTime($act->updated_at) ?>
                                </small>
                            </div>
                            <div>
                                <?= $act->getStatusBadge() ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Register CDN Chart.js
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', ['position' => \yii\web\View::POS_HEAD]);

$trendLabels = json_encode($metrics['trendLabels']);
$trendData = json_encode($metrics['trendData']);

$js = <<<JS
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('trendChart').getContext('2d');
    
    // Dynamic Gradient natively bound
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(13, 110, 253, 0.4)');
    gradient.addColorStop(1, 'rgba(13, 110, 253, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: $trendLabels,
            datasets: [{
                label: 'Arizalar soni',
                data: $trendData,
                backgroundColor: gradient,
                borderColor: '#0d6efd',
                borderWidth: 2,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#0d6efd',
                pointBorderWidth: 2,
                pointRadius: 4,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [2, 4], color: '#f8f9fa' } },
                x: { grid: { display: false } }
            }
        }
    });
});
JS;

$this->registerJs($js);
?>