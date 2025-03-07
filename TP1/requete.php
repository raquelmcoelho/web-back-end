<!-- 
 Implémenter en SQL les recherches suivantes et les tester sous la BDD livres (postgresql)
a) recherche d’auteurs connaissant une partie du nom,
b) recherche d’ouvrages connaissant une partie du titre,
c) recherche d’ouvrages connaissant le code d’un auteur,
d) recherche d’exemplaires connaissant le code d’un ouvrage (Nom de l’éditeur et Prix).
3. Une fois familiarisé avec la BDD livres, intégrer la 1ère requête SQL dans un programme
requete.php à tester indépendamment du site web. Utiliser PDO (PHP Data Objects) pour
accéder à la BDD depuis PHP, exécuter la requête (query(), fetch(), ...) et afficher le résultat
sous forme d’un tableau HTML. Un fois familiarisé avec PDO, passer au TP2. 
-->

<?php 


function executeCommand($sql) {
    try {
        $dbhost = 'localhost';
        $dbname='livres';
        $dbuser = 'postgres';
        $dbpass = '';
    
        $connection = new PDO("pgsql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    
        foreach ($connection->query($sql) as $row) {
            // TODO: make variable function
            var_dump($row);
            echo '<br>';
        }
    
        $connection = null;
    } catch (PDOException $e) {
        // TODO: instead of dying redirect all exceptions
        die("Error message: " . $e->getMessage());
    }
    
}

// TODO: put as callback of the search buttons
//searchByName($_GET['by_author']);
//searchByTitle($_GET['by_title']);

function searchByName($name) {
    echo '<br><br><br><br>SEARCH BY Name ' . $name . ' <br><br><br><br>';
    //recherche d’auteurs connaissant une partie du nom,
    $sql = "SELECT * FROM auteurs WHERE nom LIKE '%$name%';";
    executeCommand($sql);
    
}

function searchByTitle($title) {
    echo '<br><br><br><br>SEARCH BY Title ' . $title . ' <br><br><br><br>';
    //recherche d’ouvrages connaissant une partie du titre,
    $sql = "SELECT * FROM ouvrage WHERE nom LIKE '%$title%';";
    executeCommand($sql);
}

function searchByAuthorCode($code) {
    echo '<br><br><br><br>SEARCH BY AuthorCode ' . $code . ' <br><br><br><br>';
    //recherche d’ouvrages connaissant le code d’un auteur,
    $sql = "SELECT * FROM ouvrage WHERE code = $code    ;";
    executeCommand($sql);
}

function searchByBookCode($code) {
    echo '<br><br><br><br>SEARCH BY BookCode ' . $code . '<br><br><br><br>';
    //recherche d’exemplaires connaissant le code d’un ouvrage (Nom de l’éditeur et Prix).
    $sql = "SELECT * FROM exemplaire WHERE code_ouvrage = '%$code%'";;
    executeCommand($sql);
}


function test() {
searchByName("Galouye");
searchByTitle("Les deux tours");
searchByAuthorCode(13);
searchByBookCode(2);
}

test();
?>