<?php
session_start();
require('../include/database.php');
require('../include/check_timeout.php');

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../connexion/login.php');
    exit();
}

// Récupérer l'ID de l'équipe depuis l'URL
$teamId = $_GET['id_equipe'] ?? null;

if (!$teamId) {
    echo "ID de l'équipe manquant.";
    exit();
}

// Récupérer les détails de l'équipe
$stmt = $bdd->prepare("SELECT nom, niveau, date_creation FROM equipe WHERE id_équipe = ?");
$stmt->execute([$teamId]);
$team = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$team) {
    echo "Équipe introuvable.";
    exit();
}

// Récupérer les membres de l'équipe
$stmt = $bdd->prepare("
    SELECT u.pseudo, me.role, me.date_rejoint 
    FROM membres_equipe me
    JOIN utilisateurs u ON me.id_utilisateur = u.id_utilisateurs
    WHERE me.id_equipe = ?
");
$stmt->execute([$teamId]);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Détails de l\'équipe';
require('../include/head.php');
?>

<body>
    <?php include('../include/header.php'); ?>

    <div class="container my-5">
        <h1 class="mb-4 text-center">Détails de l'équipe</h1>

        <!-- Détails de l'équipe -->
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title"><?= htmlspecialchars($team['nom']) ?></h2>
                <p class="card-text"><strong>Niveau :</strong> <?= htmlspecialchars($team['niveau']) ?></p>
                <p class="card-text"><strong>Date de création :</strong> <?= htmlspecialchars(date('Y-m-d', strtotime($team['date_creation'] ?? ''))) ?></p>
            </div>
        </div>

        <!-- Liste des membres -->
        <h3 class="mb-3">Membres de l'équipe</h3>
        <?php if (count($members) > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Pseudo</th>
                        <th>Rôle</th>
                        <th>Date de Rejoint</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $member): ?>
                        <tr>
                            <td><?= htmlspecialchars($member['pseudo']) ?></td>
                            <td><?= htmlspecialchars($member['role']) ?></td>
                            <td><?= htmlspecialchars($member['date_rejoint']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucun membre dans cette équipe.</p>
        <?php endif; ?>

        <?php
// Vérifiez si l'utilisateur est déjà membre de l'équipe
$stmt = $bdd->prepare("SELECT COUNT(*) FROM membres_equipe WHERE id_equipe = ? AND id_utilisateur = ?");
$stmt->execute([$teamId, $_SESSION['user_id']]);
$isMember = $stmt->fetchColumn() > 0;

// Affichez le bouton "Rejoindre l'équipe" uniquement si l'utilisateur n'est pas membre
if (!$isMember): ?>
    <form action="../team/join_team.php" method="POST" class="mt-3">
        <input type="hidden" name="team_id" value="<?= htmlspecialchars($teamId) ?>">
        <button type="submit" class="btn btn-primary">Rejoindre l'équipe</button>
    </form>
<?php endif; ?>

        <!-- Bouton de retour -->
        <div class="text-center mt-4">
            <a href="team_list.php" class="btn btn-secondary">Retour à la liste des équipes</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>