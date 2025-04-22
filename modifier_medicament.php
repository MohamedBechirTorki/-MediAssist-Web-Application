<?php
$mysqli = new mysqli("localhost", "root", "", "mediassistdb");
if ($mysqli->connect_error) {
    header("Location: dashboard.php?error=" . urlencode("Connexion échouée."));
    exit();
}

$id = $_POST['id'] ?? null;
$nom = $_POST['nom'] ?? '';
$posologie = $_POST['posologie'] ?? '';
$frequence = $_POST['frequence'] ?? 0;
$debut = $_POST['debut'] ?? '';
$fin = $_POST['fin'] ?? null;

if (!$id || !$nom || !$posologie || !$frequence || !$debut) {
    header("Location: dashboard.php?error=" . urlencode("Champs manquants pour la modification."));
    exit();
}

$stmt = $mysqli->prepare("UPDATE medicaments SET nom = ?, posologie = ?, frequence = ?, debut = ?, fin = ? WHERE id = ?");
$stmt->bind_param("ssissi", $nom, $posologie, $frequence, $debut, $fin, $id);

if ($stmt->execute()) {
    header("Location: dashboard.php?success=" . urlencode("Médicament modifié avec succès."));
} else {
    header("Location: dashboard.php?error=" . urlencode("Erreur lors de la modification : " . $stmt->error));
}
exit();