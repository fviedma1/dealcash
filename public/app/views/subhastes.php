<?php
session_start();

function esSubhastador()
{
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'subhastador';
}

require '../config/db_connection.php';
require '../controllers/SubhastadorController.php';

$controller = new SubhastadorController($conn);

$status = isset($_GET['status']) ? $_GET['status'] : 'totes';
$fechaInicio = isset($_GET['fechaInicio']) ? $_GET['fechaInicio'] : '';
$fechaFin = isset($_GET['fechaFin']) ? $_GET['fechaFin'] : '';

$subhastes = $controller->listarSubhastes($status, $fechaInicio, $fechaFin);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && esSubhastador()) {
    if (isset($_POST['nuevoEstado']) && isset($_POST['subhastaId'])) {
        $nuevoEstado = $_POST['nuevoEstado'];
        $subhastaId = $_POST['subhastaId'];
        $controller->cambiarEstadoSubhasta($subhastaId, $nuevoEstado);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if (isset($_POST['finalizarSubhasta']) && isset($_POST['subhastaId']) && isset($_POST['productosVendidos'])) {
        $subhastaId = $_POST['subhastaId'];
        $productosVendidos = json_decode($_POST['productosVendidos'], true); // Lista de IDs de productos vendidos
        $controller->cambiarEstadoSubhasta($subhastaId, 'finalitzada', $productosVendidos);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if (!empty($data['ventas'])) {
        foreach ($data['ventas'] as $venta) {
            $productId = $venta['productId'];
            $importe = $venta['importe'];
            $controller->registrarVentaProducto($productId, $importe);
        }
    }

    if (!empty($data['rechazados'])) {
        foreach ($data['rechazados'] as $productId) {
            $controller->marcarProducteComRebutjat($productId);
        }
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dni']) && isset($_POST['name']) && isset($_POST['import']) && esSubhastador()) {
    $dni = $_POST['dni'];
    $name = $_POST['name'];
    $importe = $_POST['import'];
    $productId = $_POST['productId'];

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veure Subhastes</title>
    <link rel="stylesheet" href="../../assets/css/header.css">
    <link rel="stylesheet" href="../../assets/css/subhastes.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="../../assets/img/favicon.ico" type="image/x-icon">
</head>

<body>

<!-- Header -->
<?php include 'header.php'; ?>

<section class="filter-section-container">
    <h1>Subhastes</h1>
    <form class="filter-section" method="GET" action="">
        <select id="status" name="status">
            <option value="totes" <?= $status === 'totes' ? 'selected' : '' ?>>Totes</option>
            <option value="obertes" <?= $status === 'obertes' ? 'selected' : '' ?>>Obertes</option>
            <option value="tancades" <?= $status === 'tancades' ? 'selected' : '' ?>>Tancades</option>
            <option value="iniciades" <?= $status === 'iniciades' ? 'selected' : '' ?>>Iniciades</option>
        </select>

        <label for="fechaInicio">Data Inici:</label>
        <input type="date" id="fechaInicio" name="fechaInicio" value="<?= htmlspecialchars($fechaInicio) ?>">

        <label for="fechaFin">Data Fi:</label>
        <input type="date" id="fechaFin" name="fechaFin" value="<?= htmlspecialchars($fechaFin) ?>">

        <button type="submit">Aplicar Filtres</button>
    </form>
</section>

<main class="products-section">
    <table class="product-table">
        <thead>
        <tr>
            <?php if (esSubhastador()): ?>
                <th>Accions</th>
            <?php endif; ?>
            <th>Estat</th>
            <th>Data i Hora</th>
            <th>Descripció</th>
            <th>Percentatge</th>
            <th>Productes</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($subhastes)): ?>
            <?php foreach ($subhastes as $subhasta): ?>
                <?php $subhastaAmbProductes = $controller->mostrarSubhastaAmbProductes($subhasta['id_subhasta']); ?>
                <tr>
                    <?php if (esSubhastador()): ?>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="subhastaId" value="<?php echo htmlspecialchars($subhasta['id_subhasta']); ?>">
                                <?php if ($subhasta['estat'] !== 'iniciada' && $subhasta['estat'] !== 'finalitzada'): ?>
                                    <button type="submit" name="nuevoEstado" value="iniciada" class="btn-start"><i class="fas fa-lock"></i></button>
                                <?php endif; ?>
                                <?php if ($subhasta['estat'] !== 'finalitzada'): ?>
                                    <button type="submit" name="nuevoEstado" value="oberta" class="btn-tick"><i class="fas fa-check"></i></button>
                                    <button type="submit" name="nuevoEstado" value="tancada" class="btn-cross"><i class="fas fa-times"></i></button>
                                <?php endif; ?>
                            </form>
                        </td>
                    <?php endif; ?>
                    <td><?php echo htmlspecialchars($subhasta['estat'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($subhasta['data_hora'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($subhasta['descripcio'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($subhasta['percentatge'] ?? ''); ?></td>
                    <td>
                        <?php if (!empty($subhastaAmbProductes['productes'])): ?>
                            <ul>
                                <?php foreach ($subhastaAmbProductes['productes'] as $producte): ?>
                                    <li>
                                        <?php echo htmlspecialchars($producte['nom'] ?? 'Sin nombre'); ?>
                                        <?php if (esSubhastador() && $subhasta['estat'] === 'iniciada'): ?>
                                            <input type="checkbox" class="venta-checkbox" data-product-id="<?php echo $producte['id_producte']; ?>" data-product-name="<?php echo htmlspecialchars($producte['nom']); ?>">
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php if (esSubhastador() && $subhasta['estat'] === 'iniciada'): ?>
                                <form method="POST" action="" id="finalizar-subhasta-form">
                                    <input type="hidden" name="subhastaId" value="<?php echo htmlspecialchars($subhasta['id_subhasta']); ?>">
                                    <input type="hidden" name="finalizarSubhasta" value="true">
                                    <input type="hidden" name="productosVendidos" id="productosVendidos">
                                    <button class="submit-button" type="submit">
                                        <i class="fa-solid fa-calendar-check"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            No hi ha productes assignats
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No hi ha subhastes disponibles</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</main>

<!-- Footer -->
<?php include 'footer.php'; ?>

<!-- incluir script external -->
<script src="../../assets/js/vendre_producte.js"></script>

<div id="product-popup" class="popup" style="display:none;">
    <div class="popup-content"></div>
</div>

</body>

</html>