<?php
session_start();
require('../include/check_timeout.php');
require('../include/database.php');
require('../include/check_session.php');

$id_utilisateur = $_SESSION['user_id'];

// Récupère l’historique complet des achats
$stmt = $bdd->prepare("
    SELECT j.nom, j.prix, j.image, b.date_achat 
    FROM boutique b
    JOIN jeu j ON b.id_jeu = j.id_jeu
    WHERE b.id_utilisateur = ?
    ORDER BY b.date_achat DESC
");
$stmt->execute([$id_utilisateur]);
$achats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Historique des Achats';
include('../include/head.php');
?>
<body>
<?php include('../include/header.php'); ?>

<div class="container mt-4">
    <h1 class="text-center mb-4">🧾 Historique de vos achats</h1>

    <?php if (count($achats) > 0): ?>
        <div class="row">
            <?php foreach ($achats as $jeu): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <?php if (!empty($jeu['image'])): ?>
                            <img src="../back-office/uploads/<?= htmlspecialchars($jeu['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($jeu['nom']) ?>" style="height: 250px; object-fit: cover;">
                        <?php else: ?>
                            <img src="../../assets/img/no_image.png" class="card-img-top" alt="Jeu" style="height: 250px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($jeu['nom']) ?></h5>
                            <p class="card-text"><strong>Prix :</strong> <?= htmlspecialchars($jeu['prix']) ?> €</p>
                            <p class="text-muted"><small>Acheté le : <?= date('d/m/Y à H:i:s', strtotime($jeu['date_achat'])) ?></small></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-center text-muted">Vous n'avez encore acheté aucun jeu.</p>
    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="../jeux/index.php" class="btn btn-primary">🕹️ Parcourir les jeux</a>
        <a href="../profil.php" class="btn btn-secondary">👤 Retour au profil</a>
    </div>
</div>

</body>
<?php include('../include/footer.php'); ?>
</html>
