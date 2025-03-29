<?php
session_start();

// Connexion à la base de données
$dbhost = 'localhost';
$dbname='livres';
$dbuser = 'postgres';
$dbpass = '';

// Connexion à la base de données
try {
    $pdo = new PDO("pgsql:host=localhost;dbname=livres", "postgres", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Gestion du compteur de visites
$filename = "compteur.txt";
if (!file_exists($filename)) file_put_contents($filename, "0");
$visites = (int) file_get_contents($filename);
if (!isset($_COOKIE['visited'])) {
    $visites++;
    file_put_contents($filename, $visites);
    setcookie("visited", "1", time() + 3600);
}

// Traitement des requêtes AJAX
if (isset($_GET['action'])) {
    header("Content-Type: application/json; charset=UTF-8");
    switch ($_GET['action']) {
        case 'search_authors':
            $debnom = $_GET['debnom'] ?? '';
            $stmt = $pdo->prepare("SELECT code, nom, prenom FROM auteurs WHERE nom ILIKE ?");
            $stmt->execute(["%$debnom%"]);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            exit;
        case 'search_books':
            $debtitre = $_GET['debtitre'] ?? '';
            $stmt = $pdo->prepare("SELECT code, nom, parution, sujet FROM ouvrage WHERE nom ILIKE ?");
            $stmt->execute(["%$debtitre%"]);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            exit;
        case 'add_to_cart':
            $code_client = $_COOKIE['client'] ?? 0;
            $code_exemplaire = $_POST['code_exemplaire'] ?? 0;
            $stmt = $pdo->prepare("INSERT INTO exemplaire (code, code_ouvrage) VALUES (?, ?)");
            $stmt->execute([$code_client, $code_exemplaire]);
            echo json_encode(["message" => "Article ajouté"]);
            exit;
        case 'view_cart':
            $code_client = $_COOKIE['client'] ?? 0;
            $stmt = $pdo->prepare("SELECT o.nom, e.code FROM exemplaire e JOIN ouvrage o ON e.code_ouvrage = o.code WHERE e.code = ?");
            $stmt->execute([$code_client]);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vente de Livres Kawaii</title>
    <style>
        body { font-family: 'Comic Sans MS', cursive, sans-serif; background-color: #ffecf4; text-align: center; color: #ff69b4; }
        header { background-color: #ffb6c1; padding: 20px; border-radius: 20px; }
        input { padding: 10px; border-radius: 10px; border: 2px solid #ff69b4; }
        #resultats, #cart { margin-top: 20px; font-size: 18px; }
        nav { margin: 20px; }
        button { padding: 10px; background-color: #ff69b4; color: white; border: none; border-radius: 10px; }
    </style>
</head>
<body>
    <header>
        <h1>📚 Bienvenue dans notre Librairie Kawaii ✨</h1>
        <p>Nombre de visites: <?php echo $visites; ?></p>
    </header>
    <nav>
        <button onclick="showTab('search_authors')">🔍 Rechercher Auteurs</button>
        <button onclick="showTab('search_books')">📖 Rechercher Livres</button>
        <button onclick="showTab('cart')">🛒 Mon Panier</button>
    </nav>
    <main>
        <div id="search_authors" class="tab">
            <input type="text" id="author_search" placeholder="Rechercher un auteur...">
            <div id="resultats"></div>
        </div>
        <div id="search_books" class="tab" style="display:none;">
            <input type="text" id="book_search" placeholder="Rechercher un livre...">
            <div id="books_result"></div>
        </div>
        <div id="cart" class="tab" style="display:none;">
            <h2>🛒 Votre Panier</h2>
            <div id="cart_content"></div>
        </div>
    </main>
    <script>
        function showTab(tab) {
            document.querySelectorAll('.tab').forEach(el => el.style.display = 'none');
            document.getElementById(tab).style.display = 'block';
        }
        document.getElementById("author_search").addEventListener("keyup", function() {
            let debnom = this.value;
            
            fetch(`?action=search_authors&debnom=${debnom}`)
                .then(response => response.json())
                .then(data => {
                    let liste =  "<ol>" + data.map(a => `<li>${a.nom} ${a.prenom}</li>`).join("") + "</ol>";
               
                    document.getElementById("resultats").innerHTML = liste;
                });
        });
        document.getElementById("book_search").addEventListener("keyup", function() {
            let debtitre = this.value;
            fetch(`?action=search_books&debtitre=${debtitre}`)
                .then(response => response.json())
                .then(data => {
                    let liste = "" //"<ol>" + data.map(b => `<li>${b.nom}</li>`).join("") + "</ol>";
                    foreach (data as $book) {
                        print("alo")
                        print($book)
                        echo "<div>";
                        echo "<strong>{$book['nom']}</strong> ({$book['parution']}) - {$book['sujet']}";
                        echo "<button onclick='addToCart({$book['code']})'>🛒 Adicionar ao Carrinho</button>";
                        echo "</div>"; 
                    }   

                    document.getElementById("books_result").innerHTML = liste;
                });
        });
        function viewCart() {
            fetch(`?action=view_cart`)
                .then(response => response.json())
                .then(data => {
                    let liste = "<ul>" + data.map(item => `<li>${item.nom} (x${item.quantite})</li>`).join("") + "</ul>";
                    document.getElementById("cart_content").innerHTML = liste;
                });
        }
        showTab('search_authors');
    </script>
</body>
</html>
