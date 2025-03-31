<?php
    require 'counter.php';
    session_start();
    if (! isset($_SESSION['code_client']) && isset($_COOKIE['code_client'])) {
        include "connexion.php";
        $res = $connexion->prepare("SELECT nom, prenom FROM clients WHERE code_client = ?");
        $res->execute([$_COOKIE['code_client']]);
        $client = $res->fetch(PDO::FETCH_ASSOC);
        if ($client) {
            $_SESSION['code_client'] = $_COOKIE['code_client'];
            $_SESSION['nom']         = $client['nom'];
            $_SESSION['prenom']      = $client['prenom'];
        }
    }
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
        <h5>Nombre de visiteurs:                                                                                                                                                                                                                                 <?php echo $counter; ?></h5>
    </section>

    <section id="title">
        <h1>Bibliothèque Virtuelle</h1>
    </section>

    <section id="menu">
        <?php if (isset($_SESSION['nom'])): ?>
            <h5>Bienvenue <?php echo $_SESSION['nom'] ?> <?php echo $_SESSION['prenom'] ?></h5>
            <button type="button" onclick="montrer_panier()">Voir le Panier</button>
            <button type="button" onclick="deconnecter()">Quitter</button>
        <?php else: ?>
            <button type="button" onclick="montrer_formulaire()">Inscription</button>
        <?php endif; ?>
    </section>

</header>

<div id="panier-div" style="display:none;">
</div>

<div id="search-div">
    <nav>
        Recherche :
        <label for="debnom">Par Auteur:</label>
        <input type="text" id="debnom" onkeyup="recherche_auteurs()">
        <br>
        <label for="debtitre">Par Titre:</label>
        <input type="text" id="debtitre" onkeyup="recherche_ouvrages_titre()">
        <br>
    </nav>

    <section>
        <p>Bienvenue sur le site de la bibliothèque virtuelle.</p>
        <div id="div-gauche"></div>
        <div id="div-droite"></div>
    </section>
</div>

<div id="form-div" style="display:none;">
    <section>
        <h2>Inscription</h2>
        <form onsubmit="event.preventDefault(); enregistrement();">
            <label>Nom:</label> <input type="text" id="nom" required><br>
            <label>Prénom:</label> <input type="text" id="prenom" required><br>
            <label>Adresse:</label> <input type="text" id="adresse" required><br>
            <label>Code Postal:</label> <input type="text" id="code_postal" required><br>
            <label>Ville:</label> <input type="text" id="ville" required><br>
            <label>Pays:</label> <input type="text" id="pays" required><br>
            <button type="submit">S'inscrire</button>
            <button type="button" onclick="montrer_recherche()">Annuler</button>
        </form>
        <div id="messageErreur"></div>
    </section>
</div>

</body>
</html>

