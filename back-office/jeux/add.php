<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require_once('../../include/database.php');
require_once __DIR__ . '/../../path.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('location' . jeux_back . '?error=req');
    exit();
}
if (empty($_POST['gameName']) || empty($_POST['category']) || empty($_POST['releaseDate']) || empty($_POST['gameRating']) || empty($_POST['gamePrice']) || empty($_POST['gameType']) || empty($_POST['platform']) || empty($_POST['gamePublisher'])) {
    header('location:' . jeux_add_back . '?message=' . urlencode('Il faut remplir tous les champs nécessaires !'));
    exit();
}

$category = trim($_POST['category']);
$releaseDate = $_POST['releaseDate'];
$dateObj = DateTime::createFromFormat('Y-m-d', $_POST['releaseDate']);
$releaseDate = $dateObj ? $dateObj->format('Y-m-d') : null;
$gameName = trim($_POST['gameName']);
$gameRating = $_POST['gameRating'];
$platform = trim($_POST['platform']);
$gamePrice = trim($_POST['gamePrice']);
$gameType = trim($_POST['gameType']);
$gamePublisher = trim($_POST['gamePublisher']);
$gameDescription = trim($_POST['gameDescription']);




if (isset($_FILES['gameImage']) && $_FILES['gameImage']['error'] === UPLOAD_ERR_OK) {
    $type_accept = ['image/png', 'image/gif', 'image/jpeg'];
    if (!in_array($_FILES['gameImage']['type'], $type_accept)) {
        $_SESSION['message'] = "Le fichier doit être du type jpeg , png, ou gif !";
        header('location:' . jeux_add_back);
        exit();
    }
    $size_accept = 4 * 1024 * 1024; //4MB
    if ($_FILES['gameImage']['size'] > $size_accept) {
        $_SESSION['message'] = "La taille de l'image ne doit pas dépasser 4Mo !";
        header('location:' . jeux_add_back);
        exit();
    }
    $uploadDir = "../uploads/";

    $filename = preg_replace("/[^a-zA-Z0-9\._-]/", "_", $_FILES['gameImage']['name']);
    $imagePath = $uploadDir . $filename;

    if (move_uploaded_file($_FILES['gameImage']['tmp_name'], $imagePath)) {
        echo "Fichier déplacé avec succès : " . $imagePath;
        $imagePath = $filename;
    } else {
        echo "Erreur lors du déplacement du fichier.";
        $imagePath = null;
    }
}


try {
    $stmt = $bdd->prepare("INSERT INTO jeu (catégorie, date_sortie, nom, note_jeu, plateforme, prix, type, éditeur, image, description)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$category, $releaseDate, $gameName, $gameRating, $platform, $gamePrice, $gameType, $gamePublisher, $imagePath, $gameDescription]);
    echo "<div class='alert alert-success text-center'>Jeu ajouté avec succès !</div>";
    header("Location:" . jeux_back . "?message=success");
    exit();
} catch (PDOException $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location:' . jeux_back . '?error=bdd');
    exit();
}
