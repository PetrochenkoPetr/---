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
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-mortarboard-fill"></i> Учусь.РФ</a>
            <div class="d-flex">
                <a href="login.php" class="btn btn-outline-light btn-sm me-2">Войти</a>
                <a href="register.php" class="btn btn-light btn-sm">Регистрация</a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow border-0 overflow-hidden">
                    <div class="slider-container" style="border-radius:0;">
                        <div class="slider-wrapper" id="sliderWrapper">
                            <div class="slider-slide"><img src="assets/images/slide1.svg" alt="Курс 1"></div>
                            <div class="slider-slide"><img src="assets/images/slide2.svg" alt="Курс 2"></div>
                            <div class="slider-slide"><img src="assets/images/slide3.svg" alt="Курс 3"></div>
                            <div class="slider-slide"><img src="assets/images/slide4.svg" alt="Курс 4"></div>
                        </div>
                        <button class="slider-btn slider-prev" id="sliderPrev"><i class="bi bi-chevron-left"></i></button>
                        <button class="slider-btn slider-next" id="sliderNext"><i class="bi bi-chevron-right"></i></button>
                        <div class="slider-dots" id="sliderDots"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <h1 class="fw-bold display-6">Добро пожаловать на <span class="text-primary">Учусь.РФ</span></h1>
            <p class="lead text-muted mb-4">Портал для записи на онлайн-курсы повышения квалификации, переподготовки и охраны труда</p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="login.php" class="btn btn-primary btn-lg px-4"><i class="bi bi-box-arrow-in-right"></i> Войти</a>
                <a href="register.php" class="btn btn-outline-primary btn-lg px-4"><i class="bi bi-person-plus"></i> Регистрация</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
