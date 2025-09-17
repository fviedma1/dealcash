<?php
session_start();
require_once '../config/db_connection.php';
require_once '../controllers/SubhastadorController.php';

// Esta función comprobará si el usuario está logueado y es un subhastador
function esSubhastador() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'subhastador';
}

// Redirigir al usuario a la página de login si no está logueado como subhastador
if (!esSubhastador()) {
    header("Location: ../views/login.php");
    exit();
}

// Manejar la lógica del formulario de creación de subhasta
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dataHora = $_POST['dataHora'];
    $descripcio = $_POST['descripcio'];
    $percentatge = isset($_POST['percentatge']) ? $_POST['percentatge'] : 10; // Valor por defecto
    $productes = isset($_POST['productes']) ? $_POST['productes'] : []; // Productos seleccionados

    // Verificar que la sesión tenga el nom_usuari del subhastador
    if (empty($_SESSION['nom_usuari'])) {
        echo "Username del subhastador no está definido. Por favor, asegúrate de estar logueado.";
        exit();
    }

    $usernameSubhastador = $_SESSION['nom_usuari']; // Username del subhastador logueado

    try {
        // Obtener el id del subhastador
        $sql = "SELECT id_usuari FROM usuari WHERE nom_usuari = :username";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $usernameSubhastador);
        $stmt->execute();
        $subhastador = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$subhastador) {
            echo "El subhastador no existe en la base de datos.";
            exit();
        }

        $subhastadorId = $subhastador['id_usuari'];

        // Iniciar transacción solo si llegamos a este punto sin errores
        $conn->beginTransaction();

        // Guardar la subhasta en la base de datos, incluyendo el percentatge
        $sql = "INSERT INTO subhasta (data_hora, descripcio, percentatge, subhastador_id) VALUES (:data_hora, :descripcio, :percentatge, :subhastador_id)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':data_hora', $dataHora);
        $stmt->bindParam(':descripcio', $descripcio);
        $stmt->bindParam(':percentatge', $percentatge);
        $stmt->bindParam(':subhastador_id', $subhastadorId);

        if ($stmt->execute()) {
            $subhastaId = $conn->lastInsertId(); // Obtener el ID de la subasta creada

            // Asignar productos a la subhasta
            $subhastadorController = new SubhastadorController($conn);
            foreach ($productes as $productId) {
                $subhastadorController->assignProductToAuction($productId, $subhastaId);
            }

            // Confirmar transacción
            $conn->commit();

            // Redirigir a la página de listado de subhastes
            header("Location: ../views/subhastes.php");
            exit();
        } else {
            // Rollback solo si la ejecución falla
            $conn->rollBack();
            echo "Error en crear la subhasta.";
        }
    } catch (PDOException $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        echo "Error en crear la subhasta: " . $e->getMessage();
    }
}
?>