<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$mysqli = new mysqli("localhost", "root", "", "mediassistdb");
if ($mysqli->connect_error) {
    header("Location: dashboard.php?error=" . urlencode("Erreur de connexion : " . $mysqli->connect_error));
    exit();
}

$nom = $_POST['nom'] ?? null;
$debut = $_POST['debut'] ?? null;
$fin = $_POST['fin'] ?? null;
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;
$temps = $_POST['temps'] ?? []; // array of time strings (HH:MM)

if (!$user_id) {
    header("Location: dashboard.php?error=" . urlencode("Erreur : Aucun utilisateur connecté."));
    exit();
}

if (!$nom || !$debut || empty($temps)) {
    header("Location: dashboard.php?error=" . urlencode("Champs manquants ou invalides."));
    exit();
}

$userCheck = $mysqli->prepare("SELECT id FROM users WHERE id = ?");
$userCheck->bind_param("i", $user_id);
$userCheck->execute();
$result = $userCheck->get_result();

if ($result->num_rows === 0) {
    header("Location: dashboard.php?error=" . urlencode("L'utilisateur avec ID $user_id n'existe pas."));
    exit();
}

// Insertion du médicament
$stmt = $mysqli->prepare("INSERT INTO medicaments (user_id, nom, debut, fin) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $user_id, $nom, $debut, $fin);

if (!$stmt->execute()) {
    header("Location: dashboard.php?error=" . urlencode("Erreur SQL (médicament) : " . $stmt->error));
    exit();
}

$medicament_id = $stmt->insert_id;

// Insertion des heures
$tempStmt = $mysqli->prepare("INSERT INTO temps (medicament_id, valeur) VALUES (?, ?)");
foreach ($temps as $temp) {
    if (!preg_match('/^\d{2}:\d{2}$/', $temp)) continue;
    $tempStmt->bind_param("is", $medicament_id, $temp);
    $tempStmt->execute();
}

header("Location: dashboard.php?success=" . urlencode("Médicament et heures ajoutés avec succès."));
exit();
?>
