<?php
session_start();
require('../include/database.php');
require('../include/check_timeout.php');

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../connexion/login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Récupérer les équipes que l'utilisateur a rejointes
$stmt = $bdd->prepare("
    SELECT e.id_équipe, e.nom, e.niveau, e.date_creation 
    FROM membres_equipe me
    JOIN equipe e ON me.id_equipe = e.id_équipe
    WHERE me.id_utilisateur = ?
");
$stmt->execute([$userId]);
$teams = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Mes équipes';
require('../include/head.php');
?>

<body>
    <?php include('../include/header.php'); ?>

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
                                <a href="../team/team_details.php?id_equipe=<?= urlencode($team['id_équipe']) ?>" class="btn btn-primary btn-sm">Voir l'équipe</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center">Vous n'avez rejoint aucune équipe pour le moment.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>