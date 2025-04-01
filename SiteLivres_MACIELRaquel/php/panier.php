<?php
require_once 'connexion.php';
session_start();

$code_client = $_SESSION['code_client'] ?? null;
$code_exemplaire = $_POST['code_exemplaire'] ?? null;
$action = $_GET['action'] ?? null;

if (
    !$code_client 
    || !$action 
    || !$code_exemplaire && ($action == 'ajouter' || $action == 'retirer')
) {
    echo json_encode(["success" => false, "message" => "Erreur: client, code ou action invalide"]);
    exit;
}

function ajouter() {
    global $connexion, $code_client, $code_exemplaire;
    $req = $connexion->prepare("SELECT quantite 
                                FROM panier 
                                WHERE code_client = :code_client 
                                AND code_exemplaire = :code_exemplaire");
    $req->execute(['code_client' => $code_client, 'code_exemplaire' => $code_exemplaire]);
    $existe = $req->fetch(PDO::FETCH_ASSOC);

    if ($existe) {
        $req = $connexion->prepare("UPDATE panier SET quantite = quantite + 1 
                                    WHERE code_client = :code_client 
                                    AND code_exemplaire = :code_exemplaire");
    } else {
        $req = $connexion->prepare("INSERT INTO panier (code_client, code_exemplaire, quantite) 
                                    VALUES (:code_client, :code_exemplaire, 1)");
    }

    $req->execute(['code_client' => $code_client, 'code_exemplaire' => $code_exemplaire]);
}

function retirer() {
    global $connexion, $code_client, $code_exemplaire;
    $req = $connexion->prepare("DELETE FROM panier 
                                WHERE code_client = :code_client 
                                AND code_exemplaire = :code_exemplaire");
    $req->execute(['code_client' => $code_client, 'code_exemplaire' => $code_exemplaire]);
}


function commander() {
    global $connexion, $code_client;
    $req = $connexion->prepare("SELECT code_exemplaire, quantite, e.prix 
                                FROM panier p 
                                JOIN exemplaire e ON p.code_exemplaire = e.code 
                                WHERE p.code_client = :code_client");
    $req->execute(['code_client' => $code_client]);
    $items = $req->fetchAll(PDO::FETCH_ASSOC);
    
    $req = $connexion->prepare("INSERT INTO commande (code_client, code_exemplaire, quantite, prix) 
                                VALUES (:code_client, :code_exemplaire, :quantite, :prix)");
    
    foreach ($items as $item) {
        $req->execute([
            'code_client' => $code_client,
            'code_exemplaire' => $item['code_exemplaire'],
            'quantite' => $item['quantite'],
            'prix' => $item['prix'],
        ]);
    }
}

function vider() {
    global $connexion, $code_client;
    $req = $connexion->prepare("DELETE FROM panier 
                                WHERE code_client = :code_client");
    $req->execute(['code_client' => $code_client]);
}

function recuperer(){
    global $connexion, $code_client;
    $panier = [];
    
    $req = $connexion->prepare("SELECT o.nom, ed.nom as editeur, p.code_exemplaire, p.quantite, e.prix
                                FROM panier p
                                JOIN exemplaire e ON p.code_exemplaire = e.code
                                JOIN ouvrage o ON o.code = e.code_ouvrage
                                JOIN editeurs ed ON ed.code = e.code_editeur
                                WHERE p.code_client = :code_client");
    $req->execute(['code_client' => $code_client]);
    $panier = $req->fetchAll(PDO::FETCH_ASSOC);

    return $panier;
}

header("Content-type: application/json; charset=utf-8");
switch ($action) {
    case "ajouter":
        ajouter();
        echo json_encode(["success" => true, "message" => "Livre ajouté au panier"]);
        exit;
        break;
    case "retirer":
        retirer();
        echo json_encode(["success" => true, "message" => "Livre retiré du panier"]);
        exit;
        break;
    case "vider":
        vider();
        echo json_encode(["success" => true, "message" => "Panier vidé"]);
        exit;
        break;
    case "commander":
        commander();
        vider();
        echo json_encode(["success" => true, "message" => "Commande passée avec succès"]);
        exit;
        break;
    case "recuperer":
        $panier = recuperer();
        echo json_encode(["success" => true, "panier" => $panier]);
        exit;
        break;
    default:
        break;
}

?>

