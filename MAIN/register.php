<?php
require_once("../connection/connection.php");

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($connect, $_POST['username']);
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    $password = mysqli_real_escape_string($connect, $_POST['password']);

    if (!empty($username) && !empty($email) && !empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 4]);
        $sql = "INSERT INTO users (username, email, password, description, createDate) VALUES ('$username', '$email', '$hashedPassword', '', NOW())";
        
        if (mysqli_query($connect, $sql)) {
            header("Location: ../index.php");
        } else {
            header("Location: ../error/error.php");
        }
    } else {
        echo "¡Todos los campos son obligatorios!";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="../CSS/register.css">
</head>
<body>
    <form method="POST" action="">
        <input type="text" name="username" placeholder="Nombre de usuario" required>
        <input type="email" name="email" placeholder="Correo electrónico" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit" name="register">Registrarse</button>
    </form>
</body>
</html>
