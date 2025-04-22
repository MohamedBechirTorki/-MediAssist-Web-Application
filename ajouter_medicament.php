<?php
// Affiche les erreurs PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connexion à la base de données
$mysqli = new mysqli("localhost", "root", "", "mediassistdb");
if ($mysqli->connect_error) {
    header("Location: dashboard.php?error=" . urlencode("Erreur de connexion : " . $mysqli->connect_error));
    exit();
}

// Récupération des données du formulaire
$nom = $_POST['nom'] ?? null;
$posologie = $_POST['posologie'] ?? null;
$frequence = isset($_POST['frequence']) ? intval($_POST['frequence']) : 0;
$debut = $_POST['debut'] ?? null;
$fin = $_POST['fin'] ?? null;
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;

if (!$user_id) {
    header("Location: dashboard.php?error=" . urlencode("Erreur : Aucun utilisateur connecté."));
    exit();
}

// Vérification des champs
if (!$nom || !$posologie || $frequence <= 0 || !$debut) {
    header("Location: dashboard.php?error=" . urlencode("Champs manquants ou invalides."));
    exit();
}

// Vérifier si l'utilisateur existe
$userCheck = $mysqli->prepare("SELECT id FROM users WHERE id = ?");
$userCheck->bind_param("i", $user_id);
$userCheck->execute();
$result = $userCheck->get_result();

if ($result->num_rows === 0) {
    header("Location: dashboard.php?error=" . urlencode("L'utilisateur avec ID $user_id n'existe pas."));
    exit();
}

// Insertion dans la base
$stmt = $mysqli->prepare("INSERT INTO medicaments (user_id, nom, posologie, frequence, debut, fin) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ississ", $user_id, $nom, $posologie, $frequence, $debut, $fin);

if (!$stmt->execute()) {
    header("Location: dashboard.php?error=" . urlencode("Erreur SQL : " . $stmt->error));
    exit();
}

// Redirection succès
header("Location: dashboard.php?success=" . urlencode("Médicament ajouté avec succès."));
exit();
?>
