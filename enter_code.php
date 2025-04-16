<?php
if (isset($_GET[session_name()])) {
    session_id($_GET[session_name()]);
}
session_start();
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_email']) || !isset($_SESSION['confirmation_code'])) {
        echo "Accès non autorisé. Merci de passer par la page d'inscription.";
        exit;
    }
    $code_saisi = trim($_POST['confirmation_code']);

    // Pour debug, vérifie ce qui est en session
    var_dump($_SESSION['user_email'], $_SESSION['confirmation_code']);

    // Compare avec la base ou la session
    if ($code_saisi == $_SESSION['confirmation_code']) {
        $stmt = $pdo->prepare("UPDATE users SET is_confirmed = 1 WHERE email = ?");
        $stmt->execute([$_SESSION['user_email']]);
        header('Location: login.php');
        exit;
    } else {
        echo "<p style='color:red;'>Code invalide !</p>";
    }
}
?>
<form method="post">
    <label>Code de confirmation :</label>
    <input type="text" name="confirmation_code" required>
    <button type="submit">Vérifier</button>
</form>
