<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to index page
    header('Location: index.html');
    exit;
}

// The rest of the dashboard content goes here
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord - MediAssist</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <h2>Bienvenue sur votre tableau de bord, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h2>

    <!-- Dashboard content goes here -->
    <p>Bienvenue dans votre tableau de bord.</p>
    <a href="logout.php">Se déconnecter</a>
</body>
</html>
