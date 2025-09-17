<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../controllers/ProductController.php';
require_once '../config/depuracio_config.php';

$productController = new ProductController();

$limit = 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$searchValue = isset($_GET['searchBar']) ? trim($_GET['searchBar']) : '';
$priceFilterValue = isset($_GET['priceFilter']) ? trim($_GET['priceFilter']) : '';
$filteredProducts = [];

function normalizeText($text) {
    return strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $text));
}

$searchValue = normalizeText($searchValue);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $filteredProducts = $productController->filterProducts($searchValue, $priceFilterValue);
}

if ($filteredProducts) {
    $products = array_slice($filteredProducts, $offset, $limit);
    $totalProducts = count($filteredProducts);
} else {
    $products = [];
    $totalProducts = 0;
}

$totalPages = ceil($totalProducts / $limit);
$guestId = session_id();

if (isset($_POST['like_button'])) {
    $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $userId = $_SESSION['user_id'] ?? null;

    $productController->likeProduct($productId, $userId);

    header('Location: productes.php?page=' . $page);
    exit;
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productes</title>
    <link rel="stylesheet" href="../../assets/css/header.css">
    <link rel="stylesheet" href="../../assets/css/productes.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="icon" href="../../assets/img/favicon.ico" type="image/x-icon">
</head>
<body>
<?php include 'header.php'; ?>
<main class="products-main">
    <section class="product-filter">
        <h1>Productes</h1>
        <form method="GET" action="productes.php">
            <input type="text" id="searchBar" name="searchBar" value="<?= htmlspecialchars($searchValue); ?>" placeholder="Buscar producto...">
            <select id="priceFilter" name="priceFilter">
                <option value="">Filtrar por precio</option>
                <option value="0-99.99" <?= $priceFilterValue === '0-99.99' ? 'selected' : ''; ?>>0 - 99.99€</option>
                <option value="100-199.99" <?= $priceFilterValue === '100-199.99' ? 'selected' : ''; ?>>100 - 199.99€</option>
                <option value="200-299.99" <?= $priceFilterValue === '200-299.99' ? 'selected' : ''; ?>>200 - 299.99€</option>
                <option value="300-399.99" <?= $priceFilterValue === '300-399.99' ? 'selected' : ''; ?>>300 - 399.99€</option>
                <option value="400-499.99" <?= $priceFilterValue === '400-499.99' ? 'selected' : ''; ?>>400 - 499.99€</option>
                <option value="+500" <?= $priceFilterValue === '+500' ? 'selected' : ''; ?>>Más de 500€</option>
            </select>
            <button type="submit">Aplicar Filtros</button>
        </form>
    </section>

    <section class="products-section">
        <div class="product-flex">
            <?php if (empty($products)): ?>
                <p class="no-results">No s'han trobat productes relacionats amb la teva búsqueda.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <?php if (DEBUG_VISIBILITY_PRODUCT): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <img src="<?= htmlspecialchars($product['foto']); ?>" alt="<?= htmlspecialchars($product['nom']); ?>">
                            </div>
                            <h2 class="titol-tercer"><?= htmlspecialchars($product['nom']); ?></h2>
                            <p><?= htmlspecialchars($product['descripcio_curta']); ?></p>
                            <p><?= htmlspecialchars($product['preu']); ?>€</p>
                            <p class="text-likes">Likes: <?= $productController->getLikesCount($product['id_producte']); ?></p>
                            <form method="POST" action="productes.php">
                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id_producte']); ?>">
                                <?php
                                $userId = $_SESSION['user_id'] ?? null;
                                $guestId = $userId ? null : session_id();
                                if ($productController->hasLikedProduct($product['id_producte'], $userId, $guestId)): ?>
                                    <div class="eliminar-like">
                                    <button type="submit" name="like_button">
                                        <i class="fa-solid fa-heart"></i>
                                    </button>
                                    </div>
                                <?php else: ?>
                                    <div class="donar-like">
                                    <button type="submit" name="like_button">
                                        <i class="fa-regular fa-heart"></i>
                                    </button>
                                    </div>
                                <?php endif; ?>
                            </form>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php if (!empty($products)): ?>
            <div class="paginacio">
                <?php if ($page > 1): ?>
                    <a href="productes.php?page=<?= $page - 1; ?>">←</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="productes.php?page=<?= $i; ?>" <?= $i === $page ? 'class="actiu"' : ''; ?>><?= $i; ?></a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="productes.php?page=<?= $page + 1; ?>">→</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </section>
</main>
<?php include 'footer.php'; ?>
<script src="../../assets/js/script.js"></script>
</body>
</html>