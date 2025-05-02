<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

$mysqli = new mysqli("localhost", "root", "", "mediassistdb");
if ($mysqli->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed']));
}

$user_id = $_SESSION['user_id'];
$query = "SELECT date, heure, type_consultation, lieu, note FROM rendezvous WHERE user_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$rendezvous = [];
while ($row = $result->fetch_assoc()) {
    $rendezvous[] = $row;
}

echo json_encode(['success' => true, 'rendezvous' => $rendezvous]);
$stmt->close();
$mysqli->close();
?>
