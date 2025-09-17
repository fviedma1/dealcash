<?php
require '../config/db_connection.php';
require '../controllers/SubhastadorController.php';

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['ventas']) && !empty($data['ventas'])) {
    $controller = new SubhastadorController($conn);
    $ventas = $data['ventas'];
    foreach ($ventas as $venta) {
        $productId = $venta['productId'];
        $importe = $venta['importe'];
        $controller->registrarVentaProducto($productId, $importe); // Crear esta función en tu controlador
    }
}

if (isset($data['rechazados']) && !empty($data['rechazados'])) {
    $rechazados = $data['rechazados'];
    foreach ($rechazados as $productId) {
        $controller->marcarProducteComRebutjat($productId); // Crear esta función en tu controlador si no existe
    }
}

header('Content-Type: application/json'); // Asegura que el contenido es JSON
echo json_encode(['success' => true]);
exit();
?>