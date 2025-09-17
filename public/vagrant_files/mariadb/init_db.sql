-- Crear el usuario 'grup8' con la contraseña 'abc.123' y otorgar todos los permisos, aplicando los cambios
CREATE USER IF NOT EXISTS 'grup8'@'localhost' IDENTIFIED BY 'abc.123';
GRANT ALL PRIVILEGES ON *.* TO 'grup8'@'localhost' WITH GRANT OPTION;
FLUSH PRIVILEGES;

-- Eliminar la base de datos si existe
DROP DATABASE IF EXISTS deal_cash;

-- Crear la base de datos solo si no existe
CREATE DATABASE IF NOT EXISTS deal_cash;

-- Usar la base de datos recién creada
USE deal_cash;

-- Otorgar todos los permisos en la base de datos 'deal_cash' al usuario 'grup8'
GRANT ALL PRIVILEGES ON deal_cash.* TO 'grup8'@'localhost';

-- Aplicar los cambios
FLUSH PRIVILEGES;

-- Crear la tabla 'usuari' solo si no existe
CREATE TABLE IF NOT EXISTS usuari (
id_usuari SMALLINT(4) AUTO_INCREMENT PRIMARY KEY,
nom VARCHAR(50) NOT NULL,
cognom VARCHAR(50) NOT NULL,
correu VARCHAR(100) NOT NULL UNIQUE,
contrasenya VARCHAR(50) NOT NULL,
data_registre DATETIME NOT NULL,
nom_usuari VARCHAR(50) NOT NULL UNIQUE,
rol ENUM('comprador', 'venedor', 'subhastador') NOT NULL -- Agregar columna para roles
);

