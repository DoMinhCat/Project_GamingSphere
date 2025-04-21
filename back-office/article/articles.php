<?php
session_start();
require('../include/check_timeout.php');
require('../include/database.php');  // Assurez-vous que la connexion à la base de données est incluse

if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
} else {
    header('Location: login.php');  // Si l'utilisateur n'est pas connecté, redirige vers la page de login
    exit();
}

// Gestion de l'ajout d'un article
if (isset($_POST['add_article'])) {
    $titre = $_POST['titre'];
    $contenu = $_POST['contenu'];
    $date_article = date('Y-m-d'); // Date de création de l'article

    $query = $bdd->prepare("INSERT INTO news (titre, date_article, contenue) VALUES (?, ?, ?)");
    $query->execute([$titre, $date_article, $contenu]);

    header('Location: articles.php'); // Redirection après ajout
    exit();
}

// Gestion de la suppression d'un article
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $query = $bdd->prepare("DELETE FROM news WHERE id_news = ?");
    $query->execute([$delete_id]);

    header('Location: articles.php'); // Redirection après suppression
    exit();
}

// Gestion de la modification d'un article
if (isset($_POST['update_article'])) {
    $update_id = $_POST['update_id'];
    $titre = $_POST['titre'];
    $contenu = $_POST['contenu'];

    $query = $bdd->prepare("UPDATE news SET titre = ?, contenue = ? WHERE id_news = ?");
    $query->execute([$titre, $contenu, $update_id]);

    header('Location: articles.php'); // Redirection après modification
    exit();
}

// Affichage des articles existants
$query = $bdd->query("SELECT * FROM news ORDER BY date_article DESC");
$articles = $query->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Gestion des Articles';
include('../include/head.php');
?>

<body>
<?php include("../include/header.php"); ?>

<div class="container my-5">
    <h1 class="mb-4">Gestion des Articles</h1>

    <!-- Formulaire d'ajout d'article -->
    <form method="POST" action="articles.php" class="mb-5">
        <h3>Ajouter un nouvel article</h3>
        <div class="mb-3">
            <label for="titre" class="form-label">Titre</label>
            <input type="text" class="form-control" id="titre" name="titre" required>
        </div>
        <div class="mb-3">
            <label for="contenu" class="form-label">Contenu</label>
            <textarea class="form-control" id="contenu" name="contenu" rows="4" required></textarea>
        </div>
        <button type="submit" name="add_article" class="btn btn-primary">Ajouter l'article</button>
    </form>

    <!-- Affichage des articles -->
    <h3>Liste des articles</h3>
    <table class="table">
        <thead>
        <tr>
            <th>Titre</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($articles as $article) : ?>
            <tr>
                <td><?= htmlspecialchars($article['titre']) ?></td>
                <td><?= htmlspecialchars($article['date_article']) ?></td>
                <td>
                    <a href="articles.php?delete_id=<?= $article['id_news'] ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">Supprimer</a>
                    <a href="article_edit.php?id=<?= $article['id_news'] ?>" class="btn btn-warning">Modifier</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include("../include/footer.php"); ?>
</body>
</html>

