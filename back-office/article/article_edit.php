<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/check_timeout.php');
require_once('../../include/database.php');
require_once __DIR__ . '/../../path.php';
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Modification des Articles';
require('../head.php'); ?>

<body>
    <?php
    $page = article_back;
    include("../navbar.php"); ?>

    <main class="container my-5">
        <h1 class="my-5 text-center">Modifier un article</h1>
        <?php
        if (empty($_GET['id'])) {
            header('Location:' . article_back . '?error=missing_id');
            exit();
        }
        $id_article = $_GET['id'];
        try {
            $stmt = $bdd->prepare("SELECT * from news WHERE id_news=? LIMIT 1;");
            $stmt->execute([$id_article]);
            $article = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            header('Location:' . article_back . '?error=bdd');
            exit();
        }
        ?>
        <form method="POST" action="update_article.php" class="p-4 border rounded shadow-sm bg-light">
            <input type="hidden" name="id_article" value="<?= $id_article ?>">
            <div class="mb-3">
                <label for="titre" class="form-label">Titre de l'article</label>
                <input type="text" class="form-control" id="titre" name="titre" value="<?= $article['titre'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="contenu" class="form-label">Contenu</label>
                <textarea class="form-control" id="contenu" name="contenu" rows="4" required><?= $article['contenue'] ?></textarea>
            </div>

            <div class="mb-3">
                <div id="choose">
                    <label for="category" class="form-label">Catégorie</label>
                    <select class="form-select" id="category" name="category_choose" required>
                        <option value="Général">Général</option>
                        <option value="Esport">Esport</option>
                        <option value="Evenèment">Evenèment</option>
                        <option value="Critique">Critique</option>
                        <option value="Mise à jour">Mise à jour</option>
                    </select>
                    <button id="new" onclick="showNewCategoryForm(event)" class="mt-2 btn btn-primary">Proposer une nouvelle catégorie</button>
                </div>
                <div id="new_category" style="display: none;">
                    <label for="new_category_input" class="form-label">Categorie</label>
                    <input type="text" class="form-control" id="new_category_input" name="category_new" required>
                    <button onclick="chooseCategory()" class="mt-2 btn btn-primary">Choisir une catégorie predéfinie</button>
                </div>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a href="<?= article_back ?>" class="btn btn-secondary">Retour</a>
            </div>
        </form>
    </main>

    <script>


    </script>
</body>

</html>