CREATE TABLE IF NOT EXISTS subhasta (
id_subhasta SMALLINT(4) AUTO_INCREMENT PRIMARY KEY,
data_hora DATETIME NOT NULL,
descripcio VARCHAR(255) NOT NULL,
percentatge INT NOT NULL DEFAULT 10,
subhastador_id SMALLINT(4) NOT NULL,
estat ENUM('oberta', 'tancada', 'iniciada', 'finalitzada') NOT NULL DEFAULT 'oberta',
FOREIGN KEY (subhastador_id) REFERENCES usuari(id_usuari) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS producte (
id_producte SMALLINT(4) AUTO_INCREMENT PRIMARY KEY,
nom VARCHAR(100) NOT NULL,
descripcio_curta VARCHAR(255) NOT NULL,
descripcio_llarga VARCHAR(1000) NOT NULL,
foto VARCHAR(200) NOT NULL,
preu DOUBLE NOT NULL,
estat ENUM('pendent', 'validat', 'rebutjat', 'assignat', 'venut', 'retirat') DEFAULT 'pendent',
observacio_subhastador VARCHAR(255),
usuari_id SMALLINT(4),
subhasta_id SMALLINT(4) NULL,
rol_usuari ENUM('comprador', 'venedor') NOT NULL,
FOREIGN KEY (usuari_id) REFERENCES usuari(id_usuari) ON DELETE CASCADE,
FOREIGN KEY (subhasta_id) REFERENCES subhasta(id_subhasta) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS missatge (
id_missatge INT AUTO_INCREMENT PRIMARY KEY,
producte_id SMALLINT(4),
venedor_id SMALLINT(4),
missatge TEXT,
estat VARCHAR(50),
data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (producte_id) REFERENCES producte(id_producte) ON DELETE CASCADE,
FOREIGN KEY (venedor_id) REFERENCES usuari(id_usuari) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS likes_producte (
id_like INT AUTO_INCREMENT PRIMARY KEY,
producte_id SMALLINT(4) NOT NULL,
usuari_id SMALLINT(4) NULL,
guest_id VARCHAR(255) NULL,
FOREIGN KEY (producte_id) REFERENCES producte(id_producte) ON DELETE CASCADE,
FOREIGN KEY (usuari_id) REFERENCES usuari(id_usuari) ON DELETE CASCADE,
UNIQUE (producte_id, usuari_id, guest_id)
);

-- Insertar usuarios en la tabla usuari
INSERT INTO usuari (nom, cognom, correu, contrasenya, data_registre, nom_usuari, rol) VALUES
('venedor1', '1', 'venedor1@copernic.cat', 'venedor1', NOW(), 'venedor1', 'venedor'),
('venedor2', '2', 'venedor2@copernic.cat', 'venedor2', NOW(), 'venedor2', 'venedor'),
('subhastador', '3', 'subhastador@copernic.cat', 'subhastador', NOW(), 'subhastador', 'subhastador');

-- Insertar productos en la tabla producte
INSERT INTO producte (nom, descripcio_curta, descripcio_llarga, foto, preu, estat, observacio_subhastador, usuari_id, rol_usuari) VALUES
('Telèfon Intel·ligent', 'Telèfon amb càmera de 48 MP', 'Un smartphone d ultima generació amb 128GB d emmagatzematge i càmera de 48MP.', '../../vagrant_files/images/telefono_inteligente.png', 299.99, 'pendent', 'Opcional', 1, 'venedor'),
('Portàtil Gaming', 'Portàtil amb targeta gràfica dedicada', 'Portàtil d alt rendiment amb processador i7, 16GB de RAM i targeta gràfica GTX 1650.', '../../vagrant_files/images/portatil_gaming.png', 899.99, 'pendent', 'Opcional', 2, 'venedor'),
('Auriculars Bluetooth', 'Auriculars sense fils amb cancel·lació de soroll', 'Auriculars sense fils amb bateria de 20 hores i cancel·lació activa de soroll.', '../../vagrant_files/images/auriculares_bluetooth.png', 79.99, 'pendent', 'Opcional', 1, 'venedor'),
('Càmera Reflex', 'Càmera de 24MP amb lents intercanviables', 'Càmera rèflex digital amb 24 megapíxels i capacitat de gravació 4K.', '../../vagrant_files/images/camara_reflex.png', 499.99, 'pendent', 'Opcional', 1, 'venedor'),
('Smartwatch', 'Rellotge intel·ligent amb monitor de ritme cardíac', 'Rellotge intel·ligent amb GPS, monitor de ritme cardíac i seguiment d activitats.', '../../vagrant_files/images/smartwatch.png', 149.99, 'pendent', 'Opcional', 2, 'venedor'),
('Màquina d Escriure Vintage', 'Màquina d escriure Underwood', 'Antiga màquina d escriure Underwood de 1920, en excel·lent estat de conservació.', '../../vagrant_files/images/maquina_escribir_vintage.png', 350.00, 'validat', 'Producte validat.', 1, 'venedor'),
('Rellotge de butxaca antic', 'Rellotge de butxaca de plata', 'Rellotge de butxaca fet a mà en plata, datat en 1885 amb gravats intrincats.', '../../vagrant_files/images/reloj_bolsillo_antiguo.png', 1200.00, 'validat', 'Producte validat.', 2, 'venedor'),
('Disc de Vinil The Beatles', 'Edat limitada de l àlbum Abbey Road', 'Vinil original d Abbey Road, llançat en 1969, edició limitada en perfecte estat.', '../../vagrant_files/images/vinilo_beatles_abbey_road.png', 200.00, 'validat', 'Producte validat.', 2, 'venedor'),
('Cartell Publicitari Art Nouveau', 'Cartell de Mucha de 1898', 'Cartell original d Alphonse Mucha de 1898, emmarcat i preservat en condicions òptimes.', '../../vagrant_files/images/cartel_mucha_art_nouveau.png', 2500.00, 'validat', 'Producte validat.', 1, 'venedor'),
('Espasa Samurai Antiga', 'Katana de l era Edo', 'Katana original de la era Edo (1603-1868), amb empunyadura decorada i fulla forjada a mà.', '../../vagrant_files/images/katana_era_edo.png', 10000.00, 'validat', 'Producte validat.', 1, 'venedor'),
('Gerro de Porcellana Xinesa', 'Gerro xinès de la dinastia Ming', 'Gerro de porcellana original de la dinastia Ming (1368-1644), amb motius florals pintats a mà.', '../../vagrant_files/images/jarron_porcelana_ming.png', 15000.00, 'validat', 'Producte validat.', 2, 'venedor'),
('Pòster de Pel·lícula Casablanca', 'Pòster original de Casablanca', 'Pòster original de la pel·lícula Casablanca de 1942, una autèntica peça de col·lecció cinematogràfica.', '../../vagrant_files/images/poster_casablanca.png', 3000.00, 'validat', 'Producte validat.', 1, 'venedor'),
('Cadira de Disseny Eames', 'Cadira original de Charles i Ray Eames', 'Cadira de disseny clàssic de 1956, feta de fusta contraplacada i cuir, peça icònica del disseny modern.', '../../vagrant_files/images/silla_eames.png', 2500.00, 'validat', 'Producte validat.', 1, 'venedor'),
('Skuna, The Leonine Rakan', 'Carta d edició limitada de Yu Gi-Oh', 'Aquesta carta va ser el premi als Campionats del Món de Yu-Gi-Oh de 2009 i només existeixen sis còpies.', '../../vagrant_files/images/skuna_the_leonine_rakan.png', 6000.00, 'validat', 'Producte validat.', 2, 'venedor'),
('Moneda d Or Roma Imperial', 'Moneda d or de l Imperi Romà', 'Antiga moneda d or de l època de l emperador August, amb inscripcions originals i en bon estat.', '../../vagrant_files/images/moneda_romana.png', 1000.00, 'pendent', 'Opcional', 1, 'venedor'),
('Ploma Estilogràfica Montblanc', 'Ploma estilogràfica edició limitada', 'Ploma Montblanc de la col·lecció Patron of Art, amb detalls en or i cos de resina negra.', '../../vagrant_files/images/ploma_montblanc.png', 1000.00, 'pendent', 'Opcional', 2, 'venedor'),
('Llibre Primera Edició Shakespeare', 'Primera edició de les obres completes de Shakespeare', 'Volum rar de les obres completes de William Shakespeare, publicat originalment en 1623.', '../../vagrant_files/images/llibre_shakespeare.png', 1000.00, 'pendent', 'Opcional', 2, 'venedor'),
('Làmpara Tiffany Original', 'Làmpara d estil Tiffany amb vidre de colors', 'Làmpara original dissenyada per Louis Comfort Tiffany, amb vidres de colors en patrons florals.', '../../vagrant_files/images/lampara_tiffany.png', 2500.00, 'pendent', 'Opcional', 1, 'venedor'),
('Vi de Collita Especial Bordeaux 1959', 'Ampolla de vi Bordeaux de 1959', 'Ampolla de vi Bordeaux de collita especial de 1959, en excel·lent estat per a col·leccionistes.', '../../vagrant_files/images/vino_bordeaux.png', 120.00, 'pendent', 'Opcional', 2, 'venedor'),
('Mapa del Món del Segle XVII', 'Mapa antic del món', 'Mapa del món creat al segle XVII, emmarcat i en molt bon estat de conservació, amb detallades il·lustracions de continents.', '../../vagrant_files/images/mapa_mundo_antiguo.png', 110.00, 'validat', 'Producte validat.', 1, 'venedor'),
('Joc d Escacs d Ivori', 'Escacs de marfil del segle XVIII', 'Joc d escacs elaborat en marfil tallat a mà del segle XVIII, amb peces detallades i tauler de fusta noble.', '../../vagrant_files/images/ajedrez_marfil.png', 200.00, 'validat', 'Producte validat.', 1, 'venedor'),
('Medalla Olímpica d Or 1936', 'Medalla olímpica de Berlín 1936', 'Medalla d or original de les Olimpíades de Berlín 1936, una peça única i històrica de col·lecció.', '../../vagrant_files/images/medalla_olimpica_1936.png', 500.00, 'validat', 'Producte validat.', 1, 'venedor');

INSERT INTO subhasta (data_hora, descripcio, percentatge, subhastador_id, estat) VALUES
('2023-10-01 10:00:00', 'Subhasta de Octubre', 10, 3, 'oberta'),
('2023-11-01 10:00:00', 'Subhasta de Noviembre', 10, 3, 'tancada'),
('2023-12-01 10:00:00', 'Subhasta de Diciembre', 10, 3, 'iniciada');

-- Asignar productos a la subhasta de Octubre (ID 1)
UPDATE producte SET subhasta_id = 1, estat = 'assignat' WHERE id_producte IN (1, 2);
-- Asignar productos a la subhasta de Noviembre (ID 2)
UPDATE producte SET subhasta_id = 2, estat = 'assignat' WHERE id_producte IN (3, 4);
-- Asignar productos a la subhasta de Diciembre (ID 3)
UPDATE producte SET subhasta_id = 3, estat = 'assignat' WHERE id_producte IN (5, 6);