<?php
session_start();

// Connexion à la base de données
try {
    $pdo = new PDO("pgsql:host=localhost;dbname=livres", "postgres", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Supprimer un article du panier
if (isset($_POST['remove'])) {
    $code = $_POST['remove'];
    if (isset($_SESSION['panier'][$code])) {
        unset($_SESSION['panier'][$code]);
    }
}

// Afficher les articles du panier
$panier = $_SESSION['panier'] ?? [];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Votre Panier</h1>
    <?php if (empty($panier)): ?>
        <p>Votre panier est vide.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($panier as $code => $nom): ?>
                <li><?= htmlspecialchars($nom) ?> 
                    <form method="post" style="display:inline;">
                        <button type="submit" name="remove" value="<?= $code ?>">Supprimer</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <a href="index.php">Retour à la recherche</a>
</body>
</html>
