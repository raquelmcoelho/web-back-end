<?php
header("Content-Type: application/json; charset=UTF-8");
$pdo = new PDO("pgsql:host=localhost;dbname=livres", "postgres", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$debtitre = $_GET['debtitre'] ?? '';
$stmt = $pdo->prepare("
    SELECT o.code, o.nom, json_agg(json_build_object('code', e.code, 'prix', e.prix)) as exemplaires 
    FROM ouvrage o 
    JOIN exemplaire e ON o.code = e.code_ouvrage 
    WHERE o.nom ILIKE ?
    GROUP BY o.code, o.nom
");
$stmt->execute(["%$debtitre%"]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
