<?php
require_once '../controllers/VenedorController.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$venedorId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if ($venedorId !== null) {
    $venedorController = new VenedorController();
    $messages = $venedorController->getMessagesByVenedor($venedorId);
} else {
    $messages = [];
    // Podrías agregar algún mensaje de error o acción alternativa aquí si lo deseas.
}
?>

<header class="main-header">
    <div class="logo-title">
        <a href="index.php">
            <img class="header-logo" src="../../assets/img/logo_deal_cash.png" alt="Logo">
        </a>
        <a href="index.php" class="title">
            <h2>Deal Cash</h2>
        </a>
    </div>

    <nav class="navbar">
        <ul class="navbar-menu">
            <li><a href="index.php">Inici</a></li>
            <li><a href="productes.php">Productes</a></li>
            <li><a href="subhastes.php">Subhastes</a></li> <!-- Mostrar siempre -->
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
                <?php if ($_SESSION['rol'] == 'subhastador'): ?>
                    <li><a href="panell_subhastador.php">Panell subhastador</a></li>
                    <li><a href="subhasta_form.php">Formulari de subhasta</a></li>
                <?php elseif ($_SESSION['rol'] == 'venedor'): ?>
                    <li><a href="formulari_producte.php">Pujar producte</a></li>
                    <li><a href="panell_venedor.php">Panell venedor</a></li>
                    <?php if ($venedorId !== null): ?>
                        <li><a href="missatges.php">
                                <i class="fa-solid fa-envelope"></i> (<?= count($messages) ?>)
                            </a></li>
                    <?php else: ?>
                        <li><a href="missatges.php">
                                <i class="fa-solid fa-envelope"></i> (0)
                            </a></li>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="auth">
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
            <a href="logout.php" class="auth-button logout-button">
                <i class="fas fa-sign-out-alt"></i> Tancar Sessió
            </a>
        <?php else: ?>
            <a href="login.php" class="auth-button login-button">
                <i class="fa-solid fa-right-to-bracket"></i> Inicia Sessió
            </a>
        <?php endif; ?>
    </div>
</header>

<!-- Incluir Font Awesome para los íconos -->
<script src="../../assets/js/fontawesome.js"></script>