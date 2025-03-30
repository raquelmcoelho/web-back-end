<?php 
require 'counter.php'; 
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliothèque Virtuelle</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="script.js"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <section id="number_of_visitors">
        <h5>Nombre de visiteurs: <?php echo $counter; ?></h5>
    </section>

    <section id="title">
        <h1>Bibliothèque Virtuelle</h1>
    </section>  

    <section id="menu">
        <h5>Bienvenue Nom Prénom</h5>
        <a href="panier.php?"><h5>Voir le Panier</h5></a>
        <h5>Quitter</h5>
    </section>

</header>

<nav>
    Recherche :
    <label for="par-auteur">Par Auteur:</label>
    <input type="text" id="debnom" onkeyup="recherche_auteurs()">
    <br>
    <label for="par-titre">Par Titre:</label>
    <input type="text" id="debtitre" onkeyup="recherche_ouvrages_titre()">
    <br>
</nav>

<section>
    <p>Bienvenue sur le site de la bibliothèque virtuelle.</p>
    <div id="div-gauche"></div>
    <div id="div-droite"></div>
</section>

</body>
</html>

