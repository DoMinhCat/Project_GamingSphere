<?php
session_start();
include('../include/database.php');

// Définissez la variable $this_page pour indiquer la page actuelle
$this_page = 'tournois_details.php';

// Vérifiez si un ID de tournoi est passé dans l'URL
if (!isset($_GET['id_tournoi']) || empty($_GET['id_tournoi'])) {
    header("Location: tournois_main.php?message=missing_id");
    exit();
}

$id_tournoi = intval($_GET['id_tournoi']); // Sécurisez l'ID

try {
    // Récupérez les informations du tournoi
    $stmt = $bdd->prepare("SELECT id_tournoi, nom_tournoi, date_debut, date_fin, jeu, status_ENUM FROM tournoi WHERE id_tournoi = ?");
    $stmt->execute([$id_tournoi]);
    $tournoi = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tournoi) {
        // Si le tournoi n'existe pas, redirigez avec un message d'erreur
        header("Location: tournois_main.php?message=tournoi_not_found");
        exit();
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
                <a href="tournois_main.php" class="btn btn-secondary">Participer</a>
            </div>
        </div>
    </div>

    <?php include('../include/footer.php'); ?>
</body>
</html>