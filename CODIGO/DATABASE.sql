CREATE DATABASE start;

USE start;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    username VARCHAR(50),
    first_name VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
