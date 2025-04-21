<?php
session_start();

if (isset($_SESSION['user_email'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MediAssist - Accueil</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link id="darkMode" rel="stylesheet" href="assets/css/dark-style.css">
</head>
<body class="container">

  <header>
      <div class="left-part">
        <h1>MediAssist</h1>
        <nav>
          <a href="#about">About us</a>
          <a href="#features">Fonctionnalités</a>
        </nav>
    </div>
      <div class="rl">
        <a href="register.php">Créer un compte</a>
        <a href="login.php">Se connecter</a>
        <div class='change-color' id='changeColor'>
          <img src="./assets/images/moon.jpg" alt="">
        </div>
      </div> 
      
  </header>

  <main>
    <section id="about">
      <div class="content">
        <h2>Votre assistant santé peronnel, toujours à portée de main.</h2>
        <p>Gérez vos médicaments, rendez-vous et ordonnances facilement.</p>
      </div>
      <div class="image">
        <img src="./assets/images/home-pic.png" alt="">
      </div>


    </section>
  

  <section id="features">
    <div class="features-grid">
      <div>
        <div class="image"><img src="./assets/images/home-gestion.png" alt=""></div>
        <h3>Gestion des Médicaments</h3>
        <p>Ajoutez, modifiez et recevez des rappels pour vos prises de médicaments.</p>
      </div>
      <div>
        <div class="image"><img src="./assets/images/home-suivi.png" alt=""></div>
        <h3>Suivi des Rendez-vous</h3>
        <p>Organisez facilement vos consultations médicales avec des alertes.</p>
      </div>
      <div>
        <div class="image"><img src="./assets/images/home-ordonnance.png" alt=""></div>
        <h3>Ordonnances en Ligne</h3>
        <p>Stockez et consultez vos ordonnances depuis n’importe quel appareil.</p>
      </div>
    </div>
  </section>
</main>
  <footer>
    <p>&copy; 2025 MediAssist. Tous droits réservés.</p>
    <p>Créer par <a href="">Mohamed Bechir Torki</a></p>
  </footer>

  <script src='assets/js/main.js'></script>
</body>
</html>
