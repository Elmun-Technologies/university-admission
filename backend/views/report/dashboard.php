<?php

use yii\helpers\Html;

$this->title = 'Hisobotlar paneli';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-pie-chart text-primary me-2"></i>Yo'nalishlar bo'yicha ulush</h6>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center">
                <canvas id="directionPieChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-building-up text-primary me-2"></i>Konsaltinglar reytingi (Top 5)</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kompaniya</th>
                            <th class="text-center">Abiturientlar</th>
                            <th class="text-end">Komissiya daromadi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($consultingStats as $cs) : ?>
                        <tr>
                            <td class="fw-bold"><?= Html::encode($cs['name']) ?></td>
                            <td class="text-center"><span class="badge bg-primary rounded-pill"><?= $cs['total_students'] ?></span></td>
                            <td class="text-end fw-bold text-success"><?= number_format($cs['total_commission'], 0, '', ' ') ?> UZS</td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($consultingStats)) : ?>
                            <tr><td colspan="3" class="text-center text-muted py-4">Ma'lumot topilmadi</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
// Register CDN Chart.js
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', ['position' => \yii\web\View::POS_HEAD]);

$pieLabels = [];
$pieData = [];
foreach ($directionPie as $dp) {
    $pieLabels[] = $dp['name_uz'];
    $pieData[] = $dp['cnt'];
}
$pieLabelsJson = json_encode($pieLabels);
$pieDataJson = json_encode($pieData);

$js = <<<JS
document.addEventListener('DOMContentLoaded', function() {
    const ctxPie = document.getElementById('directionPieChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: $pieLabelsJson,
            datasets: [{
                data: $pieDataJson,
                backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right' }
            },
            cutout: '65%'
        }
    });
});
JS;

$this->registerJs($js);
?>
