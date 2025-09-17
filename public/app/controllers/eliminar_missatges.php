<?php
require_once '../controllers/VenedorController.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario ha iniciado sesión y es un vendedor
if (!isset($_SESSION['loggedin']) || $_SESSION['rol'] !== 'venedor') {
    header('Location: ../../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_id'])) {
    $messageId = intval($_POST['message_id']);
    $venedorController = new VenedorController();

    if ($venedorController->deleteMessageById($messageId)) {
        // Redirigir al usuario de vuelta a la página de mensajes con un mensaje de éxito
        header('Location:  ../views/missatges.php?status=success');
    } else {
        // Redirigir al usuario de vuelta a la página de mensajes con un mensaje de error
        header('Location:  ../views/missatges.php?status=error');
    }
    exit;
} else {
    // Si no viene del formulario de eliminación, redirigir a la página principal de mensajes
    header('Location: ../views/missatges.php');
    exit;
}
?>