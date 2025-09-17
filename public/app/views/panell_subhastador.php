<?php
require_once '../controllers/SubhastadorController.php';
require_once '../config/db_connection.php';
require_once '../controllers/ordenar_productes.php';
session_start();

// Verificar si el usuario ha iniciado sesión y es un subhastador
if (!isset($_SESSION['loggedin']) || $_SESSION['rol'] !== 'subhastador') {
    header('Location: ../../index.php');
    exit;
}

$subhastadorController = new SubhastadorController($conn);

// Obtener el estado seleccionado del filtro
$estadoFiltro = isset($_GET['estatFilter']) ? $_GET['estatFilter'] : '';

// Llamar al método del controlador para obtener productos según el estado
$products = $subhastadorController->getProductsUserName($estadoFiltro);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id'], $_POST['new_state'], $_POST['message'])) {
        $subhastadorController->updateProductState($_POST['product_id'], $_POST['new_state'], $_POST['message']);
    }
    if (isset($_POST['product_id'], $_POST['descripcio_llarga'], $_POST['descripcio_curta'], $_POST['observacions'])) {
        $subhastadorController->updateProductDescriptions($_POST['product_id'], $_POST['descripcio_llarga'], $_POST['descripcio_curta'], $_POST['observacions']);
    }

    if (isset($_POST['product_id'], $_POST['auction_id'])) {
        $subhastadorController->assignProductToAuction($_POST['product_id'], $_POST['auction_id']);
    }
    if (isset($_POST['unassign_product_id'])) {
        $subhastadorController->unassignProductFromAuction($_POST['unassign_product_id']);
    }
    header('Location: panell_subhastador.php');
    exit();
}

$order = isset($_GET['order']) ? $_GET['order'] : '';
$direction = isset($_GET['direction']) ? $_GET['direction'] : 'asc';

foreach ($products as &$product) {
    $product['likes'] = $subhastadorController->getLikesCount($product['id_producte']);
}

$products = ordenarProductos($products, $order, $direction);

