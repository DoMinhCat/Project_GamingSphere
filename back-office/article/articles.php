<?php
session_start();
require('../../include/database.php');
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/check_timeout.php');
?>

<!DOCTYPE html>
<html lang="fr">

<?php
$title = 'Gérer les articles';
require('../head.php');
?>

<body class="pb-4">
    <?php
    $page = 'index.php';
    include('../navbar.php');
    ?>
    <div class="container my-5">
        <h2>Gérer les articles</h2>
        <form action="gerer_articles.php" method="post">
            <div class="mb-3">
                <label for="titre" class="form-label">Titre de l'article</label>
                <input type="text" class="form-control" id="titre" name="title" required>
            </div>
            <div class="mb-3">
                <label for="contenu" class="form-label">Contenu</label>
                <textarea class="form-control" id="contenu" name="content" rows="5" required></textarea>
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Type</label>
                <select class="form-select" id="type" name="type" required>
                    <option value="tournament">Tournois</option>
                    <option value="game">Jeux</option>
                </select>
            </div>
            <input type="hidden" name="id" id="article-id">
            <button type="submit" name="action" value="add" class="btn btn-primary m-2">Ajouter l'article</button>
            <button type="submit" name="action" value="edit" class="btn btn-warning m-2">Modifier l'article</button>
            <button type="submit" name="action" value="delete" class="btn btn-danger m-2">Supprimer l'article</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="UTF-8">
        <title>Gérer les articles</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>

    <body>
        <div class="container my-5">
            <h2>Gérer les articles</h2>
            <form action="gerer_articles.php" method="post">
                <div class="mb-3">
                    <label for="titre" class="form-label">Titre de l'article</label>
                    <input type="text" class="form-control" id="titre" name="title" required>
                </div>
                <div class="mb-3">
                    <label for="contenu" class="form-label">Contenu</label>
                    <textarea class="form-control" id="contenu" name="content" rows="5" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="type" class="form-label">Type</label>
                    <select class="form-select" id="type" name="type" required>
                        <option value="tournament">Tournois</option>
                        <option value="game">Jeux</option>
                    </select>
                </div>
                <input type="hidden" name="id" id="article-id">
                <button type="submit" name="action" value="add" class="btn btn-primary m-2">Ajouter l'article</button>
                <button type="submit" name="action" value="edit" class="btn btn-warning m-2">Modifier l'article</button>
                <button type="submit" name="action" value="delete" class="btn btn-danger m-2">Supprimer l'article</button>
            </form>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>