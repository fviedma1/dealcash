<?php
require_once '../controllers/ProductController.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productId = $_POST['product_id'];

    $productController = new ProductController();
    $newState = 'retirat';

    $result = $productController->updateProductState($productId, $newState);


    if ($result) {
        $_SESSION['message'] = "Producte retirat correctamentq.";
    } else {
        $_SESSION['error'] = "Error al retirar el producte.";
    }

    // Redirigir a la vista del panel del vendedor
    header('Location: ../views/panell_venedor.php');
    exit;
} else {
    header('Location: ../views/panell_venedor.php');
    exit;
}