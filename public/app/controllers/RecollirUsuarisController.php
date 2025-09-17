<?php
session_start();

function esSubhastador() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'subhastador';
}

require '../config/db_connection.php';
require '../controllers/SubhastadorController.php';

$controller = new SubhastadorController($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && esSubhastador()) {
    $users = $controller->obtenerUsuariosRegistrados();
    if ($users) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'users' => $users]);
        exit();
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error']);
        exit();
    }
}
?>