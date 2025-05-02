<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "mediassistdb");
if ($mysqli->connect_error) exit;

$user_id = $_POST['user_id'] ?? null;
$nom = $_POST['nom'] ?? '';
$tel = $_POST['telephone'] ?? '';

if (!$user_id || !$nom || !$tel) exit;

$stmt = $mysqli->prepare("INSERT INTO contacts_urgence (user_id, nom, telephone) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $user_id, $nom, $tel);
$stmt->execute();
$id = $stmt->insert_id;
$stmt->close();

// Retourner HTML
echo "<div class='contact-item' id='contact-$id'>";
echo "<p><strong>Nom:</strong> " . htmlspecialchars($nom) . "</p>";
echo "<p><strong>Téléphone:</strong> " . htmlspecialchars($tel) . "</p>";
echo "<button class='btn-supprimer' onclick='supprimerContact($id)'>Supprimer</button>";
echo "</div>";
