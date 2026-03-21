CREATE DATABASE devquiz_db;
USE devquiz_db;


/* mga manggamit */
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE,
) ENGINE=InnoDB;


/*mga langguage*/
CREATE TABLE languages (
    language_id INT AUTO_INCREMENT PRIMARY KEY,
    language_name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;


/* mga choices */
INSERT INTO languages (language_name) VALUES
('JavaScript'),
('Python');


/* mga pangutana */
CREATE TABLE questions (
    question_id INT AUTO_INCREMENT PRIMARY KEY,
    language_id INT NOT NULL,
    question_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (language_id)
        REFERENCES languages(language_id)
        ON DELETE CASCADE
) ENGINE=InnoDB;


/*mga answer*/
CREATE TABLE answers (
    answer_id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    answer_text TEXT NOT NULL,
    is_correct TINYINT(1) DEFAULT 0,
    FOREIGN KEY (question_id)
        REFERENCES questions(question_id)
        ON DELETE CASCADE
) ENGINE=InnoDB;


/*sample nga question*/
INSERT INTO questions (language_id, question_text, difficulty)
VALUES (1, 'Which keyword declares a variable in JavaScript?');


INSERT INTO answers (question_id, answer_text, is_correct) VALUES
(1,'var'),
(1,'let'),
(1,'define'),
(1,'variable');


/*user answer*/

CREATE TABLE user_answers (
    user_answer_id INT AUTO_INCREMENT PRIMARY KEY,
    attempt_id INT NOT NULL,
    question_id INT NOT NULL,
    answer_id INT NOT NULL,
    is_correct TINYINT(1),
    FOREIGN KEY (question_id)
        REFERENCES questions(question_id),
    FOREIGN KEY (answer_id)
        REFERENCES answers(answer_id)
) ENGINE=InnoDB;
