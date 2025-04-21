
<?php
session_start();
require_once 'includes/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php'; // Assure-toi d’avoir installé PHPMailer via Composer

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];
    $confirm_mot_de_passe = $_POST['confirm_mot_de_passe'];
    $date_naissance = $_POST['date_naissance'];
    $genre = $_POST['genre'];

    // Validation
    if (empty($nom) || empty($email) || empty($mot_de_passe) || empty($confirm_mot_de_passe)) {
        $errors[] = "Tous les champs sont obligatoires.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email invalide.";
    }

    if ($mot_de_passe !== $confirm_mot_de_passe) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    // Vérifier si l'email existe déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    
    if ($stmt->rowCount() > 0) {
        $errors[] = "Cet email est déjà utilisé.";
    }

    // Si tout est bon
    if (empty($errors)) {
        $confirmation_code = rand(100000, 999999);
        $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (nom, email, mot_de_passe, date_naissance, genre, confirmation_code) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $email, $hash, $date_naissance, $genre, $confirmation_code]);

        $_SESSION['user_email'] = $email;

        // Envoi du mail via PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'mohamedbechirtorki@gmail.com';       // 🟡 Ton email Gmail
            $mail->Password   = 'kthk pcdt pkyg gjcx';          // 🔐 Mot de passe d’application
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('ton.email@gmail.com', 'MediAssist');
            $mail->addAddress($email, $nom);
            $mail->Subject = 'Confirmation de votre compte MediAssist';
            $mail->Body    = "Bonjour $nom,\n\nVoici votre code de confirmation : $confirmation_code\n\nMerci de le saisir sur le site pour confirmer votre compte.\n\nCordialement,\nL'équipe MediAssist";

            $mail->send();
            $_SESSION['user_email'] = $email;
            $_SESSION['confirmation_code'] = $confirmation_code;
            header('Location: enter_code.php?' . session_name() . '=' . session_id());

            
            exit;

        } catch (Exception $e) {
            $errors[] = "Erreur lors de l'envoi du mail : {$mail->ErrorInfo}";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - MediAssist</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    

    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" class="ls-form">
        <h2>Créer un compte</h2>
        <label>Nom complet:</label>
        <input type="text" name="nom" required><br>

        <label>Email:</label>
        <input type="email" name="email" required><br>

        <label>Mot de passe:</label>
        <input type="password" name="mot_de_passe" required><br>

        <label>Confirmer le mot de passe:</label>
        <input type="password" name="confirm_mot_de_passe" required><br>

        <label>Date de naissance:</label>
        <input type="date" name="date_naissance"><br>

        <label>Genre:</label>
        <select name="genre">
            <option value="Homme">Homme</option>
            <option value="Femme">Femme</option>
            <option value="Autre">Autre</option>
        </select><br>

        <button type="submit">S'inscrire</button>
        <p>Déjà un compte ? <a href="login.php">Se connecter</a></p>
    </form>

    
</body>
</html>
