<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/database.php');
require('../../include/check_timeout.php');
require_once __DIR__ . '/../../path.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gameName'])) {
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
        $_SESSION['error'] = htmlspecialchars($e->getMessage());
        header('Location:' . jeux_back . '?error=bdd');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Gestions des jeux';
require('../head.php');
?>

<body>
    <?php
    $page = jeux_back;
    include('../navbar.php');
    ?>
    <main class="container mb-5">
        <?php if (!empty($_GET['message'])) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif ?>
        <?php if (!empty($_SESSION['message'])) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php unset($_SESSION['message']);
        endif ?>
        <h1 class="text-center mt-5 mb-3" style="font-size: 1.5rem;">Ajouter un jeu</h1>

        <form action="" method="post" class="needs-validation" enctype="multipart/form-data" novalidate>
            <div class="mb-2">
                <label for="category" class="form-label">Catégorie :</label>
                <input type="text" id="category" name="category" class="form-control" required>
            </div>
            <div class="mb-2">
                <label for="releaseDate" class="form-label">Date de sortie :</label>
                <input type="date" id="releaseDate" name="releaseDate" class="form-control" required>
            </div>
            <div class="mb-2">
                <label for="gameName" class="form-label">Nom du jeu :</label>
                <input type="text" id="gameName" name="gameName" class="form-control" required>
            </div>
            <div class="mb-2">
                <label for="gameRating" class="form-label">Note du jeu :</label>
                <input type="number" id="gameRating" name="gameRating" class="form-control" step="0.1" min="0" max="10" required>
            </div>
            <div class="mb-2">
                <label for="platform" class="form-label">Plateforme :</label>
                <input type="text" id="platform" name="platform" class="form-control" required>
            </div>
            <div class="mb-2">
                <label for="gamePrice" class="form-label">Prix (€) :</label>
                <input type="number" id="gamePrice" name="gamePrice" class="form-control" step="0.01" required>
            </div>
            <div class="mb-2">
                <label for="gameType" class="form-label">Type :</label>
                <input type="text" id="gameType" name="gameType" class="form-control" required>
            </div>
            <div class="mb-2">
                <label for="gamePublisher" class="form-label">Éditeur :</label>
                <input type="text" id="gamePublisher" name="gamePublisher" class="form-control" required>
            </div>
            <div class="mb-2">
                <label for="gameImage" class="form-label">Image du jeu :</label>
                <input type="file" id="gameImage" name="gameImage" class="form-control" accept="image/*" required>
            </div>
            <div class="mb-5">
                <label for="gameDescription" class="form-label">Description:</label>
                <input type="text" id="gameDescription" name="gameDescription" class="form-control" required>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Ajouter le jeu</button>
            </div>
        </form>
    </main>
</body>

</html>