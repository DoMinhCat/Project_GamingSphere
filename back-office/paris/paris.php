<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/database.php');
require('../../include/check_timeout.php');
require_once __DIR__ . '/../../path.php';
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Gestions des paris';
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
        if (isset($_GET['message']) && $_GET['message'] === 'EDIT ME')
            $noti = 'SUCCESS MESSAGE !';
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

        <h1 class="text-center my-5">Liste des paris</h1>

        <div class="form-group mb-2 sticky-top pt-3 pb-2">
            <div class="d-flex gap-2">
                <input type="text" id="search" class="form-control searchBoxBack" placeholder="Rechercher par nom du tournois ou jeu">
                <div class="d-flex ms-2" style="gap: 0.5rem;">
                    <select id="prixFilter" class="form-select searchBoxBack">
                        <option value="">Filter</option>

                    </select>
                </div>
            </div>
        </div>

        <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
            <table class="table table-bordered table-striped">
                <thead class='table-dark' style="position: sticky; top: 0; z-index: 1;">
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Date de sortie</th>
                        <th>Catégorie</th>
                        <th>Note</th>
                        <th>Plateforme</th>
                        <th>Prix (€)</th>
                        <th>Type</th>
                        <th>Éditeur</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="jeux">
                    <?php if (count($paris) > 0): ?>
                        <?php foreach ($paris as $pari): ?>
                            <tr>
                                <td class="align-middle"><?= htmlspecialchars('content pari') ?></td>


                                <!-- <td>
                                    <div class='d-flex flex-wrap align-items-start flex-xl-row align-items-start'>
                                        <a href="<?= jeux_edit_back . '?id=' . $game['id_jeu'] ?>" class="btn btn-warning btn-sm mb-1 mb-xl-0 me-sm-1">Modifier</a>
                                        <button type="button" class="btn btn-sm btn-danger mb-1 mb-xl-0 me-sm-1" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $game['id_jeu'] ?>">Supprimer</button>
                                    </div>
                                    <div class="modal fade" id="deleteModal<?= $game['id_jeu'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $game['id_jeu'] ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="deleteModalLabel<?= $game['id_jeu'] ?>">Confirmation</h1>
                                                </div>
                                                <div class="modal-body">
                                                    Êtes-vous sûr de vouloir supprimer ce jeu du magasin ?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <a type="button" class="btn btn-danger" href="delete_game.php?id=<?= $game['id_jeu'] ?>">Supprimer</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td> -->
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center">Aucun pari trouvé.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>