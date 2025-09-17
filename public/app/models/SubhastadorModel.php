<?php
require_once '../config/db_connection.php';

class SubhastadorModel {
    private $conn;

    public function __construct() {
        global $conn; // Usamos la conexión global que viene de db_connection.php
        $this->conn = $conn;
    }

    // Obtener todos los productos y sus estados
    public function getProducts() {
        $query = "SELECT * FROM producte";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
