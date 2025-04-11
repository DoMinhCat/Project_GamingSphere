<?php
session_start();
require('../include/database.php');
require('../include/check_timeout.php');

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../connexion/login.php');
    exit();
}

// Récupérer le terme de recherche
$search = $_GET['search'] ?? '';

// Requête pour récupérer les équipes
$stmt = $bdd->prepare("
    SELECT id_équipe, nom, niveau 
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
?>

<body>
    <?php include('../include/header.php'); ?>

    <div class="container my-5">
        <h1 class="mb-4 text-center">Liste des équipes</h1>

        <!-- Formulaire de recherche -->
        <form method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Rechercher une équipe..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn btn-primary">Rechercher</button>
            </div>
        </form>

        <!-- Liste des équipes -->
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
                                <a href="team_details.php?id_equipe=<?= $team['id_équipe'] ?>" class="btn btn-sm btn-info">Voir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center">Aucune équipe trouvée.</p>
        <?php endif; ?>

        <!-- Bouton pour créer une équipe -->
        <div class="text-center mt-4">
            <a href="create_team.php" class="btn btn-success">Créer une nouvelle équipe</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>