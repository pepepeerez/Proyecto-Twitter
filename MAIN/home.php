<?php
session_start();
require_once("../connection/connection.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$userId = $_SESSION['user_id'];


$query = "SELECT * FROM users WHERE id = $userId";
$result = mysqli_query($connect, $query);
$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tweet'])) {
    $tweetText = mysqli_real_escape_string($connect, $_POST['tweet']);
    if (!empty($tweetText)) {
        $insertTweetQuery = "INSERT INTO publications (userId, text, createDate) VALUES ($userId, '$tweetText', NOW())";
        mysqli_query($connect, $insertTweetQuery);
    }
}


$showAllTweets = isset($_GET['all']) && $_GET['all'] == '1';

if ($showAllTweets) {
    
    $tweetsQuery = "SELECT publications.*, users.username FROM publications 
                    JOIN users ON publications.userId = users.id 
                    ORDER BY publications.createDate DESC";
} else {
    
    $tweetsQuery = "SELECT publications.*, users.username FROM publications 
                    JOIN users ON publications.userId = users.id 
                    JOIN follows ON follows.userToFollowId = publications.userId 
                    WHERE follows.users_id = $userId 
                    ORDER BY publications.createDate DESC";
}

$tweetsResult = mysqli_query($connect, $tweetsQuery);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Página Principal</title>
    <link rel="stylesheet" href="../CSS/home.css">
</head>
<body>
    <div class="container">
        
        <a href="./logout.php" class="logout-button">Cerrar sesión</a>

        <h1>
            Bienvenido, 
            <a href="profile.php?user=<?php echo $userId; ?>" class="username-link">
                <strong><?php echo htmlspecialchars($user['username']); ?></strong>
            </a>
        </h1>
        
        <p class="user-info">Email: <?php echo htmlspecialchars($user['email']); ?></p>

        <h2>Escribe un nuevo Tweet</h2>
        <form method="POST" action="">
            <textarea name="tweet" rows="4" placeholder="¿Qué estás pensando?"></textarea><br>
            <button type="submit">Publicar</button>
        </form>

        <h2>Tweets</h2>
        <div class="filters">
            <a href="home.php?all=1">Ver todos los tweets</a> 
            <a href="home.php">Ver tweets de personas que sigues</a>
        </div>

        <?php if (mysqli_num_rows($tweetsResult) > 0): ?>
            <?php while ($tweet = mysqli_fetch_assoc($tweetsResult)): ?>
                <div class="tweet">
                    <p>
                        <strong>
                            <a href="profile.php?user=<?php echo $tweet['userId']; ?>" class="username-link">
                                <?php echo htmlspecialchars($tweet['username']); ?>
                            </a>:
                        </strong> 
                        <?php echo htmlspecialchars($tweet['text']); ?>
                    </p>
                    <small>Publicado el: <?php echo htmlspecialchars($tweet['createDate']); ?></small>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No hay tweets para mostrar.</p>
        <?php endif; ?>
    </div>
</body>
</html>

