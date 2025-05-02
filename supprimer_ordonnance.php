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

// Récupération du nom de fichier pour suppression physique
$stmt = $mysqli->prepare("SELECT nom_fichier FROM ordonnances WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $nomFichier = $row['nom_fichier'];
    $cheminFichier = __DIR__ . "/uploads/ordonnances/" . $nomFichier;

    // Supprimer le fichier physique si existant
    if (file_exists($cheminFichier)) {
        unlink($cheminFichier);
    }

    $stmt->close();

    // Suppression dans la base de données
    $stmt = $mysqli->prepare("DELETE FROM ordonnances WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: dashboard.php?success=" . urlencode("Ordonnance supprimée avec succès."));
    } else {
        header("Location: dashboard.php?error=" . urlencode("Erreur lors de la suppression : " . $stmt->error));
    }
    $stmt->close();
} else {
    header("Location: dashboard.php?error=" . urlencode("Ordonnance non trouvée."));
}

$mysqli->close();
exit();
