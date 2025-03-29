<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliothèque Virtuelle</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<?php
$cookie_name = 'visited';
$filename = "counter.txt";

// Verifica se o arquivo existe e se não está vazio
if (!file_exists($filename)) {
    file_put_contents($filename, "0");
}
$fp = fopen($filename, "r+");

$counter = intval(fread($fp, filesize($filename) ?: 1)); // Evita erro se o arquivo estiver vazio

function incrementVisitors() {
    global $cookie_name, $fp, $counter, $filename;
    
    if (!isset($_COOKIE[$cookie_name])) {
        setcookie($cookie_name, "true", time() + 10, "/"); // Cookie dura 10 segundos
        $counter++;
        ftruncate($fp, 0); // Limpa o arquivo antes de escrever
        rewind($fp);
        fwrite($fp, $counter);
    }
}

incrementVisitors();
fclose($fp);
?>

<header>
    <section id="number_of_visitors">
        <h5>Nombre de visiteurs: <?php echo $counter; ?></h5>
    </section>

    <section id="title">
        <h1>Titre du site</h1>
    </section>  

    <section id="menu">
        <h5>Bienvenue Nom Prénom</h5>
        <h5>Quitter</h5>
    </section>
</header>

<nav>
    <label for="by_author">Par Auteur:</label>
    <input type="text" id="by_author" name="by_author">
    <br>
    <label for="by_title">Par Titre:</label>
    <input type="text" id="by_title" name="by_title">
    <br>
</nav>

<section>
    <p>Bienvenue sur le site de la bibliothèque virtuelle.</p>
    <div id="authors"></div>
    <div id="publications"></div>
</section>

<script>
    $(document).ready(function () {
        $("input").keyup(function () {
            // TODO: recherche à la base de données
            $(this).css("background-color", "pink");
        });
    });
</script>

</body>
</html>



// TODO: AJAX
// TODO: botar a estrutura qua ela queria de header divs e 
// TODO: usar query() pour exécuter la requête SQL retournant les auteurs à partir de la BDD
// TODO: chamar recherche_auteurs.php no key up so search auteurs e assim por diante
// TODO: callback affiche_auteurs recebe o json do recherche auteurs.php