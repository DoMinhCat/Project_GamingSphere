<?php
session_start();
require('../../include/database.php');
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/check_timeout.php');
require_once __DIR__ . '/../../path.php';

try {
    $stmt = $bdd->prepare("SELECT * FROM mots_interdits;");
    $stmt->execute();
    $mots = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = htmlspecialchars($e->getMessage());
    header('Location:' . communication_back . '?error=bdd');
    exit();
}

if (!empty($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        $query = $bdd->prepare("DELETE FROM mots_interdits WHERE id_mot = ?");
        $query->execute([$delete_id]);

        header('Location:' . communication_back . '?message=delete_success');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = htmlspecialchars($e->getMessage());
        header('Location:' . communication_back . '?error=bdd');
        exit();
    }
}
if (!empty($_GET['id_edit']) && !empty($_GET['status'])) {
    $edit = $_GET['id_edit'];
    $status = $_GET['status'];
    try {
        $query = $bdd->prepare("UPDATE mots_interdits SET status = ? WHERE id_mot = ?");
        $query->execute([$status, $edit]);

        header('Location:' . communication_back . '?message=update_success');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = htmlspecialchars($e->getMessage());
        header('Location:' . communication_back . '?error=bdd');
        exit();
    }
}
if (!empty($_GET['delete_bad'])) {
    if ($mots) {
        $conditions = [];
        $params = [];

        foreach ($mots as $mot) {
            $conditions[] = "contenu LIKE ?";
            $params[] = '%' . $mot['mot'] . '%';
        }
        try {
            $query = $bdd->prepare("DELETE FROM forum_reponses WHERE " . implode(" OR ", $conditions) . ";");
            $query->execute($params);
            $deletedCount = $query->rowCount();

            header('Location:' . communication_back . '?message=bad_success&count=' . $deletedCount);
            exit();
        } catch (PDOException $e) {
            $_SESSION['error'] = htmlspecialchars($e->getMessage());
            header('Location:' . communication_back . '?error=bdd');
            exit();
        }
    } else {
        header('Location:' . communication_back . '?error=no_word');
        exit();
    }
}

if (isset($_POST['add_mot'])) {
    $word = strtolower(trim($_POST['add_mot']));
    try {
        $query = $bdd->prepare("INSERT INTO mots_interdits (mot) VALUES (?);");
        $query->execute([$word]);

        header('Location:' . communication_back . '?message=add_success');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = htmlspecialchars($e->getMessage());
        header('Location:' . communication_back . '?error=bdd');
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<?php
$title = 'Gestions des commentaires';
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
        if (isset($_GET['error']) && $_GET['error'] === 'bdd') {
            $noti_Err = 'Erreur lors de la connection à la base de données : ' . $_SESSION['error'];
            unset($_SESSION['error']);
        } elseif (isset($_GET['message']) && $_GET['message'] === 'delete_success')
            $noti = 'Mot supprimé avec succès !';
        elseif (isset($_GET['message']) && $_GET['message'] === 'update_success')
            $noti = 'Statut du mot modifié avec succès !';
        elseif (isset($_GET['message']) && $_GET['message'] === 'bad_success')
            $noti = $_GET['count'] . ' mauvais commentaires supprimés !';
        elseif (isset($_GET['message']) && $_GET['message'] === 'add_success')
            $noti = 'Mot ajouté avec succès !';
        elseif (isset($_GET['error']) && $_GET['error'] === 'no_word')
            $noti = 'Aucun mot interdit !';

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

        <h1 class="my-5 text-center">Gestion commentaires</h1>
        <form action="" method="post">
            <h3>Ajouter un nouvel mot interdit</h3>
            <div class="my-3">
                <label for="mot" class="form-label">Mot interdit</label>
                <input type="text" class="form-control" id="mot" name="mot" required>
            </div>
            <button type="submit" name="add_mot" class="btn btn-primary">Ajouter mot</button>
        </form>


        <div class="d-flex justify-content-center text-center my-4">
            <a href="<?= communication_back . '?delete_bad=1' ?>" class="btn btn-lg btn-danger text-center">Supprimer tous les commentaires contenant les mots interdits</a>
        </div>


        <h3 class="text-center mb-4">Liste des mots interdits</h3>
        <?php
        if (count($mots) > 0) {
            echo '<div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">';
            echo "<table class='table table-striped table-bordered'>";
            echo "<thead class='table-dark' style=\"position: sticky; top: 0; z-index: 1;\"><tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr></thead>";
            echo '<tbody>';

            foreach ($mots as $mot) {
                echo '<tr>
                        <td class="align-middle">' . htmlspecialchars($mot['id_mot']) . '</td>
                        <td class="align-middle">' . htmlspecialchars($mot['mot']) . '</td>
                        <td class="align-middle">' . htmlspecialchars($mot['status']) . '</td>
                        <td>';
                echo '<a href="' . communication_back . '?id_edit=' . $mot['id_mot'] . '&status=' . ($mot['status'] == 1 ? 0 : 1) . '" class="btn btn-outline-' . ($mot['status'] == 1 ? 'success' : 'danger') . '">' . ($mot['status'] == 1 ? 'Actif' : 'Inactif') . '</button>';

                echo '<button type="button" class="btn btn-sm btn-danger my-1 me-1" data-bs-toggle="modal" data-bs-target="#modal' . $mot['id_mot'] . '">Supprimer</button>';
                echo '<div class="modal fade" id="modal' . $mot['id_mot'] . '" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h1 class="modal-title fs-5">Confirmation</h1>
                                </div>
                                <div class="modal-body">
                                  Êtes-vous sûr de vouloir supprimer ce mot ?
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                  <a href="' . communication_back . '?delete_id=' . $mot['id_mot'] . '" class="btn btn-danger">Supprimer</a>
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

</body>

</html>