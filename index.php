<?php
session_start();
require_once("./connection/connection.php");

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    $password = mysqli_real_escape_string($connect, $_POST['password']);

    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($connect, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: ./MAIN/home.php");
    } else {
        header("Location: ./error/error.php");
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="./CSS/index.css">
</head>
<body>
    <form method="POST" action="">
        <input type="email" name="email" placeholder="Correo electrónico" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit" name="login">Iniciar Sesión</button>
        <a href="./MAIN/register.php">Registrarse</a>
    </form>
</body>
</html>
