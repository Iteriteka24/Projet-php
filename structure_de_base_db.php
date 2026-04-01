<?php
CREATE DATABASE IF NOT EXISTS identite_db;
USE identite_db;

CREATE TABLE IF NOT EXISTS personne (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    date_naissance DATE NOT NULL,
    genre ENUM('homme', 'femme', 'autre') NOT NULL,
    situation_familiale ENUM('celibataire', 'marie', 'divorce', 'veuf') NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    telephone VARCHAR(20),
    aadresse VARCHAR(30),
    photo VARCHAR(255),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
?>