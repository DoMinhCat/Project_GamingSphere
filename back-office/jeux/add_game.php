<?php
include('../../include/database.php');
include('../navbar.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gameName'])) {
    $category = $_POST['category'];
    $releaseDate = $_POST['releaseDate'];
    $gameName = $_POST['gameName'];
    $gameRating = $_POST['gameRating'];
    $platform = $_POST['platform'];
    $gamePrice = $_POST['gamePrice'];
    $gameType = $_POST['gameType'];
    $gamePublisher = $_POST['gamePublisher'];


    if (isset($_FILES['gameImage']) && $_FILES['gameImage']['error'] === UPLOAD_ERR_OK) {
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


    $stmt = $bdd->prepare("INSERT INTO jeu (catégorie, date_sortie, nom, note_jeu, plateforme, prix, type, éditeur, image)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$category, $releaseDate, $gameName, $gameRating, $platform, $gamePrice, $gameType, $gamePublisher, $imagePath]);
    echo "<div class='alert alert-success text-center'>Jeu ajouté avec succès !</div>";
    header("Location: jeux.php?message=success");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des jeux</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h1 class="text-center mb-3" style="font-size: 1.5rem;">Ajouter un jeu</h1>

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
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Ajouter le jeu</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
