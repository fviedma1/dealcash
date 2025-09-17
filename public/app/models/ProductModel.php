<?php
require_once '../config/db_connection.php';

class ProductModel {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function getProducts() {
        $query = "SELECT * FROM producte WHERE estat IN ('validat', 'assignat')";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function filterProducts($searchValue, $priceFilterValue) {
        $query = "SELECT * FROM producte WHERE estat IN ('validat', 'assignat')";

        if ($searchValue) {
            $searchValue = '%' . strtolower($searchValue) . '%';
            $query .= " AND LOWER(nom) LIKE :searchValue";
        }

        if ($priceFilterValue) {
            if ($priceFilterValue === '+500') {
                $query .= " AND preu > 500";
            } else {
                list($minPrice, $maxPrice) = explode('-', $priceFilterValue);
                $query .= " AND preu BETWEEN :minPrice AND :maxPrice";
            }
        }

        $stmt = $this->conn->prepare($query);

        if ($searchValue) {
            $stmt->bindParam(':searchValue', $searchValue);
        }
        if ($priceFilterValue && $priceFilterValue !== '+500') {
            $stmt->bindParam(':minPrice', $minPrice, PDO::PARAM_INT);
            $stmt->bindParam(':maxPrice', $maxPrice, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductsByUserId($userId) {
        $query = "SELECT * FROM producte WHERE usuari_id = :userId AND estat IN ('validat', 'assignat','pendent', 'venut')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertProduct($nom, $descripcioCurta, $descripcioLlarga, $imatge, $preu, $observacions, $usuariId) {
        $query = "INSERT INTO producte (nom, descripcio_curta, descripcio_llarga, foto, preu, estat, observacio_subhastador, usuari_id, rol_usuari) 
                  VALUES (:nom, :descripcioCurta, :descripcioLlarga, :imatge, :preu, 'pendent', :observacions, :usuariId, 'venedor')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':descripcioCurta', $descripcioCurta);
        $stmt->bindParam(':descripcioLlarga', $descripcioLlarga);
        $stmt->bindParam(':imatge', $imatge);
        $stmt->bindParam(':preu', $preu);
        $stmt->bindParam(':observacions', $observacions);
        $stmt->bindParam(':usuariId', $usuariId);
        return $stmt->execute();
    }

    public function updateProductState($productId, $newState) {
        $query = "UPDATE producte SET estat = :newState WHERE id_producte = :productId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':newState', $newState);
        $stmt->bindParam(':productId', $productId);
        return $stmt->execute();
    }

    public function getSubhastadorByProductId($productId) {
        $query = "SELECT s.email FROM producte p
                  JOIN subhasta s ON p.id_subhasta = s.id
                  WHERE p.id_producte = :productId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':productId', $productId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function hasLikedProduct($productId, $userId = null, $guestId = null) {
        if ($userId) {
            $query = "SELECT * FROM likes_producte WHERE producte_id = :product_id AND usuari_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':product_id', $productId);
            $stmt->bindParam(':user_id', $userId);
        } else {
            $query = "SELECT * FROM likes_producte WHERE producte_id = :product_id AND guest_id = :guest_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':product_id', $productId);
            $stmt->bindParam(':guest_id', $guestId);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    public function addLike($productId, $userId = null, $guestId = null) {
        $query = "INSERT INTO likes_producte (producte_id, usuari_id, guest_id) VALUES (:product_id, :user_id, :guest_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':guest_id', $guestId);
        $stmt->execute();
    }

    public function removeLike($productId, $userId = null, $guestId = null) {
        if ($userId) {
            $query = "DELETE FROM likes_producte WHERE producte_id = :product_id AND usuari_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
        } else {
            $query = "DELETE FROM likes_producte WHERE producte_id = :product_id AND guest_id = :guest_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':guest_id', $guestId);
        }
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();
    }

    public function getLikesCount($productId) {
        $query = "SELECT COUNT(*) as total_likes FROM likes_producte WHERE producte_id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total_likes'];
    }

    public function getProductsPaginated($limit, $offset) {
        $query = "SELECT * FROM producte WHERE estat IN ('validat', 'assignat') LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalProducts() {
        $query = "SELECT COUNT(*) as total FROM producte WHERE estat IN ('validat', 'assignat')";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getAllProducts() {
        $query = "SELECT * FROM producte WHERE estat IN ('validat', 'assignat')";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>