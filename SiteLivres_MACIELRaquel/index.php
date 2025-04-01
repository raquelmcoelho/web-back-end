<?php
    require 'php/counter.php';
    session_start();
    if (! isset($_SESSION['code_client']) && isset($_COOKIE['code_client'])) {
        include "php/connexion.php";
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
    <section id="logo-section">
        <img id="logo" src="logo.png" alt="Logo de la Bibliothèque Virtuelle">
        <section id="title">
            <h1> Bibliothèque Virtuelle</h1>
            <h5> (<?php echo $counter; ?> Visiteurs) </h5>
        </section>
    </section>

    <section id="menu">
        <?php if (isset($_SESSION['nom'])): ?>
            <h2>Bienvenue <?php echo $_SESSION['nom'] ?>, <?php echo $_SESSION['prenom'] ?>. </h2>
        <?php endif; ?>

        <?php if (isset($_SESSION['nom'])): ?>
            <button type="button" onclick="montrer_panier()">Voir le Panier</button>
            <button type="button" onclick="vider_panier()">Vider le Panier</button>
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
        <div class="form-group">
            <label for="debnom">Par Auteur:</label>
            <input type="text" id="debnom" onkeyup="recherche_auteurs()">
        </div>
        <div class="form-group">
            <label for="debtitre">Par Titre:</label>
            <input type="text" id="debtitre" onkeyup="recherche_ouvrages_titre()">
        </div>
    </nav>

    <section>
        <div id="div-gauche"></div>
        <div id="div-droite"></div>
    </section>
</div>

<div id="form-div" style="display:none;">
    <section id="formulaire">
        <h2>Inscription</h2>
        <form onsubmit="event.preventDefault(); enregistrement();">
            <div class="form-group"><label>Nom:</label> <input type="text" id="nom" required><br></div>
            <div class="form-group"><label>Prénom:</label> <input type="text" id="prenom" required><br></div>
            <div class="form-group"><label>Adresse:</label> <input type="text" id="adresse" required><br></div>
            <div class="form-group"><label>Code Postal:</label> <input type="text" id="code_postal" required><br></div>
            <div class="form-group"><label>Ville:</label> <input type="text" id="ville" required><br></div>
            <div class="form-group"><label>Pays:</label> <input type="text" id="pays" required><br></div>
            <section id="buttons-formulaire">
                <button class="cancel" type="button" onclick="montrer_recherche()">Annuler</button>
                <button class="confirm" type="submit">S'inscrire</button>
            </section>
        </form>
        <div id="message_erreur"></div>
    </section>
</div>

</body>
</html>

