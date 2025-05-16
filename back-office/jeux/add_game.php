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

        <form action="add.php" method="post" class="needs-validation" enctype="multipart/form-data">
            <div class="mb-2">
                <label for="gameName" class="form-label">Nom du jeu :</label>
                <input type="text" id="gameName" name="gameName" class="form-control" required>
            </div>
            <div class="mb-2">
                <label for="category" class="form-label">Catégorie :</label>
                <input type="text" id="category" name="category" class="form-control" required>
            </div>
            <div class="mb-2">
                <label for="releaseDate" class="form-label">Date de sortie :</label>
                <input type="date" id="releaseDate" name="releaseDate" class="form-control" required>
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
                <input type="file" id="gameImage" name="gameImage" class="form-control" accept="image/*">
            </div>
            <div class="mb-5">
                <label for="gameDescription" class="form-label">Description:</label>
                <input type="text" id="gameDescription" name="gameDescription" class="form-control">
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Ajouter le jeu</button>
            </div>
        </form>
    </main>
</body>

</html>