// Obtener todas las subastas disponibles
try {
    $sql = "SELECT id_subhasta, data_hora, descripcio, estat FROM subhasta";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $subhastes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al recuperar las subhastas: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panell Subhastador</title>
    <link rel="stylesheet" href="../../assets/css/header.css">
    <link rel="stylesheet" href="../../assets/css/panell_subhastador.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="icon" href="../../assets/img/favicon.ico" type="image/x-icon">
</head>

<body>

    <!-- Header -->
    <?php include 'header.php'; ?>

    <section class="filter-section-container">
        <h1>Tots els productes</h1>
        <form class="filter-section" method="GET" action="panell_subhastador.php">
            <select id="estatFilter" name="estatFilter">
                <option value="">Filtrar per estat</option>
                <option value="validat" <?= $estadoFiltro === 'validat' ? 'selected' : ''; ?>>Validat</option>
                <option value="rebutjat" <?= $estadoFiltro === 'rebutjat' ? 'selected' : ''; ?>>Rebutjat</option>
                <option value="assignat" <?= $estadoFiltro === 'assignat' ? 'selected' : ''; ?>>Assignat</option>
                <option value="pendent" <?= $estadoFiltro === 'pendent' ? 'selected' : ''; ?>>Pendent</option>
            </select>
            <button type="submit">Aplicar Filtres</button>
        </form>
    </section>

    <main class="products-section">
        <table class="product-table">
            <thead>
                <tr>
                    <th>Imatge</th>
                    <th>Nom</th>
                    <th class="price-filter">Preu
                        <?php if ($order === 'price' && $direction === 'asc'): ?>
                            <a class="order-filter" href="?order=price&direction=desc&estatFilter=<?= htmlspecialchars($estadoFiltro); ?>">↑</a>
                        <?php elseif ($order === 'price' && $direction === 'desc'): ?>
                            <a class="order-filter" href="?order=price&direction=asc&estatFilter=<?= htmlspecialchars($estadoFiltro); ?>">↓</a>
                        <?php else: ?>
                            <a class="order-filter" href="?order=price&direction=asc&estatFilter=<?= htmlspecialchars($estadoFiltro); ?>">↓</a>
                            <a class="order-filter" href="?order=price&direction=desc&estatFilter=<?= htmlspecialchars($estadoFiltro); ?>">↑</a>
                        <?php endif; ?>
                    </th>
                    <th>Descripció curta</th>
                    <th>Descripció llarga</th>
                    <th>Observacions</th>
                    <th class="likes-filter">Me gusta
                        <?php if ($order === 'likes' && $direction === 'asc'): ?>
                            <a class="order-filter" href="?order=likes&direction=desc&estatFilter=<?= htmlspecialchars($estadoFiltro); ?>">↑</a>
                        <?php elseif ($order === 'likes' && $direction === 'desc'): ?>
                            <a class="order-filter" href="?order=likes&direction=asc&estatFilter=<?= htmlspecialchars($estadoFiltro); ?>">↓</a>
                        <?php else: ?>
                            <a class="order-filter" href="?order=likes&direction=asc&estatFilter=<?= htmlspecialchars($estadoFiltro); ?>">↓</a>
                            <a class="order-filter" href="?order=likes&direction=desc&estatFilter=<?= htmlspecialchars($estadoFiltro); ?>">↑</a>
                        <?php endif; ?>
                    </th>
                    <th>Pujat per</th>
                    <th>Estat</th>
                    <th>Nou estat</th>
                    <th>Missatge</th>
                    <th>Acció</th>
                    <th>Assignar a subhasta</th>
                    <th>Desassignar de subhasta</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $producto): ?>
                    <?php if ($producto['estat'] !== 'retirat'): ?>
                        <tr>
                            <td><img src="<?= isset($producto['foto']) && !is_null($producto['foto']) ? htmlspecialchars($producto['foto']) : '../../assets/img/default_image.png'; ?>" alt="Imatge del producte"></td>
                            <td><?= htmlspecialchars($producto['nom']); ?></td>
                            <td><?= htmlspecialchars($producto['preu']); ?>€</td>
                            <td>
                                <textarea name="descripcio_curta" class="expandable"><?= htmlspecialchars($producto['descripcio_curta'] ?? ''); ?></textarea>
                            </td>
                            <td>
                                <form method="post" action="panell_subhastador.php">
                                    <textarea name="descripcio_llarga" class="expandable"><?= htmlspecialchars($producto['descripcio_llarga'] ?? ''); ?></textarea>
                            </td>
                            <td>
                                <form method="post" action="panell_subhastador.php">
                                    <textarea name="observacions" class="expandable"><?= htmlspecialchars($producto['observacio_subhastador'] ?? ''); ?></textarea>
                            </td>
                            <td><?= $subhastadorController->getLikesCount($producto['id_producte']); ?></td>
                            <td><?= htmlspecialchars($producto['nom_usuari']); ?></td>
                            <td class="<?= strtolower(htmlspecialchars($producto['estat'])); ?>">
                                <p><?= htmlspecialchars($producto['estat']); ?></p>
                            </td>
                            <td>
                                <form method="post" action="panell_subhastador.php">
                                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($producto['id_producte']); ?>">
                                    <select name="new_state" required>
                                        <option value="pendent" <?= $producto['estat'] === 'pendent' ? 'selected' : ''; ?>>Pendent</option>
                                        <option value="validat" <?= $producto['estat'] === 'validat' ? 'selected' : ''; ?>>Validat</option>
                                        <option value="rebutjat" <?= $producto['estat'] === 'rebutjat' ? 'selected' : ''; ?>>Rebutjat</option>
                                    </select>
                            </td>
                            <td>
                                <textarea name="message" placeholder="Escriu un missatge al venedor (opcional)" class="expandable"></textarea>
                            </td>
                            <td>
    <!-- Formulari per assignar producte a subhasta -->
    <?php
    // Definir $subhasta antes de usarla
    $subhastaAsignada = null;
    foreach ($subhastes as $subhasta) {
        if ($subhasta['id_subhasta'] == $producto['subhasta_id']) {
            $subhastaAsignada = $subhasta;
            break;
        }
    }
    ?>
    <?php if (($producto['subhasta_id'] === null) || ($subhastaAsignada['estat'] !== 'iniciada' && $subhastaAsignada['estat'] !== 'finalitzada')): ?>
        <button type="submit">Actualitzar</button>
    <?php else: ?>
        <?php if ($subhastaAsignada['estat'] === 'iniciada'): ?>
            <p>Producte assignat a una subhasta iniciada</p>
        <?php elseif ($subhastaAsignada['estat'] === 'finalitzada'): ?>
            <p>Producte assignat a una subhasta finalitzada</p>
        <?php endif; ?>
    <?php endif; ?>
    </form>
