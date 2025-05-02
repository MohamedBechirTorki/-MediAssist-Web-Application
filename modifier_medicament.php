<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$mysqli = new mysqli("localhost", "root", "", "mediassistdb");
if ($mysqli->connect_error) {
    die("Erreur de connexion : " . $mysqli->connect_error);
}

// Récupération des données du formulaire
$id = $_POST['id'];
$nom = $_POST['nom'];
$debut = $_POST['debut'];
$fin = $_POST['fin'];
$temps = $_POST['temps'] ?? []; // tableau d'heures

// 1. Mise à jour des infos du médicament
$stmt = $mysqli->prepare("UPDATE medicaments SET nom = ?, debut = ?, fin = ? WHERE id = ?");
$stmt->bind_param("sssi", $nom, $debut, $fin, $id);
$stmt->execute();
$stmt->close();

// 2. Suppression des anciennes heures de prise
$stmt = $mysqli->prepare("DELETE FROM temps WHERE medicament_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

// 3. Insertion des nouvelles heures de prise
if (!empty($temps)) {
    $stmt = $mysqli->prepare("INSERT INTO temps (medicament_id, valeur) VALUES (?, ?)");
    foreach ($temps as $heure) {
        if (!empty($heure)) {
            $stmt->bind_param("is", $id, $heure);
            $stmt->execute();
        }
    }
    $stmt->close();
}

$mysqli->close();
header('Location: dashboard.php?success=modification');
exit;
?>
