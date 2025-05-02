<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Accès non autorisé.");
}

$mysqli = new mysqli("localhost", "root", "", "mediassistdb");
if ($mysqli->connect_error) {
    die("Erreur de connexion : " . $mysqli->connect_error);
}

$user_id = $_SESSION['user_id'];

// Récupérer les rendez-vous pour l'utilisateur
$stmt = $mysqli->prepare("SELECT * FROM rendezvous WHERE user_id = ? ORDER BY date, heure");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$appointments_result = $stmt->get_result();
$appointments = [];

while ($row = $appointments_result->fetch_assoc()) {
    $appointments[] = [
        'id' => $row['id'],  // ID unique
        'title' => $row['type_consultation'],
        'start' => $row['datee'] . ' ' . $row['heure'],  // Date et heure de l'événement
    ];
}

// Retourner les rendez-vous en format JSON
echo json_encode($appointments);
?>
