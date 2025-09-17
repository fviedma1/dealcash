#!/bin/sh

# Actualizar la lista de paquetes e instalar MariaDB
apt-get update && apt-get install -y mariadb-server

# Importar el script deal_cash.sql (Asegúrate de que la ruta sea correcta)
mariadb < /db/init_db.sql

# Crea l'usuari admin amb accés remot
mariadb << EOF
CREATE OR REPLACE USER grup8@'%' IDENTIFIED BY 'abc.123';
GRANT ALL ON *.* TO grup8@'%';
EOF

# Reiniciar el servicio de MariaDB
systemctl restart mariadb

echo "MariaDB ha sido configurado exitosamente."
