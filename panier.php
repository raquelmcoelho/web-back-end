<?php
session_start();

// Inicializa o carrinho se ainda não existir
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Obtém a ação da requisição
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Adiciona um livro ao carrinho
if ($action === 'add' && isset($_POST['code_ouvrage']) && isset($_POST['nom_ouvrage'])) {
    $code_ouvrage = $_POST['code_ouvrage'];
    $nom_ouvrage = base64_decode($_POST['nom_ouvrage']);


    // Verifica se o livro já está no carrinho
    if (!isset($_SESSION['panier'][$code_ouvrage])) {
        $_SESSION['panier'][$code_ouvrage] = $nom_ouvrage;
    }

    echo json_encode(["message" => "Livre ajouté au panier"]);
    exit;
}

// Remove um livro do carrinho
if ($action === 'remove' && isset($_POST['code_ouvrage'])) {
    $code_ouvrage = $_POST['code_ouvrage'];

    if (isset($_SESSION['panier'][$code_ouvrage])) {
        unset($_SESSION['panier'][$code_ouvrage]);
    }

    echo json_encode(["message" => "Livre retiré du panier"]);
    exit;
}

// Exibir o carrinho
$panier = $_SESSION['panier'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier</title>
    <script src="script.js"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Votre Panier</h1>

<?php if (empty($panier)): ?>
    <p>Votre panier est vide.</p>
<?php else: ?>
    <ul>
        <?php foreach ($panier as $code => $nom): ?>
            <li><?= htmlspecialchars($nom) ?> 
                <button onclick="removeLivre('<?= $code ?>')">Supprimer</button>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<a href="index.php">Retour à la recherche</a>

</body>
</html>

