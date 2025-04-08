<?php
session_start();
if (!isset($_SESSION['admin']) || empty($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    echo "<div class='alert alert-danger'>Accès refusé. Vous n'êtes pas administrateur !</div>";
    exit();
}
require('../include/check_timeout.php');
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Back Office</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../include/check_timeout.js"></script>
    <?php
    if (isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
        echo '<script src="../include/check_timeout.js"></script>';
    }
    ?>
</head>

<body>
    <nav class="navbar navbar-dark bg-dark px-3">
        <div class="d-flex align-items-center">
            <a href="#">
                <img src="../include/LOGO ENTIER 40px.png" alt="Logo" width="120px" height="50px" class="rounded-circle">
            </a>
        </div>
        <div class="mx-auto">
            <span class="navbar-brand">Back Office</span>
        </div>
        <div class="ms-auto">
            <a href="../index.php" class="btn btn-primary">Front Office</a>
        </div>
    </nav>

    <div class="container my-5">
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <div class="col">
            <a href="evenements.php" class="tableau-card d-block text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                Gestion des évènements
            </a>
        </div>
        <div class="col">
            <a href="article/articles.php" class="tableau-card d-block text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                Gestion des articles
            </a>
        </div>
        <div class="col">
            <a href="tournois/tournois_main.php" class="tableau-card d-block text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                Gestion des Tournois
            </a>
        </div>
        <div class="col">
            <a href="profils/profils.php" class="tableau-card d-block text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                Gestion des profils
            </a>
        </div>
        <div class="col">
            <a href="forum/forum.php" class="tableau-card d-block text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                Gestion du forum
            </a>
        </div>
        <div class="col">
            <a href="paris.php" class="tableau-card d-block text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                Gestion des paris
            </a>
        </div>
        <div class="col">
            <a href="communication.php" class="tableau-card d-block text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                Gestion des canaux de communication
            </a>
        </div>
        <div class="col">
            <a href="jeux/jeux.php" class="tableau-card d-block text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                Gestion des jeux
            </a>
        </div>
    </div>
</div>
</body>

</html>