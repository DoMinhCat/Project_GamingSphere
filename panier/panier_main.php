<?php
session_start();
require('../include/check_timeout.php');
require('../include/database.php');
require('../include/check_session.php');

$id_utilisateur = $_SESSION['user_id'];

$stmt = $bdd->prepare("SELECT p.id_jeu, j.nom, j.prix, j.image FROM panier p
                        JOIN jeu j ON p.id_jeu = j.id_jeu
                        WHERE p.id_utilisateur = ?");
$stmt->execute([$id_utilisateur]);
$panier = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmtCredits = $bdd->prepare("SELECT credits FROM credits WHERE user_id = ?");
$stmtCredits->execute([$id_utilisateur]);
$utilisateur = $stmtCredits->fetch(PDO::FETCH_ASSOC);

$credits = $utilisateur['credits'] ?? 0;
?>

<!DOCTYPE html>
<html lang="fr">

<?php
$title = 'Mon Panier';
include('../include/head.php');
?>

<body>
<?php include('../include/header.php'); ?>

<div class="container mt-4">
    <h1 class="text-center mb-4">Mon Panier</h1>

    <?php if (count($panier) > 0): ?>
    <div class="row">
        <?php
        $total = 0;
        foreach ($panier as $jeu) {
            $total += $jeu['prix'];
        ?>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <?php if (!empty($jeu['image'])): ?>
                    <img src="../back-office/uploads/<?= htmlspecialchars($jeu['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($jeu['nom']) ?>" style="height: 250px; object-fit: cover;">
                <?php else: ?>
                    <img src="../../assets/img/no_image.png" class="card-img-top" alt="Aucune image" style="height: 250px; object-fit: cover;">
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($jeu['nom']) ?></h5>
                    <p class="card-text"><strong>Prix :</strong> <?= htmlspecialchars($jeu['prix']) ?> €</p>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>

    <div class="text-center mt-4">
        <h4><strong>Total : <?= htmlspecialchars($total) ?> €</strong></h4>
        <p>Crédits disponibles : <?= htmlspecialchars((string) ($credits ?? '0')) ?> €</p>
        <?php if ($credits >= $total): ?>
            <a href="finaliser_achat.php" class="btn btn-success">Finaliser l'achat</a>
        <?php else: ?>
            <p class="text-danger">Vous n'avez pas assez de crédits pour finaliser cet achat.</p>
        <?php endif; ?>
    </div>
    <?php else: ?>
        <p class="text-center">Votre panier est vide.</p>
    <?php endif; ?>
</div>

</body>
<?php include('../include/footer.php'); ?>
</html>
