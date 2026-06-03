<?php

require_once __DIR__ . '/db.php';

function validateLogin(string $login): ?string
{
    if (mb_strlen($login) < 6) {
        return 'Логин должен содержать минимум 6 символов';
    }
    if (!preg_match('/^[a-zA-Z0-9]+$/', $login)) {
        return 'Логин должен содержать только латинские буквы и цифры';
    }
    return null;
}

function validatePassword(string $password): ?string
{
    if (mb_strlen($password) < 8) {
        return 'Пароль должен содержать минимум 8 символов';
    }
    return null;
}

function loginExists(string $login): bool
{
    $stmt = Database::getInstance()->prepare(
        'SELECT 1 FROM users WHERE login = ?'
    );
    $stmt->execute([$login]);
    return (bool) $stmt->fetchColumn();
}

function registerUser(string $login, string $password, string $fullName, string $phone, string $email): bool
{
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = Database::getInstance()->prepare(
        'INSERT INTO users (login, password, full_name, phone, email) VALUES (?, ?, ?, ?, ?)'
    );
    return $stmt->execute([$login, $hash, $fullName, $phone, $email]);
}

function authenticateUser(string $login, string $password): ?array
{
    $stmt = Database::getInstance()->prepare(
        'SELECT * FROM users WHERE login = ?'
    );
    $stmt->execute([$login]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return null;
}

function getUserById(int $id): ?array
{
    $stmt = Database::getInstance()->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function getUserApplications(int $userId): array
{
    $stmt = Database::getInstance()->prepare(
        'SELECT * FROM applications WHERE user_id = ? ORDER BY created_at DESC'
    );
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function getAllApplications(string $statusFilter = '', string $sortBy = 'created_at', string $sortOrder = 'DESC', int $limit = 10, int $offset = 0): array
{
    $allowedSort = ['created_at', 'start_date', 'status'];
    $allowedOrder = ['ASC', 'DESC'];
    if (!in_array($sortBy, $allowedSort)) $sortBy = 'created_at';
    if (!in_array($sortOrder, $allowedOrder)) $sortOrder = 'DESC';

    $sql = 'SELECT a.*, u.login, u.full_name, u.phone, u.email
            FROM applications a
            JOIN users u ON a.user_id = u.id';
    $params = [];

    if ($statusFilter !== '') {
        $sql .= ' WHERE a.status = ?';
        $params[] = $statusFilter;
    }

    $sql .= " ORDER BY a.$sortBy $sortOrder LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;

    $stmt = Database::getInstance()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function countAllApplications(string $statusFilter = ''): int
{
    $sql = 'SELECT COUNT(*) FROM applications a';
    $params = [];
    if ($statusFilter !== '') {
        $sql .= ' WHERE a.status = ?';
        $params[] = $statusFilter;
    }
    $stmt = Database::getInstance()->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetchColumn();
}

function createApplication(int $userId, string $courseType, string $startDate, string $paymentMethod): bool
{
    $stmt = Database::getInstance()->prepare(
        'INSERT INTO applications (user_id, course_type, start_date, payment_method) VALUES (?, ?, ?, ?)'
    );
    return $stmt->execute([$userId, $courseType, $startDate, $paymentMethod]);
}

function updateApplicationStatus(int $appId, string $status): bool
{
    $stmt = Database::getInstance()->prepare(
        'UPDATE applications SET status = ? WHERE id = ?'
    );
    return $stmt->execute([$status, $appId]);
}

function addReview(int $userId, int $applicationId, string $text): bool
{
    $stmt = Database::getInstance()->prepare(
        'INSERT INTO reviews (user_id, application_id, text) VALUES (?, ?, ?)'
    );
    return $stmt->execute([$userId, $applicationId, $text]);
}

function getReviewsByUser(int $userId): array
{
    $stmt = Database::getInstance()->prepare(
        'SELECT r.*, a.course_type, a.status
         FROM reviews r
         JOIN applications a ON r.application_id = a.id
         WHERE r.user_id = ?
         ORDER BY r.created_at DESC'
    );
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function getReviewByApplication(int $applicationId): ?array
{
    $stmt = Database::getInstance()->prepare('SELECT * FROM reviews WHERE application_id = ?');
    $stmt->execute([$applicationId]);
    return $stmt->fetch() ?: null;
}

function isAuthenticated(): bool
{
    return isset($_SESSION['user_id']);
}

function requireAuth(): void
{
    if (!isAuthenticated()) {
        header('Location: login.php');
        exit;
    }
}
