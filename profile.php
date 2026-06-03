<?php
require_once __DIR__ . '/includes/config.php';
requireAuth();

$userId    = $_SESSION['user_id'];
$user      = getUserById($userId);
$apps      = getUserApplications($userId);
$reviews   = getReviewsByUser($userId);

$reviewedAppIds = array_column($reviews, 'application_id');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_text'], $_POST['app_id'])) {
    $appId = (int) $_POST['app_id'];
    $text  = trim($_POST['review_text']);
    if ($text !== '') {
        addReview($userId, $appId, $text);
        header('Location: profile.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет — Учусь.РФ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="profile.php"><i class="bi bi-mortarboard-fill"></i> Учусь.РФ</a>
            <div class="d-flex">
                <a href="application.php" class="btn btn-outline-light btn-sm me-2">Новая заявка</a>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Выход</a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <i class="bi bi-person-circle display-1 text-primary"></i>
                        <h5 class="mt-2"><?= htmlspecialchars($user['full_name']) ?></h5>
                        <p class="text-muted mb-1"><i class="bi bi-envelope"></i> <?= htmlspecialchars($user['email']) ?></p>
                        <p class="text-muted mb-1"><i class="bi bi-telephone"></i> <?= htmlspecialchars($user['phone']) ?></p>
                        <p class="text-muted mb-0"><i class="bi bi-person-badge"></i> Логин: <?= htmlspecialchars($user['login']) ?></p>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mt-3">
                    <div class="card-body">
                        <h5><i class="bi bi-images"></i> Галерея</h5>
                        <div class="slider-container">
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

            <div class="col-lg-8">
                <ul class="nav nav-tabs mb-3" id="profileTabs">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabApps">Мои заявки</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabReviews">Мои отзывы</button></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tabApps">
                        <?php if (empty($apps)): ?>
                            <div class="alert alert-info">У вас пока нет заявок. <a href="application.php">Оформить заявку</a></div>
                        <?php else: ?>
                            <?php foreach ($apps as $app): ?>
                                <div class="card shadow-sm border-0 mb-3 app-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="fw-bold mb-1"><?= htmlspecialchars($app['course_type']) ?></h6>
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar"></i> <?= date('d.m.Y', strtotime($app['start_date'])) ?>
                                                    &nbsp;|&nbsp; <i class="bi bi-wallet2"></i> <?= htmlspecialchars($app['payment_method']) ?>
                                                </small>
                                            </div>
                                            <span class="badge bg-<?= $app['status'] === 'Новая' ? 'warning' : ($app['status'] === 'Идет обучение' ? 'info' : 'success') ?> fs-6">
                                                <?= htmlspecialchars($app['status']) ?>
                                            </span>
                                        </div>
                                        <small class="text-muted d-block mt-2">Создана: <?= date('d.m.Y H:i', strtotime($app['created_at'])) ?></small>

                                        <?php if (in_array($app['status'], ['Идет обучение', 'Обучение завершено']) && !in_array($app['id'], $reviewedAppIds)): ?>
                                            <button class="btn btn-sm btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#reviewModal<?= $app['id'] ?>">
                                                <i class="bi bi-star"></i> Оставить отзыв
                                            </button>

                                            <div class="modal fade" id="reviewModal<?= $app['id'] ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="post">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Отзыв о курсе</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <input type="hidden" name="app_id" value="<?= $app['id'] ?>">
                                                                <textarea name="review_text" class="form-control" rows="4" placeholder="Напишите ваш отзыв..." required></textarea>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary">Отправить</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="tab-pane fade" id="tabReviews">
                        <?php if (empty($reviews)): ?>
                            <div class="alert alert-info">Вы ещё не оставляли отзывы.</div>
                        <?php else: ?>
                            <?php foreach ($reviews as $r): ?>
                                <div class="card shadow-sm border-0 mb-3">
                                    <div class="card-body">
                                        <h6><?= htmlspecialchars($r['course_type']) ?></h6>
                                        <p class="mb-1"><?= nl2br(htmlspecialchars($r['text'])) ?></p>
                                        <small class="text-muted"><?= date('d.m.Y H:i', strtotime($r['created_at'])) ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
