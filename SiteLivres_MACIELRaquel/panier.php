<?php
session_start();

if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'add' && isset($_POST['code_ouvrage']) && isset($_POST['nom_ouvrage'])) {
    $code_ouvrage = $_POST['code_ouvrage'];
    $nom_ouvrage = base64_decode($_POST['nom_ouvrage']);

    if (!isset($_SESSION['panier'][$code_ouvrage])) {
        $_SESSION['panier'][$code_ouvrage] = $nom_ouvrage;
    }

    echo json_encode(["message" => "Livre ajouté au panier"]);
    exit;
}

if ($action === 'remove' && isset($_POST['code_ouvrage'])) {
    $code_ouvrage = $_POST['code_ouvrage'];

    if (isset($_SESSION['panier'][$code_ouvrage])) {
        unset($_SESSION['panier'][$code_ouvrage]);
    }

    echo json_encode(["message" => "Livre retiré du panier"]);
    exit;
}

$panier = $_SESSION['panier'];
?>

