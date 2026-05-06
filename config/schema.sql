-- ============================================================
--  quiz_system  –  full schema + seed data
--  Run this in phpMyAdmin or mysql CLI before first use
-- ============================================================

CREATE DATABASE IF NOT EXISTS quiz_system
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE quiz_system;

-- ── Users ────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id         INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(80)  NOT NULL UNIQUE,
    email      VARCHAR(160) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,          -- bcrypt hash
    role       ENUM('user','admin') NOT NULL DEFAULT 'user',
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME     NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Languages ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS languages (
    id         INT         NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(80) NOT NULL UNIQUE,
    created_at DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Categories ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS categories (
    id         INT         NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(80) NOT NULL UNIQUE,    -- Easy / Hard / Difficult …
    created_at DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Questions ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS questions (
    id          INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
    language_id INT          NOT NULL,
    category_id INT          NOT NULL,
    question    TEXT         NOT NULL,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (language_id) REFERENCES languages(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Answers ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS answers (
    id          INT     NOT NULL AUTO_INCREMENT PRIMARY KEY,
    question_id INT     NOT NULL,
    answer_text TEXT    NOT NULL,
    is_correct  TINYINT NOT NULL DEFAULT 0,   -- 1 = correct
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Feedback ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS feedback (
    id         INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id    INT          NOT NULL DEFAULT 0,
    username   VARCHAR(100) NOT NULL DEFAULT '',
    category   VARCHAR(80)  NOT NULL DEFAULT 'General',
    rating     TINYINT      NOT NULL DEFAULT 0,
    message    TEXT         NOT NULL,
    is_read    TINYINT(1)   NOT NULL DEFAULT 0,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── User Sessions / Login Log ─────────────────────────────────
CREATE TABLE IF NOT EXISTS login_logs (
    id         INT      NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id    INT      NOT NULL,
    logged_in  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    logged_out DATETIME NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Seed: admin account (password = Admin@1234) ───────────────
INSERT IGNORE INTO users (username, email, password, role)
VALUES ('admin', 'admin@quizsystem.com',
        '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'admin');
-- NOTE: replace the hash above with: echo password_hash('Admin@1234', PASSWORD_BCRYPT);

-- ── Seed: sample languages ────────────────────────────────────
INSERT IGNORE INTO languages (name) VALUES
    ('Java'), ('JavaScript'), ('Python'), ('C++'), ('PHP'), ('HTML/CSS');

-- ── Seed: sample categories ──────────────────────────────────
INSERT IGNORE INTO categories (name) VALUES
    ('Easy'), ('Hard'), ('Difficult');

-- ── Seed: sample questions ────────────────────────────────────
INSERT IGNORE INTO questions (language_id, category_id, question) VALUES
    (1, 1, 'What is the correct syntax to print "Hello" in Java?'),
    (2, 1, 'Which keyword declares a variable in JavaScript?'),
    (3, 2, 'What does the "self" parameter refer to in Python?');

-- ── Seed: sample answers ─────────────────────────────────────
INSERT IGNORE INTO answers (question_id, answer_text, is_correct) VALUES
    (1, 'System.out.println("Hello");', 1),
    (1, 'print("Hello")',               0),
    (1, 'echo "Hello";',                0),
    (2, 'let',  1),
    (2, 'dim',  0),
    (2, 'var',  0),
    (3, 'The current instance of the class', 1),
    (3, 'A global variable',                 0),
    (3, 'The parent class',                  0);
