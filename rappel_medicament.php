<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mysqli = new mysqli("localhost", "root", "", "mediassistdb");
if ($mysqli->connect_error) {
    die("Erreur de connexion : " . $mysqli->connect_error);
}

date_default_timezone_set('Africa/Tunis');
$heure_actuelle = date("H:i");
$date_aujourdhui = date("Y-m-d");

// Récupérer tous les utilisateurs
$users = $mysqli->query("SELECT id, email FROM users");

while ($user = $users->fetch_assoc()) {
    $user_id = $user['id'];
    $email = $user['email'];

    // Médicaments actifs pour l'utilisateur
    $stmt = $mysqli->prepare("
        SELECT m.id, m.nom 
        FROM medicaments m 
        WHERE m.user_id = ? AND m.debut <= ? AND m.fin >= ?
    ");
    $stmt->bind_param("iss", $user_id, $date_aujourdhui, $date_aujourdhui);
    $stmt->execute();
    $meds = $stmt->get_result();

    $meds_to_remind = [];

    while ($med = $meds->fetch_assoc()) {
        $med_id = $med['id'];
        $med_nom = $med['nom'];

        $temps = $mysqli->query("SELECT valeur FROM temps WHERE medicament_id = $med_id");

        while ($row = $temps->fetch_assoc()) {
            $heure_prise = substr($row['valeur'], 0, 5); // HH:MM
            if ($heure_actuelle === $heure_prise) {
                $meds_to_remind[] = $med_nom;
            }
        }
    }

    if (!empty($meds_to_remind)) {
        $message = "Bonjour !<br><br>Il est <strong>$heure_actuelle</strong>. Il est temps de prendre vos médicaments :<ul>";
        foreach ($meds_to_remind as $med) {
            $message .= "<li>$med</li>";
        }
        $message .= "</ul><br>Bonne santé !";

        sendMail($email, "Rappel de prise de médicaments", $message);
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
        echo "✅ Mail envoyé à $to\n";
    } catch (Exception $e) {
        echo "❌ Erreur d'envoi de mail : {$mail->ErrorInfo}\n";
    }
}
