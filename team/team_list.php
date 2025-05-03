<?php
session_start();
$login_page = '../connexion/login.php';
require('../include/check_session.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';
require('../include/database.php');

$search = $_GET['search'] ?? '';
$stmt = $bdd->prepare("
    SELECT id_equipe, nom, niveau 
    FROM equipe 
    WHERE nom LIKE ?
    ORDER BY nom ASC
");
$stmt->execute(['%' . $search . '%']);
$teams = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Liste des équipes';
require('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}
?>

<body>
    <?php
    include('../include/header.php');
    include('/profil/navbar.php');
    ?>

    <div class="container my-5">
        <h1 class="mb-4 text-center">Liste des équipes</h1>
        <form method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Rechercher une équipe..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn btn-primary">Rechercher</button>
            </div>
        </form>
        <?php if (count($teams) > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nom de l'équipe</th>
                        <th>Niveau</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($teams as $team): ?>
                        <tr>
                            <td><?= htmlspecialchars($team['nom']) ?></td>
                            <td><?= htmlspecialchars($team['niveau']) ?></td>
                            <td>
                                <a href="<?= team_details ?>?id_equipe=<?= $team['id_equipe'] ?>" class="btn btn-sm btn-info">Voir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center">Aucune équipe trouvée.</p>
        <?php endif; ?>
        <div class="text-center mt-4">
            <a href="<?= create_team ?>" class="btn btn-success">Créer une nouvelle équipe</a>
        </div>
    </div>
</body>

</html>