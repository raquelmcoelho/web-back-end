<?php
    global $connexion;   // variable globale
    try{
        $connexion = new PDO("pgsql:host=localhost;dbname=livres", "postgres", "");
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch (PDOException $e){
            die ('Connexion échouée : ' . $e->getMessage());
    }
?>