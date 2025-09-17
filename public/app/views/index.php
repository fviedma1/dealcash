<?php
require_once '../controllers/VenedorController.php';
session_start();

$messages = []; // Inicializar los mensajes como un array vacío

// Verificar si el usuario ha iniciado sesión y es un vendedor
if (isset($_SESSION['loggedin']) && $_SESSION['rol'] == 'venedor') {
    $venedorId = $_SESSION['user_id']; // Obtener el ID del vendedor actual desde la sesión
    $venedorController = new VenedorController();
}
?>

<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deal Cash</title>
    <link rel="stylesheet" href="../../assets/css/header.css">
    <link rel="stylesheet" href="../../assets/css/index.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="icon" href="../../assets/img/favicon.ico" type="image/x-icon">
</head>

<body>

<?php include 'header.php'; ?>

<main>
    <section class="hero-section">
        <div class="hero-content">
            <h1>Benvingut a Deal Cash</h1>
            <p>Explora les nostres subhastes úniques i emocionants.</p>
            <a href="productes.php" class="btn-hero">Veure Productes</a>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

</body>

</html>
