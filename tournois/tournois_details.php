<!-- Page dédiée à un tournois -->
<?php
session_start();
include('../include/database.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';


$this_page = tournois_details;

if (!isset($_GET['id_tournoi']) || empty($_GET['id_tournoi'])) {
    header('Location:' . tournois_main . '?message=missing_id');
    exit();
}

$id_tournoi = intval($_GET['id_tournoi']);

try {
    $stmt = $bdd->prepare("SELECT * FROM tournoi WHERE id_tournoi = ?");
    $stmt->execute([$id_tournoi]);
    $tournoi = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tournoi) {
        header('Location:' . tournois_main . '?message=tournoi_not_found');
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
$pageCategory = 'tournois';
echo "<script>const pageCategory = '$pageCategory';</script>";
include('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}
?>

<body>
    <?php include('../include/header.php'); ?>

    <div class="container my-5">
        <h1 class="mb-4 text-center">Détails du Tournoi</h1>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="card-title mb-3 text-center"><?= htmlspecialchars($tournoi['nom_tournoi']) ?></h2>

                <div class="row mb-2">
                    <div class="col-md-6">
                        <p class="card-text mb-1"><strong>Jeu :</strong> <?= htmlspecialchars($tournoi['jeu']) ?></p>
                        <p class="card-text mb-1"><strong>Date de Début :</strong> <?= htmlspecialchars(date('d/m/Y', strtotime($tournoi['date_debut']))) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p class="card-text mb-1"><strong>Date de Fin :</strong> <?= htmlspecialchars(date('d/m/Y', strtotime($tournoi['date_fin']))) ?></p>
                        <p class="card-text mb-1"><strong>Statut :</strong> <?= htmlspecialchars($tournoi['status_ENUM']) ?></p>
                    </div>
                </div>

                <p class="card-text mt-3"><?= nl2br(htmlspecialchars($tournoi['description'])) ?></p>
            </div>

            <div class="card-footer d-flex justify-content-center gap-2">
                <a href="<?= tournois_main ?>" class="btn btn-secondary">Retour à la liste</a>
                <?php if ($is_registered): ?>
                    <button class="btn btn-danger desinscrire-btn" data-id="<?= $tournoi['id_tournoi'] ?>">Se désinscrire</button>
                <?php else: ?>
                    <button class="btn btn-warning participer-btn" data-id="<?= $tournoi['id_tournoi'] ?>">Participer</button>
                <?php endif; ?>
            </div>
        </div>

    </div>
    <script src="/tournois/fluid.js"></script>
    <?php include('../include/footer.php'); ?>
</body>

</html>