</td>
<td>
    <!-- Formulari per assignar producte a subhasta -->
    <?php
    // Definir $subhasta antes de usarla
    $subhastaAsignada = null;
    foreach ($subhastes as $subhasta) {
        if ($subhasta['id_subhasta'] == $producto['subhasta_id']) {
            $subhastaAsignada = $subhasta;
            break;
        }
    }
    ?>
    <?php if (($producto['subhasta_id'] === null) || ($subhastaAsignada['estat'] !== 'iniciada' && $subhastaAsignada['estat'] !== 'finalitzada')): ?>
        <form method="post" action="panell_subhastador.php">
            <input type="hidden" name="product_id" value="<?= htmlspecialchars($producto['id_producte']); ?>">
            <select name="auction_id" required>
                <option value="">Selecciona una subhasta</option>
                <?php foreach ($subhastes as $subhasta): ?>
                    <?php if ($subhasta['estat'] !== 'iniciada'): ?>
                        <option value="<?= htmlspecialchars($subhasta['id_subhasta']); ?>"><?= htmlspecialchars($subhasta['descripcio']); ?> (<?= htmlspecialchars($subhasta['data_hora']); ?>)</option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
            <button type="submit">Asignar</button>
        </form>
    <?php else: ?>
        <?php if ($subhastaAsignada['estat'] === 'iniciada'): ?>
            <p>El producte ja està assignat a una subhasta iniciada.</p>
        <?php elseif ($subhastaAsignada['estat'] === 'finalitzada'): ?>
            <p>El producte ja està assignat a una subhasta finalitzada.</p>
        <?php endif; ?>
    <?php endif; ?>
</td>
<td>
    <!-- Formulari per desassignar producte de subhasta -->
    <form method="post" action="panell_subhastador.php">
        <input type="hidden" name="unassign_product_id" value="<?= htmlspecialchars($producto['id_producte']); ?>">
        <?php
        $mostrar_boton = true;
        foreach ($subhastes as $subhasta) {
            if ($subhasta['estat'] === 'iniciada' && $producto['subhasta_id'] === $subhasta['id_subhasta']) {
                echo '<p>No es pot desassignar (subhasta iniciada)</p>';
                $mostrar_boton = false;
                break; // Salir del bucle ya que encontramos una subasta iniciada
            } else if ($subhasta['estat'] === 'finalitzada' && $producto['subhasta_id'] === $subhasta['id_subhasta']) {
                echo '<p>No es pot desassignar (subhasta finalitzada)</p>';
                $mostrar_boton = false;
                break; // Salir del bucle ya que encontramos una subasta finalizada
            } else if ($producto['estat'] !== 'assignat') {
                echo '<p>No es pot desassignar <br>(no assignat)</p>';
                $mostrar_boton = false;
                break; // Salir del bucle ya que encontramos un producto no asignado
            }
        }
        if ($mostrar_boton) {
            echo '<button type="submit">Desassignar</button>';
        }
        ?>
    </form>
</td>
                        </tr>
                    <?php endif ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

</body>

</html>