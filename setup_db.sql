-- Run this SQL in phpMyAdmin or MySQL CLI to set up the database

CREATE DATABASE IF NOT EXISTS student_crud_sample;

USE student_crud_sample;

CREATE TABLE IF NOT EXISTS students (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(100) NOT NULL,
    lastname  VARCHAR(100) NOT NULL,
    email     VARCHAR(150) NOT NULL UNIQUE,
    course    VARCHAR(100) NOT NULL,
    year      TINYINT(1)   NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
