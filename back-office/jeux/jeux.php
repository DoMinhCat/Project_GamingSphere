<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/check_timeout.php');
require('../../include/database.php');
require_once __DIR__ . '/../../path.php';

$stmt = $bdd->query("SELECT id_jeu, catégorie, date_sortie, image, nom, note_jeu, plateforme, prix, type, éditeur FROM jeu");
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">

<?php
$title = 'Liste des jeux';
require('../head.php');
?>

<body class="pb-4">
    <?php
    $page = index_back;
    include('../navbar.php');
    ?>
    <div class="container mb-5 col-lg-10">

        <?php
        $noti = '';
        $noti_Err = '';
        if (isset($_GET['message']) && $_GET['message'] === 'deleted')
            $noti = 'Le jeu a été supprimé avec succès !';
        elseif (isset($_GET['message']) && $_GET['message'] === 'success')
            $noti = 'Le jeu a été ajouté avec succès !';
        elseif (isset($_GET['message']) && $_GET['message'] === 'updated')
            $noti = 'Le jeu a été modifié avec succès !';
        elseif (isset($_GET['error']) && $_GET['error'] === 'id_invalid')
            $noti_Err = 'ID du jeu fourni invalid !';
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

        <h1 class="text-center my-5">Liste des jeux</h1>

        <div class="text-end mb-2">
            <a href="<?= jeux_add_back ?>" class="btn btn-primary">Ajouter un jeu</a>
        </div>

        <div class="form-group mb-2 sticky-top pt-3 pb-2">
            <div class="d-flex gap-2">
                <input type="text" id="search" class="form-control searchBoxBack" placeholder="Rechercher par nom, catégorie, plateforme, type ou éditeur">
                <div class="d-flex ms-2" style="gap: 0.5rem;">
                    <select id="prixFilter" class="form-select searchBoxBack">
                        <option value="">Prix</option>
                        <option value="0-20">0-20</option>
                        <option value="20-40">20-40</option>
                        <option value="40+">40+</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
            <table class="table table-bordered">
                <thead class="table-dark">
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
                    <?php if (count($games) > 0): ?>
                        <?php foreach ($games as $game): ?>
                            <tr>
                                <td class="align-middle"><?= htmlspecialchars($game['id_jeu']) ?></td>
                                <td class="align-middle"><?= htmlspecialchars($game['nom']) ?></td>
                                <td class="align-middle"><?= htmlspecialchars($game['date_sortie']) ?></td>
                                <td class="align-middle"><?= htmlspecialchars($game['catégorie']) ?></td>
                                <td class="align-middle"><?= htmlspecialchars($game['note_jeu']) ?></td>
                                <td class="align-middle"><?= htmlspecialchars($game['plateforme']) ?></td>
                                <td class="align-middle"><?= htmlspecialchars($game['prix']) ?></td>
                                <td class="align-middle"><?= htmlspecialchars($game['type']) ?></td>
                                <td class="align-middle"><?= htmlspecialchars($game['éditeur']) ?></td>
                                <td>
                                    <div class='d-flex flex-wrap align-items-start flex-lg-row align-items-start'>
                                        <a href="<?= jeux_edit_back . '?id=' . $game['id_jeu'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                                        <button type="button" class="btn btn-sm btn-danger mb-1 mb-lg-0 me-sm-1" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $game['id_jeu'] ?>">Supprimer</button>
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
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center">Aucun jeu trouvé.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function fetchFilteredUsers() {
            const query = document.getElementById('search').value;
            const prix = document.getElementById('prixFilter').value;

            fetch(`search_jeux.php?search=${encodeURIComponent(query)}&prix=${encodeURIComponent(prix)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.text())
                .then(data => {
                    document.getElementById('jeux').innerHTML = data;
                });
        }

        document.getElementById('search').addEventListener('input', fetchFilteredUsers);
        document.getElementById('prixFilter').addEventListener('change', fetchFilteredUsers);
    </script>
</body>

</html>