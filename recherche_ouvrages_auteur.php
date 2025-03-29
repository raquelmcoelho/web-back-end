<?php
header("Content-Type: application/json; charset=UTF-8");
$pdo = new PDO("pgsql:host=localhost;dbname=livres", "postgres", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$code_auteur = $_GET['code'] ?? 0;
$stmt = $pdo->prepare("
    SELECT o.code, o.nom, json_agg(json_build_object('nom', e.nom, 'code', e.code, 'prix', e.prix)) as exemplaires 
    FROM ouvrage o 
    JOIN exemplaire e ON o.code = e.code_ouvrage 
    JOIN ecrire ec ON o.code = ec.code_ouvrage 
    WHERE ec.code_auteur = ?
    GROUP BY o.code, o.nom
");
$stmt->execute([$code_auteur]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
