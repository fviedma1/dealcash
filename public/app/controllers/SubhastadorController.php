<?php
require_once '../models/SubhastaModel.php';
require '../config/db_connection.php';

class SubhastadorController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function listarSubhastes($status = 'totes', $fechaInicio = null, $fechaFin = null) {
        $subhastaModel = new SubhastaModel($this->conn);
        return $subhastaModel->obtenirSubhastes($status, $fechaInicio, $fechaFin);
    }

    public function mostrarSubhasta($id) {
        $subhastaModel = new SubhastaModel($this->conn);
        return $subhastaModel->obtenirSubhasta($id);
    }

    public function mostrarSubhastaAmbProductes($id) {
        $subhastaModel = new SubhastaModel($this->conn);
        return $subhastaModel->obtenirSubhastaAmbProductes($id);
    }

    public function getProductsUserName($estadoFiltro = '') {
        if (!empty($estadoFiltro)) {
            $stmt = $this->conn->prepare("SELECT p.*, u.nom_usuari 
                                          FROM producte p 
                                          INNER JOIN usuari u ON p.usuari_id = u.id_usuari 
                                          WHERE p.estat = :estadoFiltro");
            $stmt->bindParam(':estadoFiltro', $estadoFiltro);
        } else {
            $stmt = $this->conn->prepare("SELECT p.*, u.nom_usuari 
                                          FROM producte p 
                                          INNER JOIN usuari u ON p.usuari_id = u.id_usuari");
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateProductState($productId, $newState, $mensaje = '') {
        try {
            // Lista de estados válidos para la columna `estat` de la tabla `producte`
            $estadosValidos = ['pendent', 'validat', 'rebutjat', 'assignat', 'venut', 'assignat a una subhasta', 'retirat'];

            if (!in_array($newState, $estadosValidos)) {
                throw new Exception("Estado no válido: $newState");
            }

            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("UPDATE producte SET estat = :newState WHERE id_producte = :productId");
            $stmt->bindParam(':newState', $newState);
            $stmt->bindParam(':productId', $productId);
            $stmt->execute();

            $stmt = $this->conn->prepare("SELECT usuari_id FROM producte WHERE id_producte = :productId");
            $stmt->bindParam(':productId', $productId);
            $stmt->execute();
            $vendedorId = $stmt->fetchColumn();

            if ($mensaje != '') {
                $stmt = $this->conn->prepare("INSERT INTO missatge (producte_id, venedor_id, missatge, estat) 
                                              VALUES (:productoId, :vendedorId, :mensaje, :newState)");
                $stmt->bindParam(':productoId', $productId);
                $stmt->bindParam(':vendedorId', $vendedorId);
                $stmt->bindParam(':mensaje', $mensaje);
                $stmt->bindParam(':newState', $newState);
                $stmt->execute();
            }

            $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateProductDescriptions($productId, $descripcio_llarga, $descripcio_curta, $observacions) {
        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("UPDATE producte SET descripcio_llarga = :descripcio_llarga, descripcio_curta = :descripcio_curta, observacio_subhastador = :observacions WHERE id_producte = :productId");
            $stmt->bindParam(':descripcio_llarga', $descripcio_llarga);
            $stmt->bindParam(':descripcio_curta', $descripcio_curta);
            $stmt->bindParam(':observacions', $observacions);
            $stmt->bindParam(':productId', $productId);
            $stmt->execute();

            $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function crearSubhasta() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dataHora = $_POST['dataHora'] ?? '';
            $descripcio = $_POST['descripcio'] ?? '';

            if ($this->validarEntrades($dataHora, $descripcio)) {
                try {
                    $this->conn->beginTransaction();

                    $stmt = $this->conn->prepare("INSERT INTO subhasta (data_hora, descripcio, ubicacio) 
                                                  VALUES (:data_hora, :descripcio, 'Ubicació predeterminada')");
                    $stmt->bindParam(':data_hora', $dataHora);
                    $stmt->bindParam(':descripcio', $descripcio);
                    $stmt->execute();

                    $this->conn->commit();

                    echo "Subhasta creada exitosament.";
                } catch (Exception $e) {
                    $this->conn->rollBack();
                    throw $e;
                }
            } else {
                echo "Si us plau, completi tots els camps obligatoris.";
            }
        } else {
            $this->mostrarFormulariCreacio();
        }
    }

    private function validarEntrades($dataHora, $descripcio) {
        return !empty($dataHora) && !empty($descripcio);
    }

    private function mostrarFormulariCreacio() {
        include 'views/subhasta_form.php';
    }

    public function getLikesCount($productId) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total_likes FROM likes_producte WHERE producte_id = :product_id");
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total_likes'];
    }

    public function assignProductToAuction($productId, $auctionId) {
        try {
            $inTransaction = $this->conn->inTransaction();
            if (!$inTransaction) {
                $this->conn->beginTransaction();
            }

            $newState = 'assignat';

            $stmt = $this->conn->prepare("UPDATE producte SET estat = :newState, subhasta_id = :auctionId WHERE id_producte = :productId");
            $stmt->bindParam(':newState', $newState);
            $stmt->bindParam(':productId', $productId);
            $stmt->bindParam(':auctionId', $auctionId);
            $stmt->execute();

            if (!$inTransaction) {
                $this->conn->commit();
            }
        } catch (Exception $e) {
            if (!$inTransaction) {
                $this->conn->rollBack();
            }
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function unassignProductFromAuction($productId) {
        try {
            $inTransaction = $this->conn->inTransaction();
            if (!$inTransaction) {
                $this->conn->beginTransaction();
            }

            $newState = 'validat';

            $stmt = $this->conn->prepare("UPDATE producte SET estat = :newState, subhasta_id = NULL WHERE id_producte = :productId");
            $stmt->bindParam(':newState', $newState);
            $stmt->bindParam(':productId', $productId);
            $stmt->execute();

            if (!$inTransaction) {
                $this->conn->commit();
            }
        } catch (Exception $e) {
            if (!$inTransaction) {
                $this->conn->rollBack();
            }
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function cambiarEstadoSubhasta($subhastaId, $nuevoEstado, $productosVendidos = []) {
        try {
            $this->conn->beginTransaction();

            // Verifica si el nuevoEstado es válido
            $estadoValido = in_array($nuevoEstado, ['oberta', 'tancada', 'iniciada', 'finalitzada']);
            if (!$estadoValido) {
                throw new Exception("Estado no válido: $nuevoEstado");
            }

            // Actualiza el estado de la subasta
            $stmt = $this->conn->prepare("UPDATE subhasta SET estat = :nuevoEstado WHERE id_subhasta = :subhastaId");
            $stmt->bindParam(':subhastaId', $subhastaId);
            $stmt->bindParam(':nuevoEstado', $nuevoEstado);
            $stmt->execute();

            if ($nuevoEstado === 'finalitzada') {
                // Actualiza el estado de los productos asociados
                $stmtVendidos = $this->conn->prepare("UPDATE producte SET estat = 'venut' WHERE id_producte = :productId AND subhasta_id = :subhastaId");
                $stmtRebutjats = $this->conn->prepare("UPDATE producte SET estat = 'rebutjat' WHERE id_producte = :productId AND subhasta_id = :subhastaId");

                foreach ($productosVendidos as $productId) {
                    $stmtVendidos->bindParam(':productId', $productId);
                    $stmtVendidos->bindParam(':subhastaId', $subhastaId);
                    $stmtVendidos->execute();
                }

                // Cambia el estado a 'rebutjat' para los productos que no están en la lista de vendidos
                if (!empty($productosVendidos)) {
                    $placeholders = implode(',', array_fill(0, count($productosVendidos), '?'));
                    $stmtNoVendidos = $this->conn->prepare("UPDATE producte SET estat = 'rebutjat' WHERE subhasta_id = ? AND id_producte NOT IN ($placeholders)");
                    $params = array_merge([$subhastaId], $productosVendidos);
                    $stmtNoVendidos->execute($params);
                } else {
                    // Si no hay productos vendidos, marca todos como 'rebutjat'
                    $stmtNoVendidos = $this->conn->prepare("UPDATE producte SET estat = 'rebutjat' WHERE subhasta_id = ?");
                    $stmtNoVendidos->execute([$subhastaId]);
                }
            }

            $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function registrarVentaProducto($productId, $importe) {
        try {
            $stmt = $this->conn->prepare("UPDATE producte SET estat = 'venut', importe = :importe WHERE id_producte = :productId");
            $stmt->bindParam(':importe', $importe);
            $stmt->bindParam(':productId', $productId);
            $stmt->execute();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    // Método para marcar como rebutjat
    public function marcarProducteComRebutjat($productId) {
        try {
            $stmt = $this->conn->prepare("UPDATE producte SET estat = 'rebutjat' WHERE id_producte = :productId");
            $stmt->bindParam(':productId', $productId);
            $stmt->execute();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}

// Manejador de peticiones POST para marcar un producto como rebutjat
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marcarComoRebutjat']) && isset($_POST['product_id'])) {
    $controller = new SubhastadorController($conn);
    $productId = $_POST['product_id'];

    try {
        $controller->marcarProducteComRebutjat($productId);
        echo "Producto marcado como rebutjat exitosamente";
    } catch (Exception $e) {
        echo "Error al marcar como rebutjat: " . $e->getMessage();
    }
}

// Manojador para cambiar el estado de la subasta y los productos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiarEstadoSubhasta']) && isset($_POST['subhasta_id']) && isset($_POST['nuevo_estado'])) {
    $controller = new SubhastadorController($conn);
    $subhastaId = $_POST['subhasta_id'];
    $nuevoEstado = $_POST['nuevo_estado'];
    $productosVendidos = isset($_POST['productosVendidos']) ? $_POST['productosVendidos'] : [];

    try {
        $controller->cambiarEstadoSubhasta($subhastaId, $nuevoEstado, $productosVendidos);
        echo "Estado de la subasta y de los productos actualizado exitosamente";
    } catch (Exception $e) {
        echo "Error al actualizar el estado de la subasta y de los productos: " . $e->getMessage();
    }
}
?>