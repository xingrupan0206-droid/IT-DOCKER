-- Database initialisatie voor het contactformulier
-- Dit script wordt automatisch uitgevoerd bij het opstarten van de MySQL container

CREATE DATABASE IF NOT EXISTS contactdb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE contactdb;

CREATE TABLE IF NOT EXISTS berichten (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    onderwerp VARCHAR(200) NOT NULL,
    bericht TEXT NOT NULL,
    aangemaakt_op TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
