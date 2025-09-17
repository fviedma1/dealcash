<?php
$servername = "localhost:33061";  // Cambia si tu DB está en otro servidor
$username = "grup8";        // Tu nombre de usuario de MariaDB
$password = "abc.123";       // Tu contraseña de MariaDB
$dbname = "deal_cash";     // El nombre de la base de datos que estás utilizando

try {
    // Crear una conexión PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

    // Configurar el modo de error de PDO a excepción
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
} catch(PDOException $e) {
    // Si hay un error en la conexión, se captura y muestra
    die("Error de conexión: " . $e->getMessage());
}
?>