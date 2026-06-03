-- ============================================================
-- База данных: uchus_rf
-- Портал "Учусь.РФ" — запись на онлайн курсы
-- ER-диаграмма (текстовое описание):
--
-- ┌──────────────────┐
-- │      users       │
-- ├──────────────────┤
-- │ id (PK)          │──┐
-- │ login (UNIQUE)   │  │
-- │ password         │  │
-- │ full_name        │  │
-- │ phone            │  │
-- │ email            │  │
-- │ created_at       │  │
-- └──────────────────┘  │
--                       │ 1
-- ┌──────────────────┐  │
-- │   applications   │  │
-- ├──────────────────┤  │
-- │ id (PK)          │  │
-- │ user_id (FK)─────┼──┘
-- │ course_type      │
-- │ start_date       │
-- │ payment_method   │
-- │ status           │
-- │ created_at       │
-- │ updated_at       │
-- └──────────────────┘
--         │
--         │ 1
-- ┌──────────────────┐
-- │     reviews      │
-- ├──────────────────┤
-- │ id (PK)          │
-- │ user_id (FK)─────┼── (связь с users)
-- │ application_id ──┼── (связь с applications)
-- │ text             │
-- │ created_at       │
-- └──────────────────┘
--
-- Связи:
--   users      1 ──∞ applications  (один пользователь → много заявок)
--   users      1 ──∞ reviews       (один пользователь → много отзывов)
--   applications 1 ──∞ reviews     (одна заявка → много отзывов)
-- ============================================================

CREATE DATABASE IF NOT EXISTS uchus_rf
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE uchus_rf;

-- -----------------------------------------------------------
-- 1) Пользователи
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    login       VARCHAR(50)  NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,                -- bcrypt hash
    full_name   VARCHAR(100) NOT NULL,
    phone       VARCHAR(20)  NOT NULL,
    email       VARCHAR(100) NOT NULL,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_login (login)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 2) Заявки на курсы
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS applications (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED NOT NULL,
    course_type     VARCHAR(100) NOT NULL,             -- вид курса
    start_date      DATE         NOT NULL,
    payment_method  VARCHAR(50)  NOT NULL,             -- способ оплаты
    status          ENUM('Новая','Идет обучение','Обучение завершено')
                     NOT NULL DEFAULT 'Новая',
    created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
                     ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 3) Отзывы
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS reviews (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED NOT NULL,
    application_id  INT UNSIGNED NOT NULL,
    text            TEXT         NOT NULL,
    created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    INDEX idx_user_review (user_id),
    INDEX idx_app_review (application_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
