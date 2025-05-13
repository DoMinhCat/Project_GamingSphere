<?php
session_start();
$login_page = '../connexion/login.php';
include('../include/database.php');
require_once __DIR__ . '/../path.php';
require('../include/check_session.php');
require('../include/check_timeout.php');

$user_id = intval($_SESSION['user_id']);
try {
    $stmt = $bdd->prepare("
    SELECT t.id_tournoi, t.nom_tournoi, t.date_debut, t.date_fin, t.jeu, t.status_ENUM, t.type
    FROM inscription_tournoi it
    INNER JOIN tournoi t ON it.id_tournoi = t.id_tournoi
    WHERE it.user_id = ?
    ORDER BY t.date_debut DESC
");
    $stmt->execute([$user_id]);
    $tournois = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Erreur lors de la récupération des tournois : " . htmlspecialchars($e->getMessage()) . "</div>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Mes Tournois';
$pageCategory = 'profil';
echo "<script>const pageCategory = '$pageCategory';</script>";
include('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}
?>

<body>
    <?php
    include('../include/header.php');
    include('navbar.php');
    ?>
    <div class="container my-5">
        <h1 class="mb-4 text-center">Mes Tournois</h1>
        <?php if (count($tournois) > 0): ?>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($tournois as $tournoi): ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($tournoi['nom_tournoi']) ?></h5>
                                <p class="card-text"><strong>Jeu :</strong> <?= htmlspecialchars($tournoi['jeu']) ?></p>
                                <p class="card-text"><strong>Type :</strong> <?= htmlspecialchars($tournoi['type']) ?></p>
                                <p class="card-text"><strong>Date de Début :</strong> <?= htmlspecialchars($tournoi['date_debut']) ?></p>
                                <p class="card-text"><strong>Date de Fin :</strong> <?= htmlspecialchars($tournoi['date_fin']) ?></p>
                                <p class="card-text"><strong>Statut :</strong> <?= htmlspecialchars($tournoi['status_ENUM']) ?></p>
                            </div>
                            <div class="card-footer text-center">
                                <a href="../tournois/tournois_details.php?id_tournoi=<?= $tournoi['id_tournoi'] ?>" class="btn btn-primary btn-sm">Voir les détails</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">Vous n'êtes inscrit à aucun tournoi pour le moment.</div>
        <?php endif; ?>
    </div>
    <?php include('../include/footer.php'); ?>
</body>

</html>