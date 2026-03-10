<?php
// Exam Engine Main Layout
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Imtihon jarayoni');
// Calculate exact seconds remaining mapped from DB creation time vs set Duration
$elapsed = time() - $attempt->started_at;
$totalSeconds = $attempt->getDuration() * 60;
$remainingSeconds = max(0, $totalSeconds - $elapsed);
?>
<style>
    /* Aggressive Exam Interface CSS overriding global logic mapped heavily to ID closures */
    body {
        background-color: #f0f2f5;
        user-select: none;
        -webkit-user-select: none;
    }

    #exam-viewport {
        max-width: 1200px;
        margin: 0 auto;
        height: 100vh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .exam-header {
        background: #fff;
        padding: 15px 30px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        z-index: 10;
    }

    .timer-badge {
        background: #2c3e50;
        color: #fff;
        font-family: monospace;
        font-size: 1.5rem;
        padding: 8px 15px;
        border-radius: 8px;
        font-weight: bold;
        letter-spacing: 1px;
    }

    .timer-warning {
        background: #e74c3c;
        animation: pulse 1s infinite;
    }

    @keyframes pulse {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.8;
        }

        100% {
            opacity: 1;
        }
    }

    .exam-body {
        display: flex;
        flex: 1;
        overflow: hidden;
    }

    /* Left: Map of Questions */
    .sidebar {
        width: 300px;
        background: #fff;
        border-right: 1px solid #eaeaea;
        overflow-y: auto;
        padding: 20px;
    }

    .q-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
    }

    .q-btn {
        width: 40px;
        height: 40px;
        border-radius: 5px;
        border: 1px solid #cbd5e1;
        background: #f8fafc;
        color: #475569;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: 0.2s;
    }

    .q-btn.active {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
    }

    .q-btn.answered {
        background: #3b82f6;
        color: #fff;
        border-color: #3b82f6;
    }

    /* Right: Current Question Display */
    .content-area {
        flex: 1;
        padding: 40px;
        overflow-y: auto;
        position: relative;
    }

    .question-card {
        background: #fff;
        border-radius: 12px;
        padding: 40px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        display: none;
    }

    .question-card.active {
        display: block;
        animation: fadeIn 0.3s;
    }

    .question-text {
        font-size: 1.25rem;
        color: #1e293b;
        margin-bottom: 30px;
        line-height: 1.6;
    }

    .option-card {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 15px 20px;
        margin-bottom: 15px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        font-size: 1.1rem;
        color: #475569;
    }

    .option-card:hover {
        border-color: #94a3b8;
        background: #f8fafc;
    }

    .option-card.selected {
        border-color: #10b981;
        background: #ecfdf5;
        box-shadow: 0 2px 10px rgba(16, 185, 129, 0.1);
    }

    .option-letter {
        width: 30px;
        height: 30px;
        background: #e2e8f0;
        color: #475569;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-right: 15px;
    }

    .option-card.selected .option-letter {
        background: #10b981;
        color: #fff;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .anti-cheat-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.9);
        z-index: 9999;
        display: none;
        color: white;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
</style>

<!-- Top Global Wrapper Obscuring default auth layout -->
<div
    style="position:fixed; top:0; left:0; width:100vw; height:100vh; background:#f0f2f5; z-index:9000; text-align: left;">
    <div id="exam-viewport">
        <!-- Header -->
        <div class="exam-header">
            <div>
                <h4 class="mb-0 fw-bold text-primary">🎓
                    <?= Html::encode($attempt->exam->direction->name_uz) ?>
                </h4>
                <small class="text-muted">Abiturient:
                    <?= Html::encode($student->getFullName()) ?>
                </small>
            </div>

            <div id="timer" class="timer-badge" data-seconds="<?= $remainingSeconds ?>">00:00:00</div>

            <?= Html::a('Imtihonni yakunlash', ['finish', 'id' => $attempt->id], [
                'class' => 'btn btn-danger',
                'id' => 'btn-force-finish',
                'data' => [
                    'confirm' => 'Haqiqatan ham imtihonni muddatidan oldin yakunlamoqchimisiz? Natija o\'zgartirilmaydi.',
                    'method' => 'post',
                ]
            ]) ?>
        </div>

        <!-- Body -->
        <div class="exam-body">
            <!-- Navigation -->
            <div class="sidebar">
                <h6 class="text-uppercase text-muted fw-bold mb-3 small tracking-wide">SAVOLLAR PANELI</h6>
                <div class="q-grid" id="q-nav-grid">
                    <?php foreach ($answers as $index => $answer):
                        $num = $index + 1;
                        $class = $answer->selected_option_id ? 'answered' : '';
                        $active = $index === 0 ? 'active' : '';
                        ?>
                        <div class="q-btn <?= $class ?> <?= $active ?>" data-target="<?= $answer->question_id ?>">
                            <?= $num ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-4 pt-4 border-top">
                    <div class="d-flex align-items-center mb-2">
                        <div class="q-btn answered" style="width:20px; height:20px; margin-right:10px;"></div>
                        <small>Javob berilgan</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="q-btn" style="width:20px; height:20px; margin-right:10px;"></div> <small>Javob
                            berilmagan</small>
                    </div>
                </div>
            </div>

            <!-- Question Forms Area -->
            <div class="content-area">
                <?php foreach ($answers as $index => $answer):
                    $num = $index + 1;
                    $q = $answer->question;
                    $options = $q->getShuffledOptions(); // Randomize display logically via Model
                    $activeClass = $index === 0 ? 'active' : '';
                    ?>
                    <div class="question-card <?= $activeClass ?>" id="q-card-<?= $q->id ?>">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fw-bold">Savol
                                <?= $num ?> /
                                <?= count($answers) ?>
                            </span>
                        </div>

                        <div class="question-text">
                            <?= Html::encode($q->text) ?>
                        </div>

                        <div class="options-container">
                            <?php $letters = ['A', 'B', 'C', 'D']; ?>
                            <?php foreach ($options as $optIndex => $option):
                                $isSelected = $answer->selected_option_id == $option->id ? 'selected' : '';
                                ?>
                                <div class="option-card <?= $isSelected ?>" data-q-id="<?= $q->id ?>"
                                    data-opt-id="<?= $option->id ?>" data-exam-id="<?= $attempt->id ?>">
                                    <div class="option-letter">
                                        <?= $letters[$optIndex] ?>
                                    </div>
                                    <div class="option-text">
                                        <?= Html::encode($option->text) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="d-flex justify-content-between mt-5 pt-3 border-top">
                            <button class="btn btn-outline-secondary btn-prev" <?= $index === 0 ? 'disabled' : '' ?>><i
                                    class="bi bi-arrow-left"></i> Oldingi</button>
                            <button class="btn btn-primary btn-next" <?= $index === count($answers) - 1 ? 'disabled' : '' ?>>Keyingi <i class="bi bi-arrow-right"></i></button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Anti-cheat overlay -->
<div id="cheat-overlay" class="anti-cheat-overlay shadow-lg">
    <i class="bi bi-exclamation-triangle-fill text-warning mb-3" style="font-size: 5rem;"></i>
    <h2 class="fw-bold mb-3">OGOHLANTIRISH!</h2>
    <p class="fs-5 text-center px-4 mb-4">
        Siz imtihon oynasidan chiqib ketdingiz yoki boshqa oynaga o'tdingiz. <br>
        Ushbu harakat qoida buzilishi hisoblanadi. Agar davom etsa, imtihon avtomatik tarzda bekor qilinadi.
    </p>
    <button class="btn btn-warning btn-lg px-5 fw-bold" id="resume-btn">Imtihonga qaytish</button>
</div>

<?php
// Register External JS Vanilla ES6 module
$this->registerJsFile('@web/js/exam.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>