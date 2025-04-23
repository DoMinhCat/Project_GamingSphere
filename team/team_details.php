<?php
session_start();
$login_page = '../connexion/login.php';
require('../include/check_session.php');
require('../include/check_timeout.php');
require('../include/database.php');
require_once __DIR__ . '/../path.php';

$teamId = $_GET['id_équipe'] ?? null;

if (!$teamId) {
    echo "ID de l'équipe manquant.";
    exit();
}


$stmt = $bdd->prepare("SELECT nom, niveau, date_creation FROM equipe WHERE id_équipe = ?");
$stmt->execute([$teamId]);
$team = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$team) {
    echo "Équipe introuvable.";
    exit();
}

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
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}
?>

<body>
    <?php include('../include/header.php'); ?>

    <div class="container my-5">
        <h1 class="mb-4 text-center">Détails de l'équipe</h1>
        <?php if (isset($_GET['success'])): ?>
            <?php if ($_GET['success'] === 'invitation_sent'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Votre demande pour rejoindre l'équipe a été envoyée avec succès.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif ($_GET['success'] === 'left_team'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Vous avez quitté l'équipe avec succès.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        <?php elseif (isset($_GET['error'])): ?>
            <?php if ($_GET['error'] === 'invitation_already_pending'): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    Une demande pour rejoindre cette équipe est déjà en attente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php else: ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Une erreur s'est produite : <?= htmlspecialchars($_GET['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>


        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title"><?= htmlspecialchars($team['nom']) ?></h2>
                <p class="card-text"><strong>Niveau :</strong> <?= htmlspecialchars($team['niveau']) ?></p>
                <p class="card-text"><strong>Date de création :</strong> <?= htmlspecialchars(date('Y-m-d', strtotime($team['date_creation'] ?? ''))) ?></p>
            </div>
        </div>

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
                            <td><?= htmlspecialchars($member(date('Y-m-d'['date_rejoint']))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucun membre dans cette équipe.</p>
        <?php endif; ?>

        <?php
        $stmt = $bdd->prepare("SELECT COUNT(*) FROM membres_equipe WHERE id_equipe = ? AND id_utilisateur = ?");
        $stmt->execute([$teamId, $_SESSION['user_id']]);
        $isMember = $stmt->fetchColumn() > 0;

        $stmt = $bdd->prepare("SELECT COUNT(*) FROM invitations WHERE id_equipe = ? AND id_utilisateur = ? AND statut = 'en attente'");
        $stmt->execute([$teamId, $_SESSION['user_id']]);
        $isInvitationPending = $stmt->fetchColumn() > 0;

        if ($isMember): ?>

            <p class="text-success mt-3">Vous êtes membre de cette équipe.</p>
            <form action="../team/leave_team.php" method="POST" class="mt-3">
                <input type="hidden" name="team_id" value="<?= htmlspecialchars($teamId) ?>">
                <button type="submit" class="btn btn-danger">Quitter l'équipe</button>
            </form>
        <?php elseif (!$isMember && !$isInvitationPending): ?>
            <form action="../team/send_invitation.php" method="POST" class="mt-3">
                <input type="hidden" name="team_id" value="<?= htmlspecialchars($teamId) ?>">
                <button type="submit" class="btn btn-primary">Envoyer une demande pour rejoindre l'équipe</button>
            </form>
        <?php elseif ($isInvitationPending): ?>
            <p class="text-warning mt-3">Une demande pour rejoindre cette équipe est déjà en attente.</p>
        <?php endif; ?>
        <div class="text-center mt-4">
            <a href="team_list.php" class="btn btn-secondary">Retour à la liste des équipes</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
