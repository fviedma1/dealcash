<?php
require_once '../controllers/VenedorController.php';
session_start();

// Verificar si el usuario ha iniciado sesión y es un vendedor
if (!isset($_SESSION['loggedin']) || $_SESSION['rol'] !== 'venedor') {
    header('Location: ../../index.php');
    exit;
}

$venedorId = $_SESSION['user_id']; // Obtener el ID del vendedor actual desde la sesión
$venedorController = new VenedorController();
$messages = $venedorController->getMessagesByVenedor($venedorId);
?>

<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Missatges del Venedor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/header.css">
    <link rel="stylesheet" href="../../assets/css/panell_venedor.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="../../assets/css/missatges.css"> <!-- Inclusión del nuevo archivo CSS -->
    <link rel="icon" href="../../assets/img/favicon.ico" type="image/x-icon">
</head>

<body>
<!-- Header -->
<?php include 'header.php'; ?>

<section class="message-section">
    <h1>Missatges dels subhastadors</h1>
    <?php if (count($messages) > 0): ?>
        <ul>
            <?php foreach ($messages as $message): ?>
                <li class="producte-<?= htmlspecialchars($message['estat'] ?? ''); ?>">
                    <div class="message-content">
                        <strong>Producte:</strong> <?= htmlspecialchars($message['product_name'] ?? 'Desconegut'); ?><br>
                        <strong>Estat:</strong> <?= htmlspecialchars($message['estat'] ?? 'Desconegut'); ?><br>
                        <strong>Missatge:</strong> <?= htmlspecialchars($message['missatge'] ?? ''); ?><br>
                    </div>
                    <form method="POST" action="../controllers/eliminar_missatges.php">
                        <input type="hidden" name="message_id" value="<?= htmlspecialchars($message['id_missatge']); ?>">
                        <button type="submit" class="delete-button"><i class="fas fa-trash"></i></button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No tens missatges.</p>
    <?php endif; ?>
</section>
<!-- Footer -->
<?php include 'footer.php'; ?>
</body>

</html>