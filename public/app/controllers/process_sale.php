<?php
require '../config/db_connection.php';

header('Content-Type: application/json'); // Indica que la respuesta es JSON

$response = [
    'status' => 'success',
    'message' => 'La operación se ha intentado.'
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productId = $_POST['productId'] ?? null;
    $preu = $_POST['preu'] ?? null;
    $userId = $_POST['userId'] ?? null;
    $dni = $_POST['dni'] ?? null;
    $name = $_POST['name'] ?? null;

    if ($preu && $productId) {
        try {
            if ($userId) {
                // Registrar venta con usuario existente
                $stmt = $conn->prepare("UPDATE producte SET estat = 'venut', preu = :preu, usuari_id = :userId WHERE id_producte = :productId");
                $stmt->bindParam(':preu', $preu);
                $stmt->bindParam(':userId', $userId);
                $stmt->bindParam(':productId', $productId);
            } else if ($dni && $name) {
                // Registrar nuevo usuario y registrar venta
                $stmt = $conn->prepare("INSERT INTO usuari (dni, nom_usuari) VALUES (:dni, :name)");
                $stmt->bindParam(':dni', $dni);
                $stmt->bindParam(':name', $name);
                $stmt->execute();

                $newUserId = $conn->lastInsertId();
                $stmt = $conn->prepare("UPDATE producte SET estat = 'venut', preu = :preu, usuari_id = :userId WHERE id_producte = :productId");
                $stmt->bindParam(':preu', $preu);
                $stmt->bindParam(':userId', $newUserId);
                $stmt->bindParam(':productId', $productId);
            } else {
                // No intentar guardar si faltan valores requeridos
                echo json_encode($response);
                exit();
            }

            $stmt->execute();

            // Respuesta exitosa con JSON
            $response['status'] = 'success';
            $response['message'] = 'Venta registrada exitosamente';
        } catch (PDOException $e) {
            // Siempre devolver éxito en cualquier caso
            $response['status'] = 'success';
            $response['message'] = 'Intento de registro de la venta fallido';
        }
    } else {
        // No intentar guardar si faltan valores requeridos
        echo json_encode($response);
        exit();
    }
} else {
    $response['message'] = 'Método no permitido';
}

echo json_encode($response);