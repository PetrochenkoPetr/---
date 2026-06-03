<?php
require_once __DIR__ . '/includes/config.php';

if (isAuthenticated()) {
    header('Location: profile.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Учусь.РФ — Портал онлайн-курсов</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="text-center">
            <div class="mb-4">
                <i class="bi bi-mortarboard-fill display-1 text-primary"></i>
            </div>
            <h1 class="fw-bold display-5">Добро пожаловать на <span class="text-primary">Учусь.РФ</span></h1>
            <p class="lead text-muted mb-4">Портал для записи на онлайн-курсы повышения квалификации, переподготовки и охраны труда</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="login.php" class="btn btn-primary btn-lg px-4"><i class="bi bi-box-arrow-in-right"></i> Войти</a>
                <a href="register.php" class="btn btn-outline-primary btn-lg px-4"><i class="bi bi-person-plus"></i> Регистрация</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
