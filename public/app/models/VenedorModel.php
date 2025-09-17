<?php
require_once __DIR__ . '/../config/db_connection.php';

class VenedorModel {
    private $conn;

    public function __construct() {
        global $conn; // Usamos la conexión global que viene de db_connection.php
        $this->conn = $conn;
    }

    // Obtener productos del vendedor por el ID de usuario
    public function getProductsByVenedor($venedorId) {
        $query = "SELECT * FROM producte WHERE usuari_id = :venedorId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':venedorId', $venedorId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para obtener los mensajes de los productos del vendedor
    public function getMessagesByVenedor($venedorId) {
        $query = "SELECT m.id_missatge, m.missatge, m.estat, m.data_hora, p.nom AS product_name
                  FROM missatge m
                  JOIN producte p ON m.producte_id = p.id_producte
                  WHERE m.venedor_id = :venedorId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':venedorId', $venedorId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para eliminar un mensaje por su ID
    public function deleteMessageById($messageId) {
        $query = "DELETE FROM missatge WHERE id_missatge = :messageId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':messageId', $messageId);
        return $stmt->execute();
    }
}
?>