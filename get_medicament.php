<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Utilisateur non authentifié.']);
    exit;
}

$mysqli = new mysqli("localhost", "root", "", "mediassistdb");
if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur de connexion à la base de données.']);
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo json_encode(['error' => 'ID invalide.']);
    exit;
}

$stmt = $mysqli->prepare("SELECT * FROM medicaments WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$med = $result->fetch_assoc();

if (!$med) {
    http_response_code(404);
    echo json_encode(['error' => 'Médicament non trouvé']);
    exit;
}

// Get hours of intake
$stmt = $mysqli->prepare("SELECT valeur FROM temps WHERE medicament_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$heures = [];
while ($row = $res->fetch_assoc()) {
    $heures[] = $row['valeur'];
}

$med['temps'] = $heures;

echo json_encode($med);
?>
