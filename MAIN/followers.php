<?php
session_start();
require_once("../connection/connection.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$profileUserId = isset($_GET['user']) ? intval($_GET['user']) : 0;

$userQuery = "SELECT username FROM users WHERE id = $profileUserId";
$userResult = mysqli_query($connect, $userQuery);
$userData = mysqli_fetch_assoc($userResult);
$profileUsername = $userData['username'] ?? 'Usuario';

$query = "SELECT users.username, users.id FROM follows
          JOIN users ON follows.users_id = users.id
          WHERE follows.userToFollowId = $profileUserId";
$result = mysqli_query($connect, $query);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seguidores de <?php echo htmlspecialchars($profileUsername); ?></title>
    <link rel="stylesheet" href="../CSS/follow.css">
</head>
<body>
    <h1>Usuarios que siguen a <?php echo htmlspecialchars($profileUsername); ?></h1>
    <?php if (mysqli_num_rows($result) > 0): ?>
        <ul>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <li><a href="profile.php?user=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['username']); ?></a></li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No tiene seguidores.</p>
    <?php endif; ?>

    <a href="../MAIN/home.php">Volver a la página principal</a>
</body>
</html>

