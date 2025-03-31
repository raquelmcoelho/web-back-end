<?php
session_start();

$nom = $_POST['nom'] ?? '';
$prenom = $_POST['prenom'] ?? '';
$adresse = $_POST['adresse'] ?? '';
$code_postal = $_POST['code_postal'] ?? '';
$ville = $_POST['ville'] ?? '';
$pays = $_POST['pays'] ?? '';

if (empty($nom) || empty($prenom) || empty($adresse) || empty($code_postal) || empty($ville) || empty($pays)) {
    echo json_encode(["success" => false, "message" => "Tous les champs sont requis."]);
    exit;
}

include "connexion.php" ;

$req_inscription="SELECT inscrire_client(:nom, :prenom, :adresse :code_postal :ville :pays)";
try {
    $res_inscription = $connexion->prepare($req_inscription);
    $res_inscription->execute([
        'nom' => $nom, 
        'prenom' => $prenom, 
        'adresse' => $adresse, 
        'code_postal' => $code_postal, 
        'ville' => $ville, 
        'pays' => $pays
    ]);
    $code_client = $res_inscription->fetchColumn(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Erreur : ' . $e→getMessage()) ;
}

header("Content-Type: application/json; charset=UTF-8");
if ($code_client > 0) {
    setcookie("code_client", $code_client, strtotime("31 Dec 2050"), "/");
    echo json_encode(["success" => true, "code_client" => $code_client]);
} else {
    echo json_encode(["success" => false, "message" => "Client déjà inscrit."]);
}

$res_inscription =null ;  
$connexion =null ; 

?>