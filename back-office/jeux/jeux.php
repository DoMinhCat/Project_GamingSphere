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
    <div class="container my-5">
        <h1 class="text-center mb-3" style="font-size: 1.5rem;">Liste des jeux</h1>

        <div class="text-end mb-3">
            <a href="<?= jeux_add_back ?>" class="btn btn-primary">Ajouter un jeu</a>
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
                <tbody>
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
                                    <a href="delete_game.php?id=$game['id_jeu'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Voulez-vous vraiment supprimer ce jeu ?');">Supprimer</a>
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
        <?php if (isset($_GET['message']) && $_GET['message'] === 'success'): ?>
            <div class="alert alert-success mt-3" role="alert">
                Jeu ajouté avec succès !
            </div>

        <?php endif;
        if (isset($_GET['message']) && $_GET['message'] === 'delete'): ?>
            <div class="alert alert-success mt-3" role="alert">
                Jeu supprimé avec succès !
            </div>
        <?php endif;
        if (isset($_GET['message']) && $_GET['message'] === 'updated'): ?>
            <div class="alert alert-success mt-3" role="alert">
                Jeu modifié avec succès !
            </div>
        <?php endif;
        if (isset($_GET['message_err']) && !empty($_GET['message_err'])): ?>
            <div class="alert alert-danger mt-3" role="alert">
                <?= htmlspecialchars($_GET['message_err']); ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>