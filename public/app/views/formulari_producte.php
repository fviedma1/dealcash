<?php
require_once '../controllers/ProductController.php';
require_once '../controllers/LoginController.php';

$loginController = new LoginController();
session_start(); // Iniciar la sesión para obtener el rol del usuario

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

$productController = new ProductController(); // Corrige el nombre de la variable
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Llamar al controlador para crear el producto
    $success = $productController->crearProducte($_POST, $_SESSION['user_id']); // Ahora también pasamos el id del usuario

    // Redirigir al panel correspondiente basado en el rol del usuario
    if ($_SESSION['rol'] === 'subhastador') {
        header("Location: panell_subhastador.php");
    } elseif ($_SESSION['rol'] === 'venedor') {
        header("Location: panell_venedor.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulari Producte</title>
    <link rel="stylesheet" href="../../assets/css/header.css">
    <link rel="stylesheet" href="../../assets/css/formulari_producte.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="icon" href="../../assets/img/favicon.ico" type="image/x-icon">
</head>

<body>

<!-- Header -->
<?php include 'header.php'; ?>
<!-- Formulario -->
<main>
    <section class="form-section">
        <h1>Omple els següents camps:</h1>
        <p>Els camps obligatoris van seguits de <strong>*</strong></p>
        <?php if ($success): ?>
            <p>Producto creado con éxito.</p>
        <?php endif; ?>

        <form action="formulari_producte.php" method="post" enctype="multipart/form-data">
            <label for="nom">Nom <strong>*</strong></label>
            <input type="text" id="nom" name="nom" placeholder="100 caracters máxim" required>

            <label for="descripcio-curta">Descripció curta <strong>*</strong></label>
            <input type="text" id="descripcio-curta" name="descripcio-curta" placeholder="255 caracters máxim" required>

            <label for="imatge">Selecciona la imatge <strong>*</strong></label>
            <input type="file" id="imatge" name="imatge" accept="image/*" required>

            <label for="descripcio-llarga">Descripció llarga <strong>*</strong></label>
            <input type="text" id="descripcio-llarga" name="descripcio-llarga" placeholder="1000 caracters máxim" required>

            <label for="preu">Preu <strong>*</strong></label>
            <input type="text" id="preu" name="preu" placeholder="10 digits i 2 decimals máxim. Ex. 99.99" required>

            <label for="observacions">Observacions</label>
            <input type="text" id="observacions" placeholder="255 caracters máxim" name="observacions">

            <button id="button_submit" type="submit">Publicar producte</button>
        </form>
    </section>
</main>

<!-- Footer -->
<?php include 'footer.php'; ?>

<!-- Enlace al archivo JavaScript externo -->
<script src="../../assets/js/formulari-producte.js"></script>
</body>

</html>