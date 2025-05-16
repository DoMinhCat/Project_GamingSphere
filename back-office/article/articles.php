<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/check_timeout.php');
require('../../include/database.php');
require_once __DIR__ . '/../../path.php';

if (isset($_POST['add_article'])) {
    if (empty($_POST['titre']) || empty($_POST['contenu'])) {
        $_SESSION['error'] = "Un titre et du contenu est obligatoire !";
        header('Location:' . article_back . '?error=bdd');
        exit();
    }

    $titre = trim($_POST['titre']);
    $contenu = trim($_POST['contenu']);
    $date_article = date('Y-m-d');
    $id_auteur = $_SESSION['user_id'];
    $category = null;

    if (!empty($_POST['category_choose'])) {

        $category = trim($_POST['category_choose']);
    } elseif (!empty($_POST['category_new'])) {

        $category = $_POST['category_new'];
    } else {
        $_SESSION['error'] = "Aucune catégorie n'a été sélectionnée ou saisie !";
        header('Location:' . article_back . '?error=bdd');
        exit();
    }
    try {
        $query = $bdd->prepare("INSERT INTO news (titre, date_article, contenue, auteur, category) VALUES (?, ?, ?, ?, ?)");
        $query->execute([$titre, $date_article, $contenu, $id_auteur, $category]);

        header('Location:' . article_back . '?message=add_success');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = htmlspecialchars($e->getMessage());
        header('Location:' . article_back . '?error=bdd');
        exit();
    }
}

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        $query = $bdd->prepare("DELETE FROM news WHERE id_news = ?");
        $query->execute([$delete_id]);

        header('Location:' . article_back . '?message=delete_success');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = htmlspecialchars($e->getMessage());
        header('Location:' . article_back . '?error=bdd');
        exit();
    }
}

