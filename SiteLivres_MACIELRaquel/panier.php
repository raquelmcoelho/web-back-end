<?php
require_once 'connexion.php';
session_start();

$code_client = $_SESSION['code_client'] ?? null;
$code_exemplaire = $_POST['code_exemplaire'] ?? null;
$action = $_GET['action'] ?? null;

if (!$code_client || !$code_exemplaire || !$action) {
    echo json_encode(["message" => "Erreur: client, code ou action invalide"]);
    exit;
}

if ($action === 'add') {
    // Verifica se o item já está no carrinho
    $req = $connexion->prepare("
    SELECT quantite FROM panier WHERE code_client = :code_client AND code_exemplaire = :code_exemplaire
    ");
    $req->execute(['code_client' => $code_client, 'code_exemplaire' => $code_exemplaire]);
    $existe = $req->fetch(PDO::FETCH_ASSOC);

    if ($existe) {
    // Atualiza a quantidade
    $req = $connexion->prepare("
        UPDATE panier SET quantite = quantite + 1 
        WHERE code_client = :code_client AND code_exemplaire = :code_exemplaire
    ");
    } else {
    // Adiciona um novo item
    $req = $connexion->prepare("
        INSERT INTO panier (code_client, code_exemplaire, quantite) 
        VALUES (:code_client, :code_exemplaire, 1)
    ");
    }

    $req->execute(['code_client' => $code_client, 'code_exemplaire' => $code_exemplaire]);

    echo json_encode(["message" => "Livre ajouté au panier"]);
    exit;
}

if ($action === 'remove') {
    $req = $connexion->prepare("
        DELETE FROM panier WHERE code_client = :code_client AND code_exemplaire = :code_exemplaire
    ");
    $req->execute(['code_client' => $code_client, 'code_exemplaire' => $code_exemplaire]);

    echo json_encode(["message" => "Livre retiré du panier"]);
    exit;
}

?>

