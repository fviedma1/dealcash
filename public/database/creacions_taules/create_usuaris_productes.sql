-- Eliminar la base de datos si existe
DROP DATABASE IF EXISTS deal_cash;

-- Crear la base de datos solo si no existe
CREATE DATABASE IF NOT EXISTS deal_cash;

-- Usar la base de datos recién creada
USE deal_cash;

-- Crear la tabla 'usuari' solo si no existe
CREATE TABLE IF NOT EXISTS usuari (
    id_usuari SMALLINT(4) AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    cognom VARCHAR(50) NOT NULL,
    correu VARCHAR(100) NOT NULL UNIQUE,
    contrasenya VARCHAR(50) NOT NULL,
    data_registre DATETIME NOT NULL,
    nom_usuari VARCHAR(50) NOT NULL UNIQUE
);

-- Crear la tabla 'producte' solo si no existe
CREATE TABLE IF NOT EXISTS producte (
    id_producte SMALLINT(4) AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    descripcio_curta VARCHAR(255) NOT NULL,
    descripcio_llarga VARCHAR(1000) NOT NULL,
    foto VARCHAR(200) NOT NULL,
    preu DOUBLE NOT NULL,
    estat ENUM('pendent', 'validat', 'rebutjat', 'assignat', 'venut') DEFAULT 'pendent',
    observacio_subhastador VARCHAR(255),
    usuari_id SMALLINT(4),
    rol_usuari ENUM('comprador', 'venedor') NOT NULL,
    FOREIGN KEY (usuari_id) REFERENCES usuari(id_usuari)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);