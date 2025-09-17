<?php
require_once '../config/db_connection.php';
require_once '../controllers/SubhastadorController.php';
session_start();

// Verificar si el usuario ha iniciado sesión y es un subhastador
if (!isset($_SESSION['loggedin']) || $_SESSION['rol'] !== 'subhastador') {
    header('Location: ../../index.php');
    exit;
}

$subhastadorController = new SubhastadorController($conn);

// Obtener el valor del filtro si se ha enviado un formulario
$estadoFiltro = isset($_POST['estatFilter']) ? $_POST['estatFilter'] : '';

// Filtrar productos si se ha seleccionado un estado, si no, mostrar todos los productos
if ($estadoFiltro) {
    $products = $subhastadorController->getProductsByState($estadoFiltro);
} else {
    $products = $subhastadorController->getProductsUserName();
}

// Método del controlador para obtener productos filtrados por estado
class SubhastadorController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getProductsByState($state) {
        $stmt = $this->conn->prepare("SELECT p.*, u.nom_usuari FROM producte p INNER JOIN usuari u ON p.usuari_id = id_usuari WHERE p.estat = :state");
        $stmt->bindParam(':state', $state);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductsUserName() {
        $stmt = $this->conn->prepare("SELECT p.*, u.nom_usuari FROM producte p INNER JOIN usuari u ON p.usuari_id = id_usuari");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

