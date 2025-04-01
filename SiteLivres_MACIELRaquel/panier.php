<?php
require_once 'connexion.php';
session_start();

$code_client = $_SESSION['code_client'] ?? null;
$code_exemplaire = $_POST['code_exemplaire'] ?? null;
$action = $_GET['action'] ?? null;

if (!$code_client || !$action || !$code_exemplaire && ($action == 'add' || $action == 'remove')) {
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


if($action === 'commander') {
    // TODO loop
    // $req = $connexion->prepare("
    //     INSERT INTO commande (code_client, code_exemplaire, quantite, prix)
    //     VALUES (
    //     :code_client, 
    //     :code_exemplaire, 
    //     (SELECT quantite FROM panier WHERE code_client = :code_client AND code_exemplaire = :code_exemplaire),
    //     (SELECT prix FROM exemplaires WHERE code_exemplaire = :code_exemplaire LIMIT 1) * quantite
    //     );
    //     DELETE FROM panier WHERE code_client = :code_client;
    // ");
    // $req->execute(['code_client' => $code_client]);

    echo json_encode(["message" => "Commande passée avec succès"]);
    exit;
}

// TODO vider panier

// if($action === 'vider') {
//     $req = $connexion->prepare("
//         DELETE FROM panier WHERE code_client = :code_client
//     ");
//     $req->execute(['code_client' => $code_client]);

//     echo json_encode(["message" => "Panier vidé"]);
//     exit;
// }

if($action === 'afficher') {
    $panier = [];

    if ($code_client) {
        $req = $connexion->prepare("
            SELECT o.nom, ed.nom as editeur, p.code_exemplaire, p.quantite, e.prix
            FROM panier p
            JOIN exemplaire e ON p.code_exemplaire = e.code
            JOIN ouvrage o ON o.code = e.code_ouvrage
            JOIN editeurs ed ON ed.code = e.code_editeur
            WHERE p.code_client = :code_client
        ");
        $req->execute(['code_client' => $code_client]);
        $panier = $req->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode($panier);
    exit;
}


?>

