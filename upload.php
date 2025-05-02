<?php
if(isset($_POST['upload'])) {
    $file = $_FILES['file'];
    $fileName = $_FILES['file']['name'];
    $fileTmpName = $_FILES['file']['tmp_name'];
    $fileSize = $_FILES['file']['size'];
    $fileError = $_FILES['file']['error'];
    $fileType = $_FILES['file']['type'];

    // Vérifier si le fichier est valide
    if($fileError === 0) {
        // Déplacer le fichier dans un répertoire approprié
        $fileDestination = 'uploads/' . $fileName;
        move_uploaded_file($fileTmpName, $fileDestination);

        // Insérer dans la base de données
        $conn = new mysqli('localhost', 'username', 'password', 'database_name');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "INSERT INTO ordonnances (nom_fichier, date_upload) VALUES ('$fileName', CURRENT_TIMESTAMP)";
        if ($conn->query($sql) === TRUE) {
            echo "Ordonnance ajoutée avec succès";
        } else {
            echo "Erreur: " . $conn->error;
        }
        $conn->close();
    } else {
        echo "Erreur lors du téléchargement du fichier.";
    }
}
?>
