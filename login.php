<?php
require_once __DIR__ . '/includes/config.php';

if (isAuthenticated()) {
    header('Location: profile.php');
    exit;
}

$error = '';
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login    = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($login === ADMIN_LOGIN && $password === ADMIN_PASS) {
        $_SESSION['admin'] = true;
        header('Location: admin.php');
        exit;
    }

    $user = authenticateUser($login, $password);
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: profile.php');
        exit;
    } else {
        $error = 'Неверный логин или пароль';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход — Учусь.РФ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="page-loader" id="pageLoader"><div class="loader-spinner"></div></div>
    <div class="container-fluid min-vh-100 d-flex p-0">
        <div class="row g-0 w-100">
            <div class="col-lg-7 d-none d-lg-flex align-items-center justify-content-center bg-primary position-relative overflow-hidden">
                <div class="text-center text-white p-5 position-relative z-1">
                    <i class="bi bi-mortarboard-fill display-1 mb-3"></i>
                    <h2 class="fw-bold display-6">Учусь.РФ</h2>
                    <p class="lead opacity-75">Портал дистанционного обучения</p>
                    <p class="opacity-75">Повышение квалификации, переподготовка, охрана труда</p>
                </div>
                <div class="position-absolute bottom-0 start-0 w-100 h-100 opacity-10" style="background: repeating-linear-gradient(45deg, transparent, transparent 40px, rgba(255,255,255,0.1) 40px, rgba(255,255,255,0.1) 80px);"></div>
            </div>
            <div class="col-lg-5 d-flex align-items-center justify-content-center bg-white p-4">
                <div class="w-100" style="max-width:400px;">
                    <div class="text-center mb-4 d-lg-none">
                        <i class="bi bi-mortarboard-fill display-4 text-primary"></i>
                        <h3 class="fw-bold">Учусь.РФ</h3>
                    </div>
                    <h4 class="fw-bold mb-1">Добро пожаловать</h4>
                    <p class="text-muted mb-4">Войдите в свой аккаунт</p>

                    <?php if ($flash): ?>
                        <div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($flash) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="post" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Логин</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="login" class="form-control" placeholder="Введите логин" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Пароль</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="Введите пароль" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2"><i class="bi bi-box-arrow-in-right"></i> Войти</button>
                    </form>

                    <div class="text-center mt-4">
                        <a href="register.php">Ещё не зарегистрированы? Регистрация</a>
                    </div>
                    <div class="text-center mt-2">
                        <a href="index.php" class="text-muted small">На главную</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
