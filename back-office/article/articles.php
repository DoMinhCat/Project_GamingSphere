<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/check_timeout.php');
require('../../include/database.php');
require_once __DIR__ . '/../../path.php';


if (isset($_POST['add_article'])) {
    $titre = $_POST['titre'];
    $contenu = $_POST['contenu'];
    $date_article = date('Y-m-d');

    $query = $bdd->prepare("INSERT INTO news (titre, date_article, contenue) VALUES (?, ?, ?)");
    $query->execute([$titre, $date_article, $contenu]);

    header('Location:' . article_back);
    exit();
}

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $query = $bdd->prepare("DELETE FROM news WHERE id_news = ?");
    $query->execute([$delete_id]);

    header('Location:' . article_back);
    exit();
}

if (isset($_POST['update_article'])) {
    $update_id = $_POST['update_id'];
    $titre = $_POST['titre'];
    $contenu = $_POST['contenu'];

    $query = $bdd->prepare("UPDATE news SET titre = ?, contenue = ? WHERE id_news = ?");
    $query->execute([$titre, $contenu, $update_id]);

    header('Location:' . article_back);
    exit();
}

$query = $bdd->query("SELECT * FROM news ORDER BY date_article DESC");
$articles = $query->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Gestion des Articles';
require('../../include/head.php');
?>

<body>
    <?php
    $page = index_back;
    include("../navbar.php"); ?>

    <div class="container my-5">
        <h1 class="mb-4">Gestion des Articles</h1>

        <!-- Formulaire d'ajout d'article -->
        <form method="POST" action="<?= article_back ?>" class="mb-5">
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
        <h3 class="text-center">Liste des articles</h3>
        <?php
        echo '<div class="form-group my-2 sticky-top pt-3 pb-2">
            <input type="text" id="search_article" class="form-control" placeholder="Rechercher par nom d\'article">
        </div>';

        if (count($articles) > 0) {
            echo '<div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">';
            echo "<table class='table table-striped'>";
            echo "<thead class='thead-dark'><tr>
                    <th>Titre</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr></thead>";
            echo '<tbody id="article_results">';



            foreach ($articles as $article) {
                echo '<tr>
                        <td>' . htmlspecialchars($article['titre']) . '</td>
                        <td>' . htmlspecialchars($article['date_article']) . '</td>
                        <td>
                            <a href=' . article_edit_back . '?id=' . $article['id_news'] . ' class="btn btn-warning my-1 me-1">Modifier</a>
                            <a href=' . article_back . '?delete_id=' . $article['id_news'] . ' class="btn btn-danger my-1 me-1" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer cet article ?\')">Supprimer</a>
                        </td>
                    </tr>';
            }
            echo '</tbody>
        </table>
    </div>';
        } else {
            echo "<div class='alert alert-warning'>Aucun article trouvé.</div>";
        }
        ?>
    </div>

    <script>
        document.getElementById('search_article').addEventListener('input', function() {
            const query = this.value;

            fetch('search_article.php?search=' + encodeURIComponent(query), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(data => {
                    document.getElementById('article_results').innerHTML = data;
                });
        });
    </script>
</body>

</html>