<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Back Office</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
    <div class="row g-4">
        <div class="col-md-4">
            <a href="evenements.php" class="tableau-card mb-4 d-block text-white text-decoration-none">Gestion des évènements</a>
        </div>
        <div class="col-md-4">
            <a href="article/articles.php" class="tableau-card mb-4 d-block text-white text-decoration-none">Gestion des articles</a>
        </div>
        <div class="col-md-4">
            <a href="profils/profils.php" class="tableau-card mb-4 d-block text-white text-decoration-none">Gestion des profils</a>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-md-4">
            <a href="forum/forum.php" class="tableau-card mb-4 d-block text-white text-decoration-none">Gestion du forum</a>
        </div>
        <div class="col-md-4">
            <a href="paris.php" class="tableau-card mb-4 d-block text-white text-decoration-none">Gestion des paris</a>
        </div>
        <div class="col-md-4">
            <a href="communication.php" class="tableau-card mb-4 d-block text-white text-decoration-none">Gestion des canaux de communication</a>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-md-4">
            <a href="jeux/jeux.php" class="tableau-card mb-4 d-block text-white text-decoration-none">Gestion des jeux</a>
        </div>
    </div>
</div>
</body>
</html>