<?php

/** @var \yii\web\View $this */
/** @var string $content */

use backend\assets\AppAsset;
use common\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);

$user = Yii::$app->user->identity;
$branchName = $user ? ($user->branch->name_uz ?? 'Boshqarv Markazi') : 'Tizim';
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <title>
        <?= Html::encode($this->title) ?> - University Admission System
    </title>
    <!-- Add Bootstrap Icons CDN natively for the sidebar mapping -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <?php $this->head() ?>
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #f8f9fa;
        }

        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
            min-height: 100vh;
        }

        #sidebar {
            min-width: 250px;
            max-width: 250px;
            background: #212529;
            color: #fff;
            transition: all 0.3s;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }

        #sidebar.active {
            margin-left: -250px;
        }

        #sidebar .sidebar-header {
            padding: 20px;
            background: #1a1e21;
            border-bottom: 1px solid #343a40;
            text-align: center;
        }

        #sidebar ul.components {
            padding: 20px 0;
            border-bottom: 1px solid #343a40;
            flex: 1;
        }

        #sidebar ul li a {
            padding: 12px 20px;
            font-size: 1.05rem;
            display: flex;
            align-items: center;
            color: #adb5bd;
            text-decoration: none;
            transition: 0.2s;
        }

        #sidebar ul li a:hover,
        #sidebar ul li.active>a {
            color: #fff;
            background: #343a40;
            border-left: 4px solid #0d6efd;
        }

        #sidebar ul li a i {
            margin-right: 15px;
            font-size: 1.2rem;
        }

        #content {
            width: 100%;
            min-height: 100vh;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }

        .top-navbar {
            background: #fff;
            padding: 10px 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 999;
        }

        .navbar-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #495057;
        }

        /* Notification Bell wrapper */
        .notification-bell {
            position: relative;
            cursor: pointer;
            padding: 5px 10px;
        }

        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            font-size: 0.65rem;
            padding: 3px 6px;
            transform: translate(25%, -25%);
            border-radius: 50%;
        }

        .notification-dropdown {
            width: 320px;
            max-height: 400px;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 0;
        }

        .main-content {
            padding: 30px;
            flex: 1;
        }

        @media (max-width: 768px) {
            #sidebar {
                margin-left: -250px;
                position: fixed;
                height: 100vh;
            }

            #sidebar.active {
                margin-left: 0;
            }
        }
    </style>
</head>

