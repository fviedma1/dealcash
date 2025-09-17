-- Insertar usuarios en la tabla usuari
INSERT INTO usuari (nom, cognom, correu, contrasenya, data_registre, nom_usuari) VALUES
('venedor1', '1', 'venedor1@copernic.cat', 'venedor1', NOW(), 'venedor1'),
('venedor2', '2', 'venedor2@copernic.cat', 'venedor2', NOW(), 'venedor2'),
('subhastador', '3', 'subhastador@copernic.cat', 'subhastador', NOW(), 'subhastador');

-- Insertar productos en la tabla producte
INSERT INTO producte (nom, descripcio_curta, descripcio_llarga, foto, preu, estat, observacio_subhastador, usuari_id, rol_usuari) VALUES
('Teléfono Inteligente', 'Teléfono con cámara de 48 MP', 'Un smartphone de última generación con 128GB de almacenamiento y cámara de 48MP.', 'foto1.jpg', 299.99, 'pendent', 'Opcional', 1, 'venedor'),
('Portátil Gaming', 'Portátil con tarjeta gráfica dedicada', 'Portátil de alto rendimiento con procesador i7, 16GB de RAM y tarjeta gráfica GTX 1650.', 'foto2.jpg', 899.99, 'pendent', 'Opcional', 2, 'venedor'),
('Auriculares Bluetooth', 'Auriculares inalámbricos con cancelación de ruido', 'Auriculares inalámbricos con batería de 20 horas y cancelación activa de ruido.', 'foto3.jpg', 79.99, 'pendent', 'Opcional', 1, 'venedor'),
('Cámara Reflex', 'Cámara de 24MP con lentes intercambiables', 'Cámara réflex digital con 24 megapíxeles y capacidad de grabación 4K.', 'foto4.jpg', 499.99, 'pendent', 'Opcional', 3, 'venedor'),
('Smartwatch', 'Reloj inteligente con monitor de ritmo cardíaco', 'Reloj inteligente con GPS, monitor de ritmo cardíaco y seguimiento de actividades.', 'foto5.jpg', 149.99, 'pendent', 'Opcional', 2, 'venedor');