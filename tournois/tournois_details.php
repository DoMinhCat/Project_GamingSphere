<?php
session_start();
include('../include/database.php');
require('../include/check_timeout.php');

$this_page = 'tournois_details.php';

if (!isset($_GET['id_tournoi']) || empty($_GET['id_tournoi'])) {
    header("Location: tournois_main.php?message=missing_id");
    exit();
}

$id_tournoi = intval($_GET['id_tournoi']); // Sécurisez l'ID

try {
    $stmt = $bdd->prepare("SELECT id_tournoi, nom_tournoi, date_debut, date_fin, jeu, status_ENUM FROM tournoi WHERE id_tournoi = ?");
    $stmt->execute([$id_tournoi]);
    $tournoi = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tournoi) {
        header("Location: tournois_main.php?message=tournoi_not_found");
        exit();
    }
    $is_registered = false;
    if (isset($_SESSION['user_id'])) {
        $user_id = intval($_SESSION['user_id']);
        $check_stmt = $bdd->prepare("SELECT COUNT(*) FROM inscription_tournoi WHERE id_tournoi = ? AND user_id = ?");
        $check_stmt->execute([$id_tournoi, $user_id]);
        $is_registered = $check_stmt->fetchColumn() > 0;
    }
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Erreur lors de la récupération des informations du tournoi : " . htmlspecialchars($e->getMessage()) . "</div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Détails du Tournoi';
include('../include/head.php');
?>
<body>
    <?php include('../include/header.php'); ?>

    <div class="container my-5">
        <h1 class="mb-4 text-center">Détails du Tournoi</h1>

        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="card-title"><?= htmlspecialchars($tournoi['nom_tournoi']) ?></h2>
                <p class="card-text"><strong>Jeu :</strong> <?= htmlspecialchars($tournoi['jeu']) ?></p>
                <p class="card-text"><strong>Date de Début :</strong> <?= htmlspecialchars($tournoi['date_debut']) ?></p>
                <p class="card-text"><strong>Date de Fin :</strong> <?= htmlspecialchars($tournoi['date_fin']) ?></p>
                <p class="card-text"><strong>Statut :</strong> <?= htmlspecialchars($tournoi['status_ENUM']) ?></p>
            </div>
            <div class="card-footer text-center">
                <a href="tournois_main.php" class="btn btn-secondary">Retour à la liste</a>
                <?php if ($is_registered): ?>
                <button class="btn btn-danger desinscrire-btn" data-id="<?= $tournoi['id_tournoi'] ?>">Se désinscrire</button>
            <?php else: ?>
                <button class="btn btn-warning participer-btn" data-id="<?= $tournoi['id_tournoi'] ?>">Participer</button>
            <?php endif; ?>
            </div>
        </div>
    </div>
<script src="fluid.js"></script>
    <?php include('../include/footer.php'); ?>
</body>
</html>