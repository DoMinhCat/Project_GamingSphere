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
    <div class="container mb-5">
        <?php if (isset($_GET['message']) && $_GET['message'] === 'success'): ?>
            <div class="alert alert-success mb-3" role="alert">
                Jeu ajouté avec succès !
            </div>

        <?php endif;
        if (isset($_GET['message']) && $_GET['message'] === 'delete'): ?>
            <div class="alert alert-success mb-3" role="alert">
                Jeu supprimé avec succès !
            </div>
        <?php endif;
        if (isset($_GET['message']) && $_GET['message'] === 'updated'): ?>
            <div class="alert alert-success mb-3" role="alert">
                Jeu modifié avec succès !
            </div>
        <?php endif;
        if (isset($_GET['message_err']) && !empty($_GET['message_err'])): ?>
            <div class="alert alert-danger mb-3" role="alert">
                <?= htmlspecialchars($_GET['message_err']); ?>
            </div>
        <?php endif; ?>

        <h1 class="text-center mt-5 mb-3">Liste des jeux</h1>

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
                                <td><?= htmlspecialchars($game['nom']) ?></td>
                                <td><?= htmlspecialchars($game['date_sortie']) ?></td>
                                <td><?= htmlspecialchars($game['catégorie']) ?></td>
                                <td><?= htmlspecialchars($game['note_jeu']) ?></td>
                                <td><?= htmlspecialchars($game['plateforme']) ?></td>
                                <td><?= htmlspecialchars($game['prix']) ?></td>
                                <td><?= htmlspecialchars($game['type']) ?></td>
                                <td><?= htmlspecialchars($game['éditeur']) ?></td>
                                <td class="text-center">
                                    <a href="delete_game.php?id=<?= $game['id_jeu'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Voulez-vous vraiment supprimer ce jeu ?');">Supprimer</a>
                                    <a href="<?= jeux_edit_back . '?id=' . $game['id_jeu'] ?>" class="btn btn-warning btn-sm">Modifier</a>
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