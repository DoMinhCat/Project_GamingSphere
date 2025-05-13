<?php
session_start();
$login_page = '../connexion/login.php';
require('../include/database.php');
require('../include/check_timeout.php');
require('../include/check_session.php');
require_once __DIR__ . '/../path.php';

$userId = $_SESSION['user_id'];
$stmt = $bdd->prepare("
    SELECT e.id_equipe, e.nom, e.niveau, e.date_creation 
    FROM membres_equipe me
    JOIN equipe e ON me.id_equipe = e.id_equipe
    WHERE me.id_utilisateur = ?
");
$stmt->execute([$userId]);
$teams = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Mes équipes';
$pageCategory = 'profil';
echo "<script>const pageCategory = '$pageCategory';</script>";
require('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}
?>

<body>
    <?php include('../include/header.php');
    include('navbar.php');
    ?>

    <div class="container my-5">
        <h1 class="mb-4 text-center">Mes équipes</h1>

        <?php if (count($teams) > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nom de l'équipe</th>
                        <th>Niveau</th>
                        <th>Date de création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($teams as $team): ?>
                        <tr>
                            <td><?= htmlspecialchars($team['nom']) ?></td>
                            <td><?= htmlspecialchars($team['niveau']) ?></td>
                            <td><?= htmlspecialchars(date('Y-m-d', strtotime($team['date_creation']))) ?></td>
                            <td>
                                <a href="../team/team_details.php?id_equipe=<?= urlencode($team['id_equipe']) ?>" class="btn btn-primary btn-sm">Voir l'équipe</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center">Vous n'avez rejoint aucune équipe pour le moment.</p>
        <?php endif; ?>
        <div class="text-center mt-4">
            <a href="<?= create_team ?>" class="btn btn-success">Créer une nouvelle équipe</a>
            <a href="<?= team_list ?>" class="btn btn-success">Liste des équipes</a>
        </div>
    </div>

</body>

</html>