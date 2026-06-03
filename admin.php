<?php
require_once __DIR__ . '/includes/config.php';

if (!isset($_SESSION['admin'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $login    = $_POST['login'] ?? '';
        $password = $_POST['password'] ?? '';
        if ($login === ADMIN_LOGIN && $password === ADMIN_PASS) {
            $_SESSION['admin'] = true;
            header('Location: admin.php');
            exit;
        }
        $error = 'Неверные учётные данные администратора';
    }
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Админ-панель — Учусь.РФ</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
    <body class="bg-light">
        <div class="container d-flex align-items-center justify-content-center min-vh-100">
            <div class="card shadow-lg border-0 p-4" style="width:100%;max-width:420px;">
                <div class="text-center mb-4">
                    <h3 class="fw-bold"><i class="bi bi-shield-lock-fill"></i> Администратор</h3>
                </div>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Логин</label>
                        <input type="text" name="login" class="form-control" value="Admin26" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Пароль</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-dark w-100 py-2">Войти в панель</button>
                </form>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
    exit;
}

// Admin authenticated — handle actions
$page    = max(1, (int)($_GET['page'] ?? 1));
$limit   = 5;
$offset  = ($page - 1) * $limit;
$filter  = $_GET['filter'] ?? '';
$sort    = $_GET['sort'] ?? 'created_at';
$order   = $_GET['order'] ?? 'DESC';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_status'])) {
    $appId  = (int) $_POST['app_id'];
    $status = $_POST['status'];
    updateApplicationStatus($appId, $status);
    header('Location: admin.php?page=' . $page . '&filter=' . urlencode($filter) . '&sort=' . $sort . '&order=' . $order);
    exit;
}

$totalApps  = countAllApplications($filter);
$totalPages = max(1, ceil($totalApps / $limit));
$apps       = getAllApplications($filter, $sort, $order, $limit, $offset);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель — Учусь.РФ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="admin.php"><i class="bi bi-shield-lock-fill"></i> Админ-панель</a>
            <div class="d-flex align-items-center">
                <span class="text-light me-3"><i class="bi bi-person"></i> Admin</span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Выход</a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <h4 class="fw-bold mb-3"><i class="bi bi-list-check"></i> Все заявки</h4>

        <div class="row mb-3 g-2">
            <div class="col-auto">
                <select class="form-select" id="filterSelect" onchange="filterChange()">
                    <option value="">Все статусы</option>
                    <option value="Новая" <?= $filter === 'Новая' ? 'selected' : '' ?>>Новая</option>
                    <option value="Идет обучение" <?= $filter === 'Идет обучение' ? 'selected' : '' ?>>Идет обучение</option>
                    <option value="Обучение завершено" <?= $filter === 'Обучение завершено' ? 'selected' : '' ?>>Обучение завершено</option>
                </select>
            </div>
            <div class="col-auto">
                <select class="form-select" id="sortSelect" onchange="filterChange()">
                    <option value="created_at" <?= $sort === 'created_at' ? 'selected' : '' ?>>По дате создания</option>
                    <option value="start_date" <?= $sort === 'start_date' ? 'selected' : '' ?>>По дате начала</option>
                    <option value="status" <?= $sort === 'status' ? 'selected' : '' ?>>По статусу</option>
                </select>
            </div>
            <div class="col-auto">
                <select class="form-select" id="orderSelect" onchange="filterChange()">
                    <option value="DESC" <?= $order === 'DESC' ? 'selected' : '' ?>>По убыванию</option>
                    <option value="ASC" <?= $order === 'ASC' ? 'selected' : '' ?>>По возрастанию</option>
                </select>
            </div>
        </div>

        <?php if (empty($apps)): ?>
            <div class="alert alert-info">Заявок нет.</div>
        <?php else: ?>
            <?php foreach ($apps as $app): ?>
                <div class="card shadow-sm border-0 mb-3 admin-app-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <h6 class="fw-bold mb-1"><?= htmlspecialchars($app['full_name']) ?></h6>
                                <small class="text-muted"><i class="bi bi-person-badge"></i> <?= htmlspecialchars($app['login']) ?></small><br>
                                <small class="text-muted"><i class="bi bi-envelope"></i> <?= htmlspecialchars($app['email']) ?></small><br>
                                <small class="text-muted"><i class="bi bi-telephone"></i> <?= htmlspecialchars($app['phone']) ?></small>
                            </div>
                            <div class="col-md-3">
                                <strong><?= htmlspecialchars($app['course_type']) ?></strong><br>
                                <small class="text-muted"><i class="bi bi-calendar"></i> <?= date('d.m.Y', strtotime($app['start_date'])) ?></small><br>
                                <small class="text-muted"><i class="bi bi-wallet2"></i> <?= htmlspecialchars($app['payment_method']) ?></small>
                            </div>
                            <div class="col-md-2">
                                <span class="badge bg-<?= $app['status'] === 'Новая' ? 'warning' : ($app['status'] === 'Идет обучение' ? 'info' : 'success') ?> fs-6">
                                    <?= htmlspecialchars($app['status']) ?>
                                </span>
                            </div>
                            <div class="col-md-3">
                                <form method="post" class="row g-1">
                                    <input type="hidden" name="app_id" value="<?= $app['id'] ?>">
                                    <div class="col-8">
                                        <select name="status" class="form-select form-select-sm">
                                            <option value="Новая" <?= $app['status'] === 'Новая' ? 'selected' : '' ?>>Новая</option>
                                            <option value="Идет обучение" <?= $app['status'] === 'Идет обучение' ? 'selected' : '' ?>>Идет обучение</option>
                                            <option value="Обучение завершено" <?= $app['status'] === 'Обучение завершено' ? 'selected' : '' ?>>Обучение завершено</option>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <button type="submit" name="change_status" class="btn btn-dark btn-sm w-100">OK</button>
                                    </div>
                                </form>
                                <small class="text-muted"><?= date('d.m.Y', strtotime($app['created_at'])) ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if ($totalPages > 1): ?>
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&filter=<?= urlencode($filter) ?>&sort=<?= $sort ?>&order=<?= $order ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script>
    function filterChange() {
        const f = document.getElementById('filterSelect').value;
        const s = document.getElementById('sortSelect').value;
        const o = document.getElementById('orderSelect').value;
        window.location.href = '?filter=' + encodeURIComponent(f) + '&sort=' + s + '&order=' + o;
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
