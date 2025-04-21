<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord - MediAssist</title>
    <script src="https://kit.fontawesome.com/your_kit_code.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="assets/css/style.css">

</head>
<body class="container">
    <header class="dash-header">
        <h2>Tableau de bord</h2>
        <div class="right-dash-header">
            <div class="notif">
                <i class="fa-regular fa-bell"></i>
            </div>
            <p><i class="fa-solid fa-user"></i> <span> <?= htmlspecialchars($_SESSION['user_name']) ?></span></p>
            
            <!--<a href="logout.php">Se déconnecter</a>-->
            <div class='change-color' id='changeColor'>
            <img src="./assets/images/moon.jpg" alt="">
            </div>
        </div>
    </header>
    <nav class="dash-nav">
        <div><span><i class="fa-solid fa-capsules"></i></span><p>Médicaments</p></div>
        <div><span><i class="fa-regular fa-calendar-check"></i></span> <p>Rendez-vous Médicaux</p></div>
        <div><span><i class="fa-solid fa-file-medical"></i></span> <p>Ordonnances</p></div>
        <div><span><i class="fa-regular fa-address-book"></i> </span> <p>Contacts d'Urgence</p></div>
    </nav>
    <main>
        <slide>
    </main>
    
</body>
</html>
