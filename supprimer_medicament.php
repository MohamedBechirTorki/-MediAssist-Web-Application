<?php
// Connexion à la base de données
$mysqli = new mysqli("localhost", "root", "", "mediassistdb");

if ($mysqli->connect_error) {
    header("Location: dashboard.php?error=" . urlencode("Erreur de connexion à la base de données."));
    exit();
}

// Récupération de l'ID
$id = $_POST['id'] ?? null;

if (!$id) {
    header("Location: dashboard.php?error=" . urlencode("ID manquant pour la suppression."));
    exit();
}

// Requête de suppression
$stmt = $mysqli->prepare("DELETE FROM medicaments WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: dashboard.php?success=" . urlencode("Médicament supprimé avec succès."));
} else {
    header("Location: dashboard.php?error=" . urlencode("Erreur lors de la suppression : " . $stmt->error));
}

$stmt->close();
$mysqli->close();
exit();
