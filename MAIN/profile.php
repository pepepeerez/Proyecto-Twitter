<?php
session_start();
require_once("../connection/connection.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$loggedInUserId = $_SESSION['user_id'];
$profileUserId = isset($_GET['user']) ? intval($_GET['user']) : $loggedInUserId;
$isOwnProfile = ($loggedInUserId == $profileUserId);


$query = "SELECT * FROM users WHERE id = $profileUserId";
$result = mysqli_query($connect, $query);
$profileUser = mysqli_fetch_assoc($result);


$followingQuery = "SELECT * FROM follows WHERE users_id = $loggedInUserId AND userToFollowId = $profileUserId";
$isFollowing = mysqli_num_rows(mysqli_query($connect, $followingQuery)) > 0;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['follow'])) {
        mysqli_query($connect, "INSERT INTO follows (users_id, userToFollowId) VALUES ($loggedInUserId, $profileUserId)");
    } elseif (isset($_POST['unfollow'])) {
        mysqli_query($connect, "DELETE FROM follows WHERE users_id = $loggedInUserId AND userToFollowId = $profileUserId");
    } elseif (isset($_POST['update_description']) && $isOwnProfile) {
        $newDescription = mysqli_real_escape_string($connect, $_POST['description']);
        mysqli_query($connect, "UPDATE users SET description = '$newDescription' WHERE id = $profileUserId");
        $profileUser['description'] = $newDescription;
    }
    header("Location: profile.php?user=$profileUserId");
    exit();
}


$tweetsQuery = "SELECT * FROM publications WHERE userId = $profileUserId ORDER BY createDate DESC";
$tweetsResult = mysqli_query($connect, $tweetsQuery);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil de <?php echo htmlspecialchars($profileUser['username']); ?></title>
    <link rel="stylesheet" href="../CSS/profile.css">
</head>
<body>
    <div class="container">
        <a href="home.php" class="home-button">Inicio</a>
        <h1>Perfil de <strong><?php echo htmlspecialchars($profileUser['username']); ?></strong></h1>
        <p>Email: <?php echo htmlspecialchars($profileUser['email']); ?></p>
        <p>Descripción: <?php echo htmlspecialchars($profileUser['description']); ?></p>

        
        <?php if ($isOwnProfile): ?>
            <form method="POST" action="" class="edit-description">
                <textarea name="description" rows="3"><?php echo htmlspecialchars($profileUser['description']); ?></textarea>
                <button type="submit" name="update_description">Actualizar Descripción</button>
            </form>
        <?php else: ?>
            
            <form method="POST" action="">
                <?php if ($isFollowing): ?>
                    <button type="submit" name="unfollow" class="unfollow-button">Dejar de seguir</button>
                <?php else: ?>
                    <button type="submit" name="follow" class="follow-button">Seguir</button>
                <?php endif; ?>
            </form>
        <?php endif; ?>

        <div class="connections-links">
            <a href="followers.php?user=<?php echo $profileUserId; ?>&type=followers">Seguidores</a> |
            <a href="following.php?user=<?php echo $profileUserId; ?>&type=following">Seguidos</a>
        </div>

        <h2>Tweets de <?php echo htmlspecialchars($profileUser['username']); ?></h2>
        <?php if (mysqli_num_rows($tweetsResult) > 0): ?>
            <?php while ($tweet = mysqli_fetch_assoc($tweetsResult)): ?>
                <div class="tweet">
                    <p><?php echo htmlspecialchars($tweet['text']); ?></p>
                    <small>Publicado el: <?php echo htmlspecialchars($tweet['createDate']); ?></small>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No hay tweets para mostrar.</p>
        <?php endif; ?>
    </div>
</body>
</html>


