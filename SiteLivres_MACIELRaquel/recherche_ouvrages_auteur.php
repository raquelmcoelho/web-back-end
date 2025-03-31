<?php
if (isset($_GET["code"])){
    $code_auteur=$_GET["code"];
}
else die ("Code inconnu");

include "connexion.php" ;

$req_livres = "
    SELECT o.code, o.nom, json_agg(json_build_object('code', e.code, 'prix', e.prix)) as exemplaires 
    FROM ouvrage o 
    JOIN exemplaire e ON o.code = e.code_ouvrage 
    JOIN ecrit_par ec ON o.code = ec.code_ouvrage 
    WHERE ec.code_auteur = ?
    GROUP BY o.code, o.nom
";

try {
    $res_livres = $connexion->prepare($req_livres);
    $res_livres->execute([$code_auteur]);
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
