<?php
require_once '../config/db_connection.php';

class UserModel {
    private $conn;

    public function __construct() {
        global $conn; // Usamos la conexión global que viene de db_connection.php
        $this->conn = $conn;
    }

    // Método para verificar el usuario y la contraseña
    public function verifyUser($username, $password) {
        $query = "SELECT * FROM usuari WHERE nom_usuari = :username AND contrasenya = :password";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