if (isset($_POST['update_article'])) {
    $update_id = $_POST['update_id'];
    $titre = $_POST['titre'];
    $contenu = $_POST['contenu'];
    try {
        $query = $bdd->prepare("UPDATE news SET titre = ?, contenue = ? WHERE id_news = ?");
        $query->execute([$titre, $contenu, $update_id]);

        header('Location:' . article_back . '?message=update_success');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = htmlspecialchars($e->getMessage());
        header('Location:' . article_back . '?error=bdd');
        exit();
    }
}
try {
    $query = $bdd->prepare("SELECT n.id_news, n.titre, n.date_article, n.category, u.email
FROM news n JOIN utilisateurs u ON n.auteur = u.id_utilisateurs ORDER BY n.date_article DESC;");
    $query->execute();
    $articles = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = htmlspecialchars($e->getMessage());
    header('Location:' . article_back . '?error=bdd');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Gestion des Articles';
require('../head.php');
?>

<body>

    <?php
    $page = index_back;
    include("../navbar.php");
    ?>
    <main class="container mb-5">
        <?php
        $noti = '';
        $noti_Err = '';
        if (isset($_GET['message']) && $_GET['message'] === 'delete_success')
            $noti = 'L\'article a été supprimé avec succès !';
        elseif (isset($_GET['message']) && $_GET['message'] === 'add_success')
            $noti = 'L\'article a été ajouté avec succès !';
        elseif (isset($_GET['message']) && $_GET['message'] === 'update_success')
            $noti = 'L\'article a été modifié avec succès !';
        elseif (isset($_GET['error']) && $_GET['error'] === 'bdd') {
            $noti_Err = 'Erreur lors de la connection à la base de données : ' . $_SESSION['error'];
            unset($_SESSION['error']);
        } elseif (isset($_GET['error']) && $_GET['error'] === 'missing_id')
            $noti_Err = 'Aucun ID spécifié';
        ?>
        <?php if (!empty($noti_Err)) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $noti_Err ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif ?>

        <?php if (!empty($noti)) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $noti ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif ?>
        <h1 class="my-5 text-center">Gestion des Articles</h1>

        <!-- Formulaire d'ajout d'article -->
        <form method="POST" action="" class="mb-5">
            <h3>Ajouter un nouvel article</h3>
            <div class="my-3">
                <label for="titre" class="form-label">Titre</label>
                <input type="text" class="form-control" id="titre" name="titre" required>
            </div>
            <div class="mb-3">
                <div id="choose">
                    <label for="category" class="form-label">Catégorie</label>
                    <select class="form-select" id="category" name="category_choose" required>
                        <option value="Général">Général</option>
                        <option value="À la une">À la une</option>
                        <option value="Esport">Esport</option>
                        <option value="Évenèment">Évenèment</option>
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
            <div class="mb-3">
                <label for="contenu" class="form-label">Contenu</label>
                <textarea class="form-control" id="contenu" name="contenu" rows="4" required></textarea>
            </div>
            <button type="submit" name="add_article" class="btn btn-primary">Ajouter l'article</button>
        </form>

        <!-- Affichage des articles -->
        <h3 class="text-center mb-4">Liste des articles</h3>
        <?php
        echo '<div class="form-group my-2 sticky-top pt-3 pb-2">
            <input type="text" id="search_article" class="form-control searchBoxBack" placeholder="Rechercher par nom d\'article">
        </div>';

        if (count($articles) > 0) {
            echo '<div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">';
            echo "<table class='table table-striped table-bordered'>";
            echo "<thead class='table-dark' style=\"position: sticky; top: 0; z-index: 1;\"><tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Catégorie</th>
                    <th>Date</th>
                    <th>Auteur</th>
                    <th>Actions</th>
                </tr></thead>";
            echo '<tbody id="article_results">';



            foreach ($articles as $article) {
                echo '<tr>
                        <td class="align-middle">' . htmlspecialchars($article['id_news']) . '</td>
                        <td class="align-middle">' . htmlspecialchars($article['titre']) . '</td>
                        <td class="align-middle">' . htmlspecialchars($article['category']) . '</td>
                        <td class="align-middle">' . htmlspecialchars($article['date_article']) . '</td>
                        <td class="align-middle">' . htmlspecialchars($article['email']) . '</td>
                        <td>
                            <a href=' . article_edit_back . '?id=' . $article['id_news'] . ' class="btn btn-sm btn-warning my-1 me-1">Modifier</a>
                            <button type="button" class="btn btn-sm btn-danger my-1 me-1" data-bs-toggle="modal" data-bs-target="#modal' . $article['id_news'] . '">Supprimer</button>';
                echo '<div class="modal fade" id="modal' . $article['id_news'] . '" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h1 class="modal-title fs-5">Confirmation</h1>
                                </div>
                                <div class="modal-body">
                                  Êtes-vous sûr de vouloir supprimer cet article ?
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                  <a href="' . article_back . '?delete_id=' . $article['id_news'] . '" class="btn btn-danger">Supprimer</a>
                                </div>
                              </div>
                            </div>
                          </div>';
                echo '</td>
                    </tr>';
            }
            echo '</tbody>
        </table>
    </div>';
        } else {
            echo "<div class='alert alert-warning'>Aucun article trouvé.</div>";
        }
        ?>
    </main>


    <script>
        async function fetchArticle(query = '') {
            try {
                const response = await fetch('search_article.php?search=' + encodeURIComponent(query), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.text();
                document.getElementById('article_results').innerHTML = data;
            } catch (error) {
                console.error('Fetch erreur:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search_article');
            const categorySelect = document.getElementById('category');

            fetchArticle('');

            categorySelect.value = "Général";

            searchInput.addEventListener('input', function() {
                fetchArticle(this.value);
            });

            chooseCategory();
        });

        function showNewCategoryForm(event) {
            event.preventDefault();

            const newForm = document.getElementById('new_category');
            const selectForm = document.getElementById('choose');
            const newCategoryInput = document.getElementById('new_category_input');
            const categorySelect = document.getElementById('category');

            newForm.style.display = 'block';
            selectForm.style.display = 'none';

            newCategoryInput.required = true;
            categorySelect.required = false;
            categorySelect.value = "";
        }

        function chooseCategory() {
            document.getElementById('new_category').style.display = 'none';
            document.getElementById('choose').style.display = 'block';

            const newCategoryInput = document.getElementById('new_category_input');
            const categorySelect = document.getElementById('category');

            newCategoryInput.required = false;
            categorySelect.required = true;
            categorySelect.value = "Général";
        }
    </script>


</body>

</html>