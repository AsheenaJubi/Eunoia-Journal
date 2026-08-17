CREATE DATABASE IF NOT EXISTS eunoia
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE eunoia;

CREATE TABLE IF NOT EXISTS users (
    user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    profile_image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS entries (
    entry_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(150) NOT NULL,
    content TEXT NOT NULL,
    mood ENUM('Happy','Calm','Excited','Sad','Angry','Grateful') NOT NULL DEFAULT 'Happy',
    file_name VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_entries_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    INDEX idx_entries_user (user_id),
    INDEX idx_entries_mood (user_id, mood),
    INDEX idx_entries_date (user_id, created_at)
) ENGINE=InnoDB;
