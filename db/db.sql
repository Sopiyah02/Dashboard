CREATE DATABASE devquiz_db;
USE devquiz_db;

/* User Table */
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE
) ENGINE=InnoDB;

/* Languages Table */
CREATE TABLE languages (
    language_id INT AUTO_INCREMENT PRIMARY KEY,
    language_name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

/* Insert Initial Languages */
INSERT INTO languages (language_name) VALUES
('JavaScript'),
('Python');

/* Questions Table (with Difficulty) */
CREATE TABLE questions (
    question_id INT AUTO_INCREMENT PRIMARY KEY,
    language_id INT NOT NULL,
    question_text TEXT NOT NULL,
    difficulty VARCHAR(20) NOT NULL, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (language_id)
        REFERENCES languages(language_id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

/* Answers Table */
CREATE TABLE answers (
    answer_id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    answer_text TEXT NOT NULL,
    is_correct TINYINT(1) DEFAULT 0,
    FOREIGN KEY (question_id)
        REFERENCES questions(question_id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

/* Sample Question */
INSERT INTO questions (language_id, question_text, difficulty)
VALUES (1, 'Which keyword declares a variable in JavaScript?', 'beginner');

INSERT INTO answers (question_id, answer_text, is_correct) VALUES
(1,'var', 0),
(1,'let', 1),
(1,'define', 0),
(1,'variable', 0);