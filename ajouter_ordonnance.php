<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "mediassistdb");

if ($mysqli->connect_error) exit;

$user_id = $_POST['user_id'] ?? null;

if (!isset($_FILES['image_ordonnance']) || !$user_id) exit;

// Créer dossier si inexistant
$uploadDir = "uploads/ordonnances/";
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$nomFichier = basename($_FILES["image_ordonnance"]["name"]);
$cheminFichier = $uploadDir . $nomFichier;

if (move_uploaded_file($_FILES["image_ordonnance"]["tmp_name"], $cheminFichier)) {
    $stmt = $mysqli->prepare("INSERT INTO ordonnances (user_id, nom_fichier, date_upload) VALUES (?, ?, NOW())");
    $stmt->bind_param("is", $user_id, $nomFichier);
    $stmt->execute();
    $id = $stmt->insert_id;
    $stmt->close();

    // Affichage dynamique
    echo "<div class='ordonnance-item' id='ordonnance-$id'>";
    echo "<img src='$cheminFichier' alt='Ordonnance' style='max-width: 200px;' class='ordonnance-thumbnail' onclick='afficherImagePleinEcran(this.src)'><br>";
    echo "<p><strong>Nom du fichier:</strong> " . htmlspecialchars($nomFichier) . "</p>";
    echo "<p><strong>Date d'upload:</strong> " . date("Y-m-d H:i:s") . "</p>";
    echo "<button class='btn-supprimer' onclick='supprimerOrdonnance($id)'>Supprimer</button>";
    echo "</div>";
}
