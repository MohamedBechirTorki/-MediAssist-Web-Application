<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $conn = new mysqli('localhost', 'username', 'password', 'database_name');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Supprimer le fichier dans le répertoire
    $sql = "SELECT * FROM ordonnances WHERE id = $id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $fileName = $row['nom_fichier'];
    unlink('uploads/' . $fileName); // Supprimer le fichier du répertoire

    // Supprimer l'entrée de la base de données
    $sql = "DELETE FROM ordonnances WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        echo "Ordonnance supprimée avec succès.";
    } else {
        echo "Erreur: " . $conn->error;
    }

    $conn->close();
}
?>
