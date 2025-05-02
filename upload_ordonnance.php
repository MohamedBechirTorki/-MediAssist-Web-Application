<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo 'Utilisateur non authentifié.';
    exit;
}

$mysqli = new mysqli("localhost", "root", "", "mediassistdb");
if ($mysqli->connect_error) {
    echo 'Erreur de connexion à la base de données.';
    exit;
}

if (!isset($_FILES['image_ordonnance']) || $_FILES['image_ordonnance']['error'] !== UPLOAD_ERR_OK) {
    echo 'Erreur lors du téléchargement du fichier.';
    exit;
}

$user_id = $_SESSION['user_id'];
$uploadDir = 'uploads/ordonnances/';

// Créer le dossier s’il n’existe pas
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$originalName = basename($_FILES['image_ordonnance']['name']);
$extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

if (!in_array($extension, $allowedExtensions)) {
    echo 'Extension de fichier non autorisée.';
    exit;
}

$newFileName = uniqid('ordonnance_') . '.' . $extension;
$destination = $uploadDir . $newFileName;

if (!move_uploaded_file($_FILES['image_ordonnance']['tmp_name'], $destination)) {
    echo 'Erreur lors du téléchargement du fichier.';
    exit;
}

// Enregistrement dans la base de données
$stmt = $mysqli->prepare("INSERT INTO ordonnances (user_id, nom_fichier) VALUES (?, ?)");
$stmt->bind_param("is", $user_id, $newFileName);

if ($stmt->execute()) {
    // Send a simple success message
    echo 'Ordonnance ajoutée avec succès.';
} else {
    // Send a failure message
    echo 'Erreur lors de l\'enregistrement en base de données.';
}

$mysqli->close();
?>
