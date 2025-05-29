<?php
session_start();
require_once('../../../include/database.php');
$login_page = '../../../connexion/login.php';
require('../../check_session.php');
require('../../../include/check_timeout.php');
require_once __DIR__ . '/../../../path.php';


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add'])) {
    $titre = trim($_POST['titre']);
    $contenu = trim($_POST['contenu']);
    $auteur = $_SESSION['user_pseudo'] ?? 'Anonyme';

    if (strlen($titre) > 150 || strlen($contenu) > 1000) {
        header('Location:' . forum_annonce_back . '?error=length');
        exit;
    } elseif (empty($titre) || empty($contenu)) {
        header('Location:' . forum_annonce_back . '?error=missing_fields');
        exit;
    } else {
        try {
            $stmt = $bdd->prepare("INSERT INTO forum_sujets (titre, contenu, date_msg, categories, parent_id, auteur) VALUES (?, ?, NOW(), 'Annonces', NULL, ?);");
            $stmt->execute([$titre, $contenu, $auteur]);

            header('Location:' . forum_annonce_back . '?message=added');
            exit;
        } catch (PDOException $e) {
            $_SESSION['error'] = htmlspecialchars($e->getMessage());
            header('Location:' . forum_back . '?error=bdd');
            exit();
        }
    }
}

if (!empty($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        $query = $bdd->prepare("DELETE FROM forum_sujets WHERE id_sujet = ?");
        $query->execute([$delete_id]);

        header('Location:' . forum_annonce_back . '?message=deleted');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = htmlspecialchars($e->getMessage());
        header('Location:' . forum_back . '?error=bdd');
        exit();
    }
}

try {
    $query = $bdd->prepare("SELECT id_sujet,titre, date_creation, auteur from forum_sujets where categories='Annonces' order by date_creation desc;");
    $query->execute();
    $annonces = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = htmlspecialchars($e->getMessage());
    header('Location:' . forum_back . '?error=bdd');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<?php
$title = 'Gestion des annonces';
require('../../head.php');
?>

<body class="pb-4">
    <?php
    $page = forum_back;
    include('../../navbar.php');
    ?>
    <main class="container mb-5">
        <?php
        $noti = '';
        $noti_Err = '';
        if (isset($_GET['error']) && $_GET['error'] === 'missing_id')
            $noti_Err = 'Aucun ID spécifié !';
        elseif (isset($_GET['message']) && $_GET['message'] === 'added')
            $noti = 'Sujet ajouté !';
        elseif (isset($_GET['error']) && $_GET['error'] === 'missing_fields')
            $noti_Err = 'Veuillez remplir tous les champs !';
        elseif (isset($_GET['error']) && $_GET['error'] === 'id_invalid')
            $noti_Err = 'ID du sujet fourni est invalid !';
        elseif (isset($_GET['error']) && $_GET['error'] === 'length')
            $noti_Err = 'Veuillez respecter la longeur du titre et du contenu !';
        elseif (isset($_GET['message']) && $_GET['message'] === 'deleted')
            $noti = 'Sujet supprimé avec succès !';
        elseif (isset($_GET['message']) && $_GET['message'] === 'updated')
            $noti = 'Sujet modifié avec succès !';
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
        <?php endif;
        if (!empty($noti)) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $noti ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif ?>

        <h1 class="my-5 text-center">Gestion du forum Annonce</h1>
        <form method="post">
            <input type="hidden" name="add">
            <div class="mb-3">
                <label for="titre" class="form-label">Titre du sujet</label>
                <input type="text" class="form-control" id="titre" name="titre" maxlength="150" required oninput="updateCounter()">
                <div class="d-flex justify-content-between">
                    <small class="text-muted">Maximum 150 caractères</small>
                    <small id="counter" class="text-muted">0 / 150</small>
                </div>
            </div>

            <div class="mb-3">
                <label for="contenu" class="form-label">Message</label>
                <textarea class="form-control" id="contenu" name="contenu" rows="5" maxlength="1000" oninput="updateContentCounter()" required></textarea>
                <div class="d-flex justify-content-between">
                    <small class="text-muted">Maximum 1000 caractères</small>
                    <small id="contentCounter" class="text-muted">0 / 1000</small>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Créer le sujet</button>
        </form>

        <h3 class="text-center mt-5 mb-4">Liste des sujets Annonce</h3>
        <?php
        echo '<div class="form-group my-2 sticky-top pt-3 pb-2">
            <input type="text" id="search" class="form-control searchBoxBack" placeholder="Rechercher par titre ou auteur du sujet">
        </div>';

        if (count($annonces) > 0) {
            echo '<div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">';
            echo "<table class='table table-striped table-bordered'>";
            echo "<thead class='table-dark' style=\"position: sticky; top: 0; z-index: 1;\"><tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Date</th>
                    <th>Auteur</th>
                    <th>Actions</th>
                </tr></thead>";
            echo '<tbody id="results">';



            foreach ($annonces as $annonce) {
                echo '<tr>
                        <td class="align-middle">' . htmlspecialchars($annonce['id_sujet']) . '</td>
                        <td class="align-middle">' . htmlspecialchars($annonce['titre']) . '</td>
                        <td class="align-middle">' . htmlspecialchars($annonce['date_creation']) . '</td>
                        <td class="align-middle">' . htmlspecialchars($annonce['auteur']) . '</td>
                        <td>
                            <a href=' . annonce_edit_back . '?id=' . $annonce['id_sujet'] . ' class="btn btn-sm btn-warning my-1 me-1">Modifier</a>
                            <button type="button" class="btn btn-sm btn-danger my-1 me-1" data-bs-toggle="modal" data-bs-target="#modal' . $annonce['id_sujet'] . '">Supprimer</button>';
                echo '<div class="modal fade" id="modal' . $annonce['id_sujet'] . '" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h1 class="modal-title fs-5">Confirmation</h1>
                                </div>
                                <div class="modal-body">
                                  Êtes-vous sûr de vouloir supprimer cet annonce ?
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                  <a href="' . forum_annonce_back . '?delete_id=' . $annonce['id_sujet'] . '" class="btn btn-danger">Supprimer</a>
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
            echo "<div class='alert alert-warning'>Aucun sujet trouvé.</div>";
        }
        ?>
    </main>

    <script>
        function updateCounter() {
            const input = document.getElementById('titre');
            const counter = document.getElementById('counter');
            counter.textContent = `${input.value.length} / 150`;
        }

        function updateContentCounter() {
            const textarea = document.getElementById('contenu');
            const counter = document.getElementById('contentCounter');
            counter.textContent = `${textarea.value.length} / 1000`;
        }

        function fetchAnnonces() {
            const query = document.getElementById('search').value;

            fetch(`search_annonces.php?search=${encodeURIComponent(query)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.text())
                .then(data => {
                    document.getElementById('results').innerHTML = data;
                });
        }

        document.getElementById('search').addEventListener('input', fetchAnnonces);

        document.addEventListener("DOMContentLoaded", () => {
            updateCounter();
            updateContentCounter();
            fetchAnnonces();
        });
    </script>
</body>

</html>