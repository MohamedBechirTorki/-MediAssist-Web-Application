<?php
session_start();
require_once 'includes/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];

    if (empty($email) || empty($mot_de_passe)) {
        $errors[] = "L'email et le mot de passe sont obligatoires.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id, mot_de_passe, nom FROM users WHERE email = ?");
        $stmt->execute([$email]);

        $user = $stmt->fetch();
        if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nom'];
            $_SESSION['user_email'] = $email;
            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = "Email ou mot de passe incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - MediAssist</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <h2>Se connecter</h2>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST">
        <label>Email:</label>
        <input type="email" name="email" required><br>

        <label>Mot de passe:</label>
        <input type="password" name="mot_de_passe" required><br>

        <button type="submit">Se connecter</button>
    </form>

    <p>Pas encore de compte ? <a href="registre.php">S'inscrire</a></p>
</body>
</html>
