<?php
session_start();

// Destruir todas las variables de sesión
$_SESSION = array();

// Si se desea destruir la sesión completamente, también se deben borrar las cookies de sesión.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruir la sesión.
session_destroy();

// Redirigir al usuario de vuelta al índice.
header("Location: ../../index.php");
exit;
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tancant sessió</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="icon" href="../../assets/img/favicon.ico" type="image/x-icon">
</head>
<body>
    <p>Tancant sessió, si us plau esperi...</p>
</body>
</html>