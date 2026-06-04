<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
requireAuth();

$courseTypes = [
    'Повышение квалификации',
    'Профессиональная переподготовка',
    'Охрана труда',
    'Пожарная безопасность',
    'Первая помощь',
];

$paymentMethods = [
    'Наличные',
    'Банковская карта',
    'Банковский перевод',
    'Электронные деньги',
];

$success = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseType    = $_POST['course_type'] ?? '';
    $startDate     = $_POST['start_date'] ?? '';
    $paymentMethod = $_POST['payment_method'] ?? '';

    if (!in_array($courseType, $courseTypes)) {
        $errors[] = 'Выберите курс';
    }
    if (!preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $startDate)) {
        $errors[] = 'Укажите дату в формате ДД.ММ.ГГГГ';
    }
    if (!in_array($paymentMethod, $paymentMethods)) {
        $errors[] = 'Выберите способ оплаты';
    }

    if (empty($errors)) {
        $mysqlDate = implode('-', array_reverse(explode('.', $startDate)));
        createApplication($_SESSION['user_id'], $courseType, $mysqlDate, $paymentMethod);
        $success = 'Заявка успешно отправлена!';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новая заявка — Учусь.РФ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="page-loader" id="pageLoader"><div class="loader-spinner"></div></div>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="profile.php"><i class="bi bi-mortarboard-fill"></i> Учусь.РФ</a>
            <div class="d-flex">
                <a href="profile.php" class="btn btn-outline-light btn-sm">Личный кабинет</a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-lg border-0 p-4">
                    <h3 class="fw-bold text-center mb-3"><i class="bi bi-file-earmark-plus"></i> Оформление заявки</h3>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php endif; ?>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0"><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
                        </div>
                    <?php endif; ?>

                    <form method="post" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Вид курса</label>
                            <select name="course_type" class="form-select" required>
                                <option value="">— Выберите курс —</option>
                                <?php foreach ($courseTypes as $ct): ?>
                                    <option value="<?= $ct ?>"><?= $ct ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Предпочтительная дата начала</label>
                            <input type="text" name="start_date" id="startDate" class="form-control" placeholder="ДД.ММ.ГГГГ" maxlength="10" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Способ оплаты</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="">— Выберите способ —</option>
                                <?php foreach ($paymentMethods as $pm): ?>
                                    <option value="<?= $pm ?>"><?= $pm ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2">Отправить заявку</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
