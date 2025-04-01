<?php
if (isset($_GET["key"])){
    $key=$_GET["key"];
}
else die ("Clé inconnu");

include "connexion.php" ;

$req = "";
function ouvrages_par_titre() {
    global $req, $key;
    $req = "SELECT o.code, o.nom, json_agg(json_build_object('code', e.code, 'prix', e.prix)) as exemplaires 
            FROM ouvrage o 
            JOIN exemplaire e ON o.code = e.code_ouvrage 
            WHERE o.nom ILIKE ?
            GROUP BY o.code, o.nom
            ORDER BY o.nom";
    $key = "%$key%";
}

function ouvrages_par_auteur() {
    global $req, $key;
    $req = "SELECT o.code, o.nom, json_agg(json_build_object('code', e.code, 'prix', e.prix)) as exemplaires 
            FROM ouvrage o 
            JOIN exemplaire e ON o.code = e.code_ouvrage 
            JOIN ecrit_par ec ON o.code = ec.code_ouvrage 
            WHERE ec.code_auteur = ?
            GROUP BY o.code, o.nom";
}

function auteurs_par_nom() {
    global $req, $key;
    $req = "SELECT code, nom, prenom 
            FROM auteurs 
            WHERE nom ILIKE ? 
            ORDER BY nom";
    $key = "%$key%";
}


switch ($_GET["type"]) {
    case "ouvrages_titre":
        ouvrages_par_titre();
        break;
    case "ouvrages_auteur":
        ouvrages_par_auteur();
        break;
    case "auteurs":
        auteurs_par_nom();
        break;
    default:
        die("Type inconnu");
}

try {
    $res = $connexion->prepare($req);
    $res->execute([$key]);
} 
catch (PDOException $e) {
    die('Erreur : ' . $e→getMessage()) ;
}

header("Content-Type: application/json; charset=UTF-8");
echo json_encode($res->fetchAll(PDO::FETCH_ASSOC));
$res =null ;  
$connexion =null ;   
?>
