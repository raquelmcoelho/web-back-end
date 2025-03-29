<?php
header("Content-Type: application/json; charset=UTF-8");
$pdo = new PDO("pgsql:host=localhost;dbname=livres", "postgres", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$debnom = $_GET['debnom'] ?? '';
$stmt = $pdo->prepare("SELECT code, nom, prenom FROM auteurs WHERE nom ILIKE ?");
$stmt->execute(["%$debnom%"]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
