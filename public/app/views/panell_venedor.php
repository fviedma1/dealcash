<?php
require_once '../controllers/ProductController.php';
session_start();

// Verificar si el usuario ha iniciado sesión y es un vendedor
if (!isset($_SESSION['loggedin']) || $_SESSION['rol'] !== 'venedor') {
    header('Location: ../../public/index.php');
    exit;
}

$venedorId = $_SESSION['user_id']; // Obtener el ID del vendedor actual desde la sesión
$productController = new ProductController();
$products = $productController->getProductsByUser($venedorId);
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panell del Venedor</title>
    <link rel="stylesheet" href="../../assets/css/header.css">
    <link rel="stylesheet" href="../../assets/css/panell_venedor.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="icon" href="../../assets/img/favicon.ico" type="image/x-icon">
</head>

<body>

<!-- Header -->
<?php include 'header.php'; ?>

<!-- Productos -->
<main class="product-main">
    <section class="product-section">
        <?php if (count($products) > 0): ?>
            <h1>Els meus productes</h1>
            <section class="product-flex">
                <?php foreach ($products as $product): ?>
                    <div class="product-card <?= 'estado-' . htmlspecialchars($product['estat']); ?>">
                        <div class="product-image">
                            <img src="<?= htmlspecialchars($product['foto']); ?>" alt="<?= htmlspecialchars($product['nom']); ?>">
                        </div>
                        <h2><?= htmlspecialchars($product['nom']); ?></h2>
                        <p><?= htmlspecialchars($product['descripcio_curta']); ?></p>
                        <p>Estat: <?= htmlspecialchars($product['estat']); ?></p>
                        <p>Preu: €<?= htmlspecialchars($product['preu']); ?></p>
                        <!-- Opciones de retiro de producto -->
                        <?php
                        $allowedStates = ['pendent', 'validat', 'rebutjat', 'assignat', 'assignat a una subhasta'];
                        if (in_array($product['estat'], $allowedStates)): ?>
                            <?php if (isset($product['id_producte'])): ?>
                                <!-- Asegúrate de que la ruta de 'action' apunta al archivo correcto -->
                                <form method="POST" action="../../app/controllers/retirar_producte.php">
                                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id_producte']); ?>">
                                    <button type="submit" class="remove-button"><i class="fas fa-trash"></i></button>
                                </form>
                            <?php else: ?>
                                <p>Error: Clave "id_producte" no encontrada en el producto.</p>
                                <pre><?php print_r($product); ?></pre> <!-- Mostrar el array del producto completo para depuración -->
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </section>
        <?php else: ?>
            <div class="product-card">
                <h2>Ningun Producte</h2>
                <p>Sense productes disponibles.</p>
            </div>
        <?php endif; ?>
    </section>
</main>

<!-- Footer -->
<?php include 'footer.php'; ?>

</body>
</html>