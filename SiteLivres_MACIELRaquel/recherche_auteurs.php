<?php
if (isset($_GET["debnom"])){
    $debnom=$_GET["debnom"];
}
else die ("Debnom inconnu");

include "connexion.php" ;

$req_auteurs="SELECT code, nom, prenom FROM auteurs WHERE nom ILIKE ? ORDER BY nom";
try {
    $res_auteurs = $connexion->prepare($req_auteurs);
    $res_auteurs->execute(["%$debnom%"]);
} 
catch (PDOException $e) {
    die('Erreur : ' . $e→getMessage()) ;
}

$auteurs= $res_auteurs->fetchAll(PDO::FETCH_ASSOC);     
header("Content-Type: application/json; charset=UTF-8");
echo json_encode($auteurs);
$res_auteurs =null ;  
$connexion =null ;   
?>
