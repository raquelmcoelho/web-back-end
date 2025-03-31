<?php
require_once 'connexion.php';
session_start();

$code_client = $_SESSION['code_client'] ?? null;
$panier = [];

if ($code_client) {
    $req = $connexion->prepare("
        SELECT o.nom, p.code_exemplaire, p.quantite, e.prix
        FROM panier p
        JOIN exemplaire e ON p.code_exemplaire = e.code
        JOIN ouvrage o ON o.code = e.code_ouvrage
        WHERE p.code_client = :code_client
    ");
    $req->execute(['code_client' => $code_client]);
    $panier = $req->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($panier);
?>