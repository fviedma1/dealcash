<?php
require '../config/db_connection.php'; // Ajusta el camino si es necesario

try {
    $query = "SELECT id_usuari, nom_usuari FROM usuari";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>