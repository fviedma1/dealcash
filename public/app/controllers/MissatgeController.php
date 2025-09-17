<?php
require_once '../config/db_connection.php'; // Asegúrate de incluir la conexión a la base de datos

class MissatgeController {
    public function enviarMissatge($venedorId, $missatge) {
        try {
            global $conn; // Usar la conexión global definida en db_connection.php

            // Preparar y ejecutar la consulta
            $stmt = $conn->prepare("INSERT INTO missatge (venedor_id, missatge) VALUES (?, ?)");
            $stmt->execute([$venedorId, $missatge]);

            return true;
        } catch (PDOException $e) {
            die("Error en la conexión: " . $e->getMessage());
        }
    }
}
?>