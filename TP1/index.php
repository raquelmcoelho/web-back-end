<!DOCTYPE html>
<html>
<head>
</head>
<body>

<?php
$cookie_name = 'visited';
$filename = "counter.txt";
$fp = fopen($filename, "r+") or die("Unable to open file!");
$counter = fread($fp, filesize($filename));

function incrementVisitors() {
    $cookies_path = "/";
    $cookie_value = TRUE;
    $seconds_per_hour = 60 * 60;
    setcookie($GLOBALS['cookie_name'], $cookie_value, time() + 10, $cookies_path); 
    $GLOBALS['counter'] = $GLOBALS['counter'] + 1;
    fseek($GLOBALS['fp'], 0);
    fwrite($GLOBALS['fp'], $GLOBALS['counter'], filesize($GLOBALS['filename'])) or die("Unable to write file!");
}

if(!isset($_COOKIE[$cookie_name])) {
    incrementVisitors();
}

fclose($fp);
?>


<header>
    <section id="number_of_visitors">
        <h5>Nombre de visiteurs <php? echo $counter?></h5>
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
    <input type="text" name="by_author">
    <br>
    <label for="by_title">Par Titre:</label>
    <input type="text" name="by_title">
    <br>
</nav>

<section>
<p>Bienvenu sur le site de la bibliothèque virtuelle.</p>
<div id="authors">

</div>
<div id="publications">

</div>
</section>

<script>
    $("input").keyup(function(){
        // TODO: recherche at database
        $("input").css("background-color", "pink");
    });
</script>


</body>
</html>
