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

$user_id = $_SESSION['user_id'];

// Fetch ordonnances for the logged-in user
$query = "SELECT id, nom_fichier, date_upload FROM ordonnances WHERE user_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Display ordonnances as cards
    while ($row = $result->fetch_assoc()) {
        $date_upload = new DateTime($row['date_upload']);
        echo '
            <div class="card">
                <img src="uploads/ordonnances/' . $row['nom_fichier'] . '" class="card-img-top" alt="Ordonnance image">
                <div class="card-body">
                    <h5 class="card-title">Ordonnance #' . $row['id'] . '</h5>
                    <p class="card-text">Date de téléchargement: ' . $date_upload->format('d-m-Y H:i:s') . '</p>
                    <form action="delete_ordonnance.php" method="POST" onsubmit="return confirm(\'Voulez-vous vraiment supprimer cette ordonnance ?\')">
                        <input type="hidden" name="ordonnance_id" value="' . $row['id'] . '">
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                </div>
            </div>
            <br>';
    }
} else {
    echo '<p>Aucune ordonnance trouvée.</p>';
}

$mysqli->close();
?>