<body class="d-flex flex-column h-100">
    <?php $this->beginBody() ?>

    <div class="wrapper">
        <!-- Sidebar  -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h4 class="mb-0 text-white fw-bold"><i class="bi bi-mortarboard text-primary"></i> Admission</h4>
                <div class="small text-muted mt-2 text-truncate" title="<?= Html::encode($branchName) ?>">
                    <?= Html::encode($branchName) ?>
                </div>
            </div>

            <ul class="list-unstyled components">
                <li class="<?= Yii::$app->controller->id == 'dashboard' ? 'active' : '' ?>">
                    <a href="<?= \yii\helpers\Url::to(['/dashboard/index']) ?>"><i class="bi bi-house-door"></i>
                        Dashboard</a>
                </li>

                <?php if (Yii::$app->user->can('viewStudent') || Yii::$app->user->id === 1): // Failsafe SuperAdmin ID 1 check ?>
                    <li class="<?= Yii::$app->controller->id == 'student' ? 'active' : '' ?>">
                        <a href="<?= \yii\helpers\Url::to(['/student/index']) ?>"><i class="bi bi-people"></i> Talabalar
                            (Students)</a>
                    </li>
                <?php endif; ?>

                <?php if (Yii::$app->user->can('manageDirection') || Yii::$app->user->id === 1): ?>
                    <li class="<?= Yii::$app->controller->id == 'direction' ? 'active' : '' ?>">
                        <a href="<?= \yii\helpers\Url::to(['/direction/index']) ?>"><i class="bi bi-journal-bookmark"></i>
                            Yo'nalishlar (Directions)</a>
                    </li>
                <?php endif; ?>

                <?php if (Yii::$app->user->can('manageExam') || Yii::$app->user->id === 1): ?>
                    <li class="<?= Yii::$app->controller->id == 'exam' ? 'active' : '' ?>">
                        <a href="<?= \yii\helpers\Url::to(['/exam/index']) ?>"><i class="bi bi-calendar-check"></i>
                            Imtihonlar (Exams)</a>
                    </li>
                <?php endif; ?>

                <?php if (Yii::$app->user->can('viewContract') || Yii::$app->user->id === 1): ?>
                    <li class="<?= Yii::$app->controller->id == 'contract' ? 'active' : '' ?>">
                        <a href="<?= \yii\helpers\Url::to(['/contract/index']) ?>"><i class="bi bi-file-earmark-text"></i>
                            Shartnomalar (Contracts)</a>
                    </li>
                <?php endif; ?>

                <?php if (Yii::$app->user->can('managePayment') || Yii::$app->user->id === 1): ?>
                    <li class="<?= Yii::$app->controller->id == 'payment' ? 'active' : '' ?>">
                        <a href="<?= \yii\helpers\Url::to(['/payment/index']) ?>"><i class="bi bi-wallet2"></i> To'lovlar
                            (Payments)</a>
                    </li>
                <?php endif; ?>

                <?php if (Yii::$app->user->can('manageConsulting') || Yii::$app->user->id === 1): ?>
                    <li class="<?= Yii::$app->controller->id == 'consulting' ? 'active' : '' ?>">
                        <a href="<?= \yii\helpers\Url::to(['/consulting/index']) ?>"><i class="bi bi-building"></i>
                            Konsalting (Agencies)</a>
                    </li>
                <?php endif; ?>

                <?php if (Yii::$app->user->can('viewReport') || Yii::$app->user->id === 1): ?>
                    <li class="<?= Yii::$app->controller->id == 'report' ? 'active' : '' ?>">
                        <a href="<?= \yii\helpers\Url::to(['/report/dashboard']) ?>"><i class="bi bi-graph-up"></i>
                            Hisobotlar (Reports)</a>
                    </li>
                <?php endif; ?>

                <?php if (Yii::$app->user->can('superAdmin') || Yii::$app->user->id === 1): ?>
                    <li
                        class="<?= Yii::$app->controller->id == 'settings' ? 'active' : '' ?> mt-4 border-top pt-3 border-secondary">
                        <a href="<?= \yii\helpers\Url::to(['/settings/university']) ?>"><i class="bi bi-gear"></i>
                            Sozlamalar (Settings)</a>
                    </li>
                <?php endif; ?>
            </ul>

            <div class="p-4 small text-muted text-center mt-auto">
                &copy; University Admission System
                <?= date('Y') ?><br>v1.0.0
            </div>
        </nav>

        <!-- Page Content  -->
        <div id="content">
            <!-- Top Navbar -->
            <div class="top-navbar">
                <div class="d-flex align-items-center">
                    <button type="button" id="sidebarCollapse" class="navbar-btn me-3 d-md-none">
                        <i class="bi bi-list"></i>
                    </button>
                    <h5 class="mb-0 fw-bold text-dark d-none d-md-block">
                        <?= Html::encode($this->title) ?>
                    </h5>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <!-- Notifications Dropdown mapping logically to NotificationController API -->
                    <div class="dropdown">
                        <div class="notification-bell text-secondary" id="notifDropdown" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="bi bi-bell fs-5"></i>
                            <span class="badge bg-danger notification-badge d-none" id="notif-count">0</span>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end notification-dropdown p-0"
                            aria-labelledby="notifDropdown">
                            <li class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold">Bildirishnomalar</h6>
                                <button class="btn btn-sm btn-link text-decoration-none p-0"
                                    id="mark-all-read">Barchasini o'qish</button>
                            </li>
                            <div id="notif-list" class="list-group list-group-flush">
                                <div class="text-center p-4 text-muted small">
                                    <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                                    <br>Yuklanmoqda...
                                </div>
                            </div>
                        </ul>
                    </div>

                    <!-- User Profile -->
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle"
                            id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-2 shadow-sm"
                                style="width: 35px; height: 35px; font-weight: bold;">
                                <?= $user ? strtoupper(substr($user->first_name, 0, 1)) : 'A' ?>
                            </div>
                            <span class="d-none d-md-inline fw-bold">
                                <?= $user ? Html::encode($user->first_name . ' ' . $user->last_name) : 'Admin' ?>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="dropdownUser1">
                            <li><a class="dropdown-item" href="<?= \yii\helpers\Url::to(['/site/profile']) ?>"><i
                                        class="bi bi-person me-2"></i> Profil</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'd-flex']) ?>
                                <?= Html::submitButton('<i class="bi bi-box-arrow-right me-2"></i> Tizimdan chiqish', ['class' => 'dropdown-item text-danger fw-bold']) ?>
                                <?= Html::endForm() ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="main-content">
                <?= Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    'options' => ['class' => 'breadcrumb bg-transparent p-0 mb-4']
                ]) ?>
                <?= Alert::widget() ?>
                <?= $content ?>
            </div>
        </div>
    </div>

    <?php
    // Native JS handlers for sidebar toggle and notification polling
    $this->registerJs("
    // Sidebar physical toggle
    document.getElementById('sidebarCollapse')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('active');
    });

    // Notification Engine mapped to AJAX endpoints
    const notifCountBadge = document.getElementById('notif-count');
    const notifList = document.getElementById('notif-list');
    
    function fetchNotifications() {
        fetch('/notification/get-unread')
            .then(res => res.json())
            .then(data => {
                if(data.unreadCount > 0) {
                    notifCountBadge.textContent = data.unreadCount;
                    notifCountBadge.classList.remove('d-none');
                } else {
                    notifCountBadge.classList.add('d-none');
                }
                
                notifList.innerHTML = '';
                if(data.notifications && data.notifications.length > 0) {
                    data.notifications.forEach(n => {
                        let icon = 'bi-info-circle text-primary';
                        if(n.type === 'new_student') icon = 'bi-person-plus text-success';
                        else if(n.type === 'payment_received') icon = 'bi-cash-coin text-warning';
                        else if(n.type === 'exam_done') icon = 'bi-check2-square text-info';
                        
                        const item = document.createElement('a');
                        item.href = n.link ? n.link : '#';
                        item.className = 'list-group-item list-group-item-action py-3 ' + (n.is_read == 0 ? 'bg-primary bg-opacity-10' : '');
                        item.innerHTML = `
                            <div class=\"d-flex w-100 align-items-center\">
                                <i class=\"bi \${icon} fs-4 me-3\"></i>
                                <div>
                                    <h6 class=\"mb-1 small fw-bold\">\${n.title}</h6>
                                    <p class=\"mb-1 small text-muted\" style=\"line-height:1.3\">\${n.message}</p>
                                    <small class=\"text-primary\" style=\"font-size:0.7rem\">⏳ \${n.time_ago}</small>
                                </div>
                            </div>
                        `;
                        
                        // Mark read on click natively
                        item.addEventListener('click', () => {
                            if(n.is_read == 0) {
                                fetch('/notification/mark-read?id=' + n.id);
                            }
                        });
                        
                        notifList.appendChild(item);
                    });
                } else {
                    notifList.innerHTML = '<div class=\"p-4 text-center text-muted small\"><i class=\"bi bi-bell-slash fs-3 d-block mb-2\"></i>Bildirishnomalar yo\'q</div>';
                }
            }).catch(err => console.error('Notification sync disabled or failed'));
    }

    // Initial load and subsequent 30s heartbeat polling
    setTimeout(fetchNotifications, 1000); // 1s delay on load
    setInterval(fetchNotifications, 30000);

    // Mark all read listener
    document.getElementById('mark-all-read')?.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        fetch('/notification/mark-all-read').then(() => fetchNotifications());
    });
");
    ?>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage();
