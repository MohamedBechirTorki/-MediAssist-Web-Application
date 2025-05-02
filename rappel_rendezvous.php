<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mysqli = new mysqli("localhost", "root", "", "mediassistdb");
if ($mysqli->connect_error) {
    die("Erreur de connexion : " . $mysqli->connect_error);
}

date_default_timezone_set('Africa/Tunis');
$date_aujourdhui = date("Y-m-d");

// Récupérer tous les utilisateurs
$users = $mysqli->query("SELECT id, email FROM users");

while ($user = $users->fetch_assoc()) {
    $user_id = $user['id'];
    $email = $user['email'];

    // Vérifier les rendez-vous pour aujourd’hui
    $rdvs = $mysqli->query("
        SELECT * FROM rendezvous 
        WHERE user_id = $user_id AND date = '$date_aujourdhui'
    ");

    if ($rdvs->num_rows > 0) {
        $message = "Bonjour !<br><br>Vous avez un rendez-vous <strong>aujourd'hui ($date_aujourdhui)</strong>.<br>Veuillez ne pas l'oublier !<br><br>Bonne santé !";
        sendMail($email, "📅 Rappel de rendez-vous", $message);
    }
}

function sendMail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'mohamedbechirtorki@gmail.com';
        $mail->Password   = 'kthk pcdt pkyg gjcx';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('mohamedbechirtorki@gmail.com', 'MediAssist');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        echo "✅ Mail de rendez-vous envoyé à $to\n";
    } catch (Exception $e) {
        echo "❌ Erreur envoi mail : {$mail->ErrorInfo}\n";
    }
}
