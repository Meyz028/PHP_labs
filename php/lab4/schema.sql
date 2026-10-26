CREATE DATABASE IF NOT EXISTS practicum4 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE practicum4;

CREATE TABLE IF NOT EXISTS teams (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    name   VARCHAR(100) NOT NULL,
    city   VARCHAR(100) NOT NULL,
    points INT NOT NULL DEFAULT 0
);

INSERT INTO teams (name, city, points) VALUES
('Шахтар', 'Донецьк', 45),
('Динамо', 'Київ', 47),
('Дніпро-1', 'Дніпро', 30),
('Зоря', 'Луганськ', 28),
('Карпати', 'Львів', 22);
