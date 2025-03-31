<?php
if (isset($_GET["debtitre"])){
    $debtitre=$_GET["debtitre"];
}
else die ("Debtitre inconnu");

include "connexion.php" ;
$req_livres = "    SELECT o.code, o.nom, json_agg(json_build_object('code', e.code, 'prix', e.prix)) as exemplaires 
    FROM ouvrage o 
    JOIN exemplaire e ON o.code = e.code_ouvrage 
    WHERE o.nom ILIKE ?
    GROUP BY o.code, o.nom
    ORDER BY o.nom";
try {
    $res_livres = $connexion->prepare($req_livres);
    $res_livres->execute(["%$debtitre%"]);
} 
catch (PDOException $e) {
    die('Erreur : ' . $e→getMessage()) ;
}
$livres= $res_livres->fetchAll(PDO::FETCH_ASSOC);     
header("Content-Type: application/json; charset=UTF-8");
echo json_encode($livres);
$res_livres =null ;  
$connexion =null ;   
?>
