CREATE DATABASE IF NOT EXISTS user4_sql_lab CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE user4_sql_lab;

DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

INSERT INTO users (login, password) VALUES 
('user4_sql_lab', '21012000zxc');