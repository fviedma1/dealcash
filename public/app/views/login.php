<?php
session_start();
require_once '../../app/config/db_connection.php';  // Ruta ajustada al archivo de conexión

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        // Preparar y ejecutar la consulta
        $stmt = $conn->prepare("SELECT id_usuari, nom_usuari, rol FROM usuari WHERE nom_usuari = :username AND contrasenya = :password");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        // Verificar las credenciales
        if ($stmt->rowCount() == 1) {
            // Usuario encontrado
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id_usuari'];
            $_SESSION['nom_usuari'] = $user['nom_usuari'];
            $_SESSION['rol'] = $user['rol'];

            // Redirigir siempre a index.php
            header('Location: ../../index.php');
            exit;
        } else {
            // Credenciales incorrectas
            $error = "Credenciales incorrectas. Inténtalo de nuevo.";
        }
    } catch (PDOException $e) {
        $error = "Error al conectar con la base de datos: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../../assets/css/header.css">
    <link rel="stylesheet" href="../../assets/css/login.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="icon" href="../../assets/img/favicon.ico" type="image/x-icon">
</head>
<body>
<!-- Header -->
<?php include 'header.php'; ?>
<!-- Login Form -->
<main class="form">
    <section class="login-section">
        <h1>Introdueix el teu usuari i contrasenya</h1>
        <form action="login.php" method="post">
            <?php if (isset($error)): ?>
                <p class="error"><?= htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <div class="input-container">
                <label for="username">Usuari</label>
                <input type="text" id="username" name="username" placeholder="Escriu el teu usuari" required>
            </div>
            <div class="input-container">
                <label for="password">Contrasenya</label>
                <input type="password" id="password" name="password" placeholder="Escriu la teva contrasenya" required>
            </div>
            <button type="submit" class="login-button">Inicia Sessió</button>
        </form>
    </section>
</main>
<!-- Footer -->
<?php include 'footer.php'; ?>

</body>
</html>