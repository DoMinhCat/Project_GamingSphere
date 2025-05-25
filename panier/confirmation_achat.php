<?php
session_start();
$login_page = '../connexion/login.php';
require('../include/check_timeout.php');
require('../include/database.php');
require('../include/check_session.php');
require_once __DIR__ . '/../path.php';

$id_utilisateur = $_SESSION['user_id'];

$stmt = $bdd->prepare("
    SELECT j.nom, j.prix, j.image, b.date_achat 
    FROM boutique b
    JOIN jeu j ON b.id_jeu = j.id_jeu
    WHERE b.id_utilisateur = ? AND b.date_achat >= NOW() - INTERVAL 10 MINUTE
    ORDER BY b.date_achat DESC
");
$stmt->execute([$id_utilisateur]);
$achats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<?php
$title = 'Confirmation d\'Achat';
$pageCategory = 'panier';
echo "<script>const pageCategory = '$pageCategory';</script>";
require('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}
?>

<body>
    <?php include('../include/header.php'); ?>

    <div class="container my-5">
        <h1 class="text-center mb-4">🎉 Achat Confirmé !</h1>

        <?php if (count($achats) > 0): ?>
            <h4 class="text-center">Voici les jeux que vous venez d’acheter :</h4>
            <div class="row mt-3">
                <?php foreach ($achats as $jeu): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm h-100">
                            <?php if (!empty($jeu['image'])): ?>
                                <img src="/back-office/uploads/<?= htmlspecialchars($jeu['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($jeu['nom']) ?>" style="height: 250px; object-fit: cover;">
                            <?php else: ?>
                                <img src="/magasin/img/no_image2.png" class="card-img-top" alt="Jeu" style="height: 250px; object-fit: cover;">
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
            <p class="text-center text-muted">Aucun achat récent trouvé.</p>
        <?php endif; ?>

        <div class="text-center mt-4">
            <p>Merci pour votre achat ! Vos crédits ont été mis à jour avec succès.</p>
            <a href="<?= magasin_main ?>" class="btn btn-primary">Voir d'autres jeux</a>
            <a href="<?= my_account ?>" class="btn btn-secondary">Mon profil</a>
        </div>
    </div>
</body>
<?php include('../include/footer.php'); ?>

</html>