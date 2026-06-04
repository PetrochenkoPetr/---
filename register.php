<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

if (isAuthenticated()) {
    header('Location: profile.php');
    exit;
}

$errors = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login    = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $fullName = trim($_POST['full_name'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $old      = compact('login', 'fullName', 'phone', 'email');

    $errLogin    = validateLogin($login);
    $errPassword = validatePassword($password);

    if ($errLogin) $errors['login'] = $errLogin;
    if ($errPassword) $errors['password'] = $errPassword;
    if (empty($fullName)) $errors['full_name'] = 'Укажите ФИО';
    if (empty($phone)) $errors['phone'] = 'Укажите телефон';
    if (empty($email)) $errors['email'] = 'Укажите e-mail';

    if (empty($errors) && loginExists($login)) {
        $errors['login'] = 'Логин уже занят';
    }

    if (empty($errors)) {
        registerUser($login, $password, $fullName, $phone, $email);
        $_SESSION['flash'] = 'Регистрация прошла успешно! Войдите в систему.';
        header('Location: login.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация — Учусь.РФ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">
    <div class="page-loader" id="pageLoader"><div class="loader-spinner"></div></div>
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="card shadow-lg border-0 p-4" style="width:100%;max-width:480px;">
            <div class="text-center mb-4">
                <h2 class="fw-bold text-primary"><i class="bi bi-mortarboard-fill"></i> Учусь.РФ</h2>
                <p class="text-muted">Регистрация нового пользователя</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0"><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <form method="post" novalidate>
                <div class="mb-3">
                    <label class="form-label">Логин <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                        <input type="text" name="login" class="form-control<?= isset($errors['login']) ? ' is-invalid' : '' ?>" value="<?= htmlspecialchars($old['login']??'') ?>" placeholder="Латинские буквы и цифры, мин. 6">
                    </div>
                    <?php if (isset($errors['login'])): ?><div class="invalid-feedback d-block"><?= $errors['login'] ?></div><?php endif; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label">Пароль <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" id="regPassword" class="form-control<?= isset($errors['password']) ? ' is-invalid' : '' ?>" placeholder="Минимум 8 символов">
                    </div>
                    <div class="password-strength mt-1" id="passwordStrength">
                        <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
                        <small class="strength-text" id="strengthText"></small>
                    </div>
                    <?php if (isset($errors['password'])): ?><div class="invalid-feedback d-block"><?= $errors['password'] ?></div><?php endif; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label">ФИО <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" name="full_name" class="form-control<?= isset($errors['full_name']) ? ' is-invalid' : '' ?>" value="<?= htmlspecialchars($old['full_name']??'') ?>">
                    </div>
                    <?php if (isset($errors['full_name'])): ?><div class="invalid-feedback d-block"><?= $errors['full_name'] ?></div><?php endif; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label">Телефон <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                        <input type="tel" name="phone" class="form-control<?= isset($errors['phone']) ? ' is-invalid' : '' ?>" value="<?= htmlspecialchars($old['phone']??'') ?>">
                    </div>
                    <?php if (isset($errors['phone'])): ?><div class="invalid-feedback d-block"><?= $errors['phone'] ?></div><?php endif; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label">E-mail <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>" value="<?= htmlspecialchars($old['email']??'') ?>">
                    </div>
                    <?php if (isset($errors['email'])): ?><div class="invalid-feedback d-block"><?= $errors['email'] ?></div><?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2"><i class="bi bi-person-plus"></i> Зарегистрироваться</button>
            </form>

            <div class="text-center mt-3">
                <a href="login.php">Уже зарегистрированы? Войти</a>
            </div>
            <div class="text-center mt-1">
                <a href="index.php" class="text-muted small">На главную</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
