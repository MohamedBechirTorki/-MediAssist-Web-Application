<?php
// Connexion à la base de données
require_once 'db_connection.php';

// Récupérer les ordonnances de la base de données
$query = "SELECT * FROM ordonnances WHERE user_id = ? ORDER BY date_creation DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

// Affichage des ordonnances
while ($row = $result->fetch_assoc()) {
    $image_path = $row['image_path'];  // Le chemin de l'image
    $date_creation = $row['date_creation'];  // La date de création de l'ordonnance
    $ordonnance_id = $row['id'];  // L'ID de l'ordonnance pour la suppression
    echo '<div class="ordonnance-item">';
    echo '<img src="' . $image_path . '" alt="Ordonnance" class="ordonnance-image">';
    echo '<p>Date : ' . $date_creation . '</p>';
    echo '<button class="delete-button" data-id="' . $ordonnance_id . '">Supprimer</button>';
    echo '</div>';
}
?>
