<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Accès non autorisé.");
}

$mysqli = new mysqli("localhost", "root", "", "mediassistdb");
if ($mysqli->connect_error) {
    die("Erreur de connexion : " . $mysqli->connect_error);
}

if (isset($_POST['appointment_id'])) {
    $appointment_id = $_POST['appointment_id'];
    
    // Préparer la requête pour supprimer le rendez-vous
    $stmt = $mysqli->prepare("DELETE FROM rendezvous WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $appointment_id, $_SESSION['user_id']);
    if ($stmt->execute()) {
        echo "Rendez-vous supprimé avec succès!";
    } else {
        echo "Erreur lors de la suppression du rendez-vous.";
    }
}
?>
