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
$title = 'Liste des tournois';
require('../head.php');
?>

<body class="pb-4">
    <?php
    $page = index_back;
    include('../navbar.php'); ?>

    <div class="container mb-5">
        <?php if (isset($_GET['message']) && $_GET['message'] === 'tournoi_deleted')
            $noti = 'Le tournoi a été supprimé avec succès !';
        elseif (isset($_GET['message']) && $_GET['message'] === 'tournoi_added')
            $noti = 'Un nouveau tournois a été ajouté !';
        elseif (isset($_GET['updated']) && $_GET['updated'] == 1)
            $noti = 'Les résultats du tournoi ont bien été enregistrés !';
        elseif (isset($_GET['error']) && $_GET['error'] === 'add') {
            $noti_Err = 'Erreur lors de l\'ajout du tournoi : ' . $_SESSION['error'];
            unset($_SESSION['error']);
        } elseif (isset($_GET['error']) && $_GET['error'] === 'delete') {
            $noti_Err = 'Erreur lors de la suppression du tournoi : ' . $_SESSION['error'];
            unset($_SESSION['error']);
        } elseif (isset($_GET['error']) && $_GET['error'] === 'missing_id')
            $noti_Err = 'Aucun ID spécifié';
        elseif (isset($_GET['error']) && $_GET['error'] === 'no_id')
            $noti_Err = 'Tournoi non trouvé ! ';
        elseif (isset($_GET['error']) && $_GET['error'] === 'db') {
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
        <h1 class="my-5 text-center">Liste des Tournois Disponibles</h1>
        <div class="mb-3 text-end">
            <a href="<?= tournois_add_back ?>" class="btn btn-primary">Ajouter un tournoi</a>
        </div>
        <div class="form-group my-2 sticky-top pt-3 pb-2">
            <div class="input-group">
                <input type="text" id="search_tournois" class="form-control" placeholder="Rechercher par nom du tournoi ou jeu">
                <div class="input-group-append">
                    <select id="statusFilter" class="form-select">
                        <option value="">Tous</option>
                        <option value="attente">En attente</option>
                        <option value="encours">En cours</option>
                        <option value="termine">Terminé</option>
                    </select>
                    <select id="typeFilter" class="form-select">
                        <option value="">Tous</option>
                        <option value="solo">Solo</option>
                        <option value="equipe">Equipe</option>
                    </select>
                </div>
            </div>
        </div>
        <?php
        try {
            $stmt = $bdd->query("SELECT id_tournoi, nom_tournoi, date_debut, date_fin, jeu, status_ENUM, type FROM tournoi ORDER BY date_debut DESC");
            $tournois = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($tournois) > 0): ?>
                <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                    <table class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Nom</th>
                                <th>Date de Début</th>
                                <th>Date de Fin</th>
                                <th>Statut</th>
                                <th>Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tournois_results">
                            <?php foreach ($tournois as $tournoi): ?>
                                <tr>
                                    <td><?= htmlspecialchars($tournoi['id_tournoi']) ?></td>
                                    <td><?= htmlspecialchars($tournoi['nom_tournoi']) ?></td>
                                    <td><?= htmlspecialchars($tournoi['jeu']) ?></td>
                                    <td><?= htmlspecialchars($tournoi['date_debut']) ?></td>
                                    <td><?= htmlspecialchars($tournoi['date_fin']) ?></td>
                                    <td><?= htmlspecialchars($tournoi['status_ENUM']) ?></td>
                                    <td><?= htmlspecialchars($tournoi['type']) ?></td>
                                    <td>
                                        <a href="<?= tournois_edit_back . '?id_tournoi=' . $tournoi['id_tournoi'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                                        <a href="/back-office/tournois/delete_tournoi.php?id_tournoi=<?= $tournoi['id_tournoi'] ?>" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#exampleModal">Supprimer</a>
                                        <a href="<?= tournois_result_back . '?id_tournoi=' . $tournoi['id_tournoi'] ?>" class="btn btn-sm btn-success">Éditer les Résultats</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center">Aucun tournoi disponible pour le moment.</div>
        <?php endif;
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>Erreur lors de la récupération des tournois : " . htmlspecialchars($e->getMessage()) . "</div>";
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
                    Êtes-vous sûr de vouloir supprimer ce tournoi ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger">Supprimer</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function fetchFilteredUsers() {
            const query = document.getElementById('search_tournois').value;
            const status = document.getElementById('statusFilter').value;
            const type = document.getElementById('typeFilter').value;

            fetch(`search_tournois.php?search=${encodeURIComponent(query)}&status=${encodeURIComponent(status)}&type=${encodeURIComponent(type)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.text())
                .then(data => {
                    document.getElementById('tournois_results').innerHTML = data;
                });
        }

        document.getElementById('search_tournois').addEventListener('input', fetchFilteredUsers);
        document.getElementById('statusFilter').addEventListener('change', fetchFilteredUsers);
        document.getElementById('typeFilter').addEventListener('change', fetchFilteredUsers);
    </script>
</body>

</html>