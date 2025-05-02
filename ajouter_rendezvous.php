<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Utilisateur non authentifié.']);
    exit;
}

$mysqli = new mysqli("localhost", "root", "", "mediassistdb");
if ($mysqli->connect_error) {
    echo json_encode(['error' => 'Erreur de connexion à la base de données.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$date = $_POST['date'];
$heure = $_POST['heure'];
$type_consultation = $_POST['type_consultation'];
$lieu = $_POST['lieu'];
$note = isset($_POST['note']) ? $_POST['note'] : '';

$stmt = $mysqli->prepare("INSERT INTO rendezvous (user_id, date, heure, type_consultation, lieu, note) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssss", $user_id, $date, $heure, $type_consultation, $lieu, $note);

if ($stmt->execute()) {
    // Fetch the inserted appointment ID
    $appt_id = $stmt->insert_id;

    // Prepare the appointment data to return
    $appointment = [
        'id' => $appt_id,
        'date' => $date,
        'heure' => $heure,
        'type_consultation' => $type_consultation,
        'lieu' => $lieu,
        'note' => $note
    ];

    echo json_encode(['success' => 'Rendez-vous ajouté avec succès.', 'appointment' => $appointment]);
} else {
    echo json_encode(['error' => 'Erreur lors de l\'ajout du rendez-vous.']);
}
?>
