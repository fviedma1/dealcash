<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../controllers/ProductController.php';
require_once '../config/depuracio_config.php';

$productController = new ProductController();

// Inicializar variables de filtrado
$searchValue = '';
$priceFilterValue = '';

// Comprobar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $searchValue = isset($_GET['searchBar']) ? trim($_GET['searchBar']) : '';
    $priceFilterValue = isset($_GET['priceFilter']) ? $_GET['priceFilter'] : '';
}

function normalizeText($text) {
    return strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $text));
}

$searchValue = normalizeText($searchValue);

// Obtener todos los productos
$products = $productController->getAllProducts(); // Asegúrate de que este método obtenga todos los productos sin paginación

// Filtrar productos
$filteredProducts = array_filter($products, function($product) use ($searchValue, $priceFilterValue) {
    // Normalizar y comprobar nombre del producto
    $productName = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $product['nom']));
    $matchesSearch = empty($searchValue) || strpos($productName, $searchValue) !== false;

    // Filtrar por precio
    $price = (float)$product['preu'];
    $priceMatch = true;

    if (!empty($priceFilterValue)) {
        if ($priceFilterValue === '+500') {
            $priceMatch = $price > 500;
        } else {
            list($minPrice, $maxPrice) = explode('-', $priceFilterValue);
            $priceMatch = $price >= (float)$minPrice && $price <= (float)$maxPrice;
        }
    }

    return $matchesSearch && $priceMatch;
});

// Calcular el total de productos filtrados
$totalProducts = count($filteredProducts);

// Devolver resultados
return [
    'products' => $filteredProducts, // Devolver todos los productos filtrados
    'totalProducts' => $totalProducts,
    'searchValue' => $searchValue,
    'priceFilterValue' => $priceFilterValue,
];
?>