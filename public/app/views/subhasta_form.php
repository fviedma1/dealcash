<?php
session_start();
require_once '../controllers/SubhastadorController.php';
require_once '../config/db_connection.php';

function esSubhastador()
{
    return isset($_SESSION['rol']) && $_SESSION['rol'] == 'subhastador';
}

if (!esSubhastador()) {
    header("Location: ../views/login.php");
    exit();
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

$subhastadorController = new SubhastadorController($conn);
$products = $subhastadorController->getProductsUserName('validat'); // Obtener productos validados.
?>

<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <title>Crear Subhasta</title>
    <link rel="stylesheet" href="../../assets/css/header.css">
    <link rel="stylesheet" href="../../assets/css/subhasta_form.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="icon" href="../../assets/img/favicon.ico" type="image/x-icon">
</head>

<body>
    <?php include 'header.php'; ?>
    <main>
        <section class="seccio-formulari">
            <h1>Crear subhasta</h1>
            <form method="POST" action="../models/CrearSubhastaModel.php" class="formulari">
                <label for="dataHora">Data i hora:</label>
                <input type="datetime-local" id="dataHora" name="dataHora" required>

                <label for="descripcio">Descripció:</label>
                <textarea id="descripcio" name="descripcio" rows="4" required></textarea>

                <label for="percentatge">Percentatge de la venda (%):</label>
                <input type="number" id="percentatge" name="percentatge" value="10" min="0" max="100" required>

                <input type="hidden" name="subhastador_username" value="<?= htmlspecialchars($username); ?>">

                <label for="productes">Selecciona productes per a la subhasta:</label>
                <select multiple id="productes" name="productes[]">
                    <?php foreach ($products as $product): ?>
                        <option value="<?= htmlspecialchars($product['id_producte']); ?>"><?= htmlspecialchars($product['nom']); ?> - €<?= htmlspecialchars($product['preu']); ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="submit" value="Crear subhasta" class="boto-enviar">
            </form>
        </section>
    </main>
    <?php include 'footer.php'; ?>

    <script src="../../assets/js/select-multiple.js"></script>
</body>

</html>