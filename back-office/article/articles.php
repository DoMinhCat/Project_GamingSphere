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
    try {
        $query = $bdd->prepare("INSERT INTO news (titre, date_article, contenue) VALUES (?, ?, ?)");
        $query->execute([$titre, $date_article, $contenu]);

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

        header('Location:' . article_back . '?return=delete_success');
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
    $query = $bdd->query("SELECT * FROM news ORDER BY date_article DESC");
    $articles = $query->fetchAll();
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
    include("../navbar.php"); ?>

    <div class="container my-5 col-lg-10">
        <?php if (isset($_GET['message']) && $_GET['message'] === 'delete_success')
            $noti = 'L\article a été supprimé avec succès !';
        elseif (isset($_GET['message']) && $_GET['message'] === 'add_success')
            $noti = 'L\article a été ajouté avec succès !';
        elseif (isset($_GET['message']) && $_GET['message'] === 'update_success')
            $noti = 'L\article a été modifié avec succès !';
        elseif (isset($_GET['error']) && $_GET['error'] === 'bdd') {
            $noti_Err = 'Erreur lors de la connection à la base de données : ' . $_SESSION['error'];
            unset($_SESSION['error']);
        }
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
        <h1 class="mb-5 text-center">Gestion des Articles</h1>

        <!-- Formulaire d'ajout d'article -->
        <form method="POST" action="<?= article_back ?>" class="mb-5">
            <h3>Ajouter un nouvel article</h3>
            <div class="my-3">
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
                        <td class="align-middle">' . htmlspecialchars($article['titre']) . '</td>
                        <td class="align-middle">' . htmlspecialchars($article['date_article']) . '</td>
                        <td>
                            <a href=' . article_edit_back . '?id=' . $article['id_news'] . ' class="btn btn-warning my-1 me-1">Modifier</a>
                            <button type="button" class="btn btn-sm btn-danger my-1 me-1" data-bs-toggle="modal" data-bs-target="#exampleModal">Supprimer</button>
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

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmation</h1>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer cet article ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <a href="<?= article_back . '?delete_id=' . $article['id_news'] ?>" type="button" class="btn btn-danger">Supprimer</a>
                </div>
            </div>
        </div>
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