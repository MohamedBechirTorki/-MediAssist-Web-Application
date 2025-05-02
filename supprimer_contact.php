<?php
$mysqli = new mysqli("localhost", "root", "", "mediassistdb");
if ($mysqli->connect_error) exit;

$id = $_POST['id'] ?? null;
if (!$id) exit;

$stmt = $mysqli->prepare("DELETE FROM contacts_urgence WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();
