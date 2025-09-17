<?php
require_once '../models/ProductModel.php';

class ProductController
{
    private $productModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
    }

    public function getProducts()
    {
        return $this->productModel->getProducts();
    }

    public function filterProducts($searchValue, $priceFilterValue)
    {
        return $this->productModel->filterProducts($searchValue, $priceFilterValue);
    }

    public function getProductsByUser($userId)
    {
        return $this->productModel->getProductsByUserId($userId);
    }

    public function crearProducte($data, $userId)
    {
        $nom = $data['nom'];
        $descripcioCurta = $data['descripcio-curta'];
        $descripcioLlarga = $data['descripcio-llarga'];
        $preu = $data['preu'];
        $observacions = isset($data['observacions']) ? $data['observacions'] : '';

        if (isset($_FILES['imatge']) && $_FILES['imatge']['error'] === UPLOAD_ERR_OK) {
            $imatgeTmpPath = $_FILES['imatge']['tmp_name'];
            $imatgeNom = $_FILES['imatge']['name'];
            $imatgeUploadPath = '../../vagrant_files/images/' . basename($imatgeNom);

            if (move_uploaded_file($imatgeTmpPath, $imatgeUploadPath)) {
                $imatge = $imatgeUploadPath;
            } else {
                throw new Exception('Error al subir la imagen');
            }
        } else {
            throw new Exception('Imagen no proporcionada o error en la subida');
        }

        $rol = $_SESSION['rol'];
        return $this->productModel->insertProduct($nom, $descripcioCurta, $descripcioLlarga, $imatge, $preu, $observacions, $userId, $rol);
    }

    public function updateProductState($productId, $newState)
    {
        return $this->productModel->updateProductState($productId, $newState);
    }

    public function getSubhastadorByProductId($productId)
    {
        return $this->productModel->getSubhastadorByProductId($productId);
    }

    public function hasLikedProduct($productId, $userId = null)
    {
        return $this->productModel->hasLikedProduct($productId, $userId, $userId ? null : session_id());
    }

    public function likeProduct($productId, $userId = null)
    {
        if ($this->hasLikedProduct($productId, $userId)) {
            $this->productModel->removeLike($productId, $userId, $userId ? null : session_id());
        } else {
            $this->productModel->addLike($productId, $userId, $userId ? null : session_id());
        }
    }

    public function getLikesCount($productId)
    {
        return $this->productModel->getLikesCount($productId);
    }

    public function getProductsPaginated($limit, $offset)
    {
        return $this->productModel->getProductsPaginated($limit, $offset);
    }

    public function getAllProducts()
    {
        return $this->productModel->getAllProducts();
    }

    public function getTotalProducts()
    {
        return $this->productModel->getTotalProducts();
    }
}
?>
