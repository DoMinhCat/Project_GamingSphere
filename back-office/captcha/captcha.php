<?php
session_start();
require('../../include/database.php');
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/check_timeout.php');
require_once __DIR__ . '/../../path.php';

try {
    $query = $bdd->query("SELECT id_captcha, question,answer,status,id_auteur,email FROM captcha join utilisateurs where id_utilisateurs=captcha.id_auteur ORDER BY id_captcha");
    $captchas = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = htmlspecialchars($e->getMessage());
    header('Location:' . captcha_back . '?error=bdd');
    exit();
}

if (isset($_POST['add_captcha'])) {
    $question = trim($_POST['question']);
    $answer = trim($_POST['answer']);
    try {
        $query = $bdd->prepare("INSERT INTO captcha (question, answer, id_auteur) VALUES (?, ?, ?)");
        $query->execute([$question, $answer, $_SESSION['user_id']]);

        header('Location:' . captcha_back . '?message=add_success');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = htmlspecialchars($e->getMessage());
        header('Location:' . captcha_back . '?error=bdd');
        exit();
    }
}
if (!empty($_GET['delete_id'])) {
    $id_delete = htmlspecialchars($_GET['delete_id']);
    try {
        $query = $bdd->prepare("select id_captcha from captcha WHERE id_captcha=?");
        $query->execute([$id_delete]);
        $captcha = $query->fetch();
        if (!$captcha) {
            header('Location:' . captcha_back . '?message=id_invalid');
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = htmlspecialchars($e->getMessage());
        header('Location:' . captcha_back . '?error=bdd');
        exit();
    }

    try {
        $query = $bdd->prepare("DELETE from captcha WHERE id_captcha=?");
        $query->execute([$id_delete]);

        header('Location:' . captcha_back . '?message=delete_success');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = htmlspecialchars($e->getMessage());
        header('Location:' . captcha_back . '?error=bdd');
        exit();
    }
}

?>


<!DOCTYPE html>
<html lang="fr">

<?php
$title = 'Questions captcha';
require('../head.php');
?>

<body class="pb-4">
    <?php
    $page = index_back;
    include('../navbar.php');
    ?>

    <main class="container mb-5">
        <?php
        $noti = '';
        $noti_Err = '';
        if (isset($_GET['message']) && $_GET['message'] === 'delete_success')
            $noti = 'La question a été supprimée avec succès !';
        elseif (isset($_GET['message']) && $_GET['message'] === 'add_success')
            $noti = 'La question a été ajoutée avec succès !';
        elseif (isset($_GET['message']) && $_GET['message'] === 'update_success')
            $noti = 'La question a été modifiée avec succès !';
        elseif (isset($_GET['error']) && $_GET['error'] === 'bdd') {
            $noti_Err = 'Erreur lors de la connection à la base de données : ' . $_SESSION['error'];
            unset($_SESSION['error']);
        } elseif (isset($_GET['error']) && $_GET['error'] === 'id_invalid')
            $noti_Err = 'ID de la question fourni invalid !';

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
        <h1 class="my-5 text-center">Gestion des Captchas</h1>

        <!-- Formulaire d'ajout de captcha -->
        <form method="POST" action="" class="my-5">
            <h3 class="mb-3">Ajouter une nouvelle question captcha</h3>
            <div class="my-3">
                <label for="question" class="form-label">Question</label>
                <input type="text" class="form-control" id="question" name="question" placeholder="Quel est le nom du professeur de C à l'ESGI ?" required>
            </div>
            <div class="mb-3">
                <label for="answer" class="form-label">La bonne réponse</label>
                <textarea class="form-control" id="answer" name="answer" rows="1" placeholder="Sananes" required></textarea>
            </div>
            <button type="submit" name="add_captcha" class="btn btn-primary">Ajouter captcha</button>
        </form>

        <!-- Affichage des captchas -->
        <h3 class="text-center mb-4">Liste des captchas</h3>
        <?php

        echo '<div class="form-group my-2 sticky-top pt-3 pb-2">
                <div class="d-flex gap-2">
                    <input type="text" id="search_captcha" class="form-control searchBoxBack" placeholder="Rechercher par question ou réponse">
                    <div class="d-flex ms-2" style="gap: 0.5rem;">
                    <select id="statusFilter" class="form-select searchBoxBack">
                        <option value="">Tous</option>
                        <option value="1">Actif</option>
                        <option value="0">Inactif</option>
                    </select>
                    </div>
                </div>
                </div>';

        if (count($captchas) > 0) {
            echo '<div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">';
            echo "<table class='table table-striped table-bordered'>";
            echo "<thead class='table-dark' style=\"position: sticky; top: 0; z-index: 1;\"><tr>
                    <th>ID</th>
                    <th>Question</th>
                    <th>Bonne réponse</th>
                    <th>Auteur</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr></thead>";
            echo '<tbody id="captcha_results">';



            foreach ($captchas as $captcha) {
                echo '<tr>
                        <td class="align-middle">' . htmlspecialchars($captcha['id_captcha']) . '</td>
                        <td class="align-middle">' . htmlspecialchars($captcha['question']) . '</td>
                        <td class="align-middle">' . htmlspecialchars($captcha['answer']) . '</td>
                        <td class="align-middle">' . htmlspecialchars($captcha['email']) . '</td>
                        <td class="align-middle">' . ($captcha['status'] == 1 ? 'Actif' : 'Inactif') . '</td>
                        
                        <td>
                            <a href=' . captcha_edit_back . '?id=' . $captcha['id_captcha'] . ' class="btn btn-sm btn-warning my-1 me-1">Modifier</a>
                            <button type="button" class="btn btn-sm btn-danger my-1 me-1" data-bs-toggle="modal" data-bs-target="#modal' . $captcha['id_captcha'] . '">Supprimer</button>';
                echo '<div class="modal fade" id="modal' . $captcha['id_captcha'] . '" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h1 class="modal-title fs-5">Confirmation</h1>
                                </div>
                                <div class="modal-body">
                                  Êtes-vous sûr de vouloir supprimer cette question captcha ?
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                  <a href="' . captcha_back . '?delete_id=' . $captcha['id_captcha'] . '" class="btn btn-danger">Supprimer</a>
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
            echo "<div class='alert alert-warning'>Aucun question trouvée.</div>";
        }
        ?>
    </main>

    <script>
        function fetchFilteredCaptchas() {
            const query = document.getElementById('search_captcha').value;
            const status = document.getElementById('statusFilter').value;

            fetch(`search_captcha.php?search=${encodeURIComponent(query)}&status=${encodeURIComponent(status)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.text())
                .then(data => {
                    document.getElementById('captcha_results').innerHTML = data;
                });
        }

        document.getElementById('search_captcha').addEventListener('input', fetchFilteredCaptchas);
        document.getElementById('statusFilter').addEventListener('change', fetchFilteredCaptchas);
    </script>
</body>

</html>