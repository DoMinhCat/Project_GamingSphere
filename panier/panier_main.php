<?php
session_start();
$login_page = '../connexion/login.php';
require('../include/check_timeout.php');
require('../include/database.php');
require('../include/check_session.php');
require_once __DIR__ . '/../path.php';

$id_utilisateur = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_id'])) {
    $removeId = intval($_POST['remove_id']);

    $stmt = $bdd->prepare("DELETE FROM panier WHERE id_utilisateur = ? AND id_jeu = ?");
    $stmt->execute([$_SESSION['user_id'], $removeId]);
    header('Location: panier_main.php');
    exit;
}
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
$pageCategory = 'panier';
echo "<script>const pageCategory = '$pageCategory';</script>";
include('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}
?>

<body>
    <?php include('../include/header.php'); ?>

    <div class="container my-5">
        <h1 class="text-center mb-5">Mon Panier</h1>

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
                                <img src="/magasin/img/no_image.png" class="card-img-top" alt="Aucune image" style="height: 250px; object-fit: cover;">
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">

                                <h5 class="card-title"><?= htmlspecialchars($jeu['nom']) ?></h5>
                                <p class="card-text"><strong>Prix :</strong> <?= htmlspecialchars($jeu['prix']) ?> €</p>
                                <div class="d-flex flex-row justify-content-between">
                                    <a href="<?= magasin_game ?>?id=<?= $jeu['id_jeu'] ?>" class="btn btn-magasin btn-outline-primary w-50 mt-3 h-50">Voir détails</a>
                                    <form method="post" class="mt-auto">
                                        <input type="hidden" name="remove_id" value="<?= $jeu['id_jeu'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm mt-2">Retirer</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <div class="text-center mt-4">
                <h4><strong>Total : <?= htmlspecialchars($total) ?> €</strong></h4>
                <p>Crédits disponibles : <?= htmlspecialchars((string) ($credits ?? '0')) ?> €</p>
                <?php if ($credits >= $total): ?>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                        Finaliser l'achat
                    </button>

                    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Confirmation d'achat</h1>

                                </div>
                                <div class="modal-body text-start">
                                    Veuillez confirmer votre achat, n'oubliez pas de vérifier les informations de votre achat !
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">Annuler</button>
                                    <button type="button" class="btn btn-danger">Confirmer</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-danger">Vous n'avez pas assez de crédits pour finaliser cet achat.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p class="text-center mb-2">Votre panier est vide.</p>
            <a href="<?= magasin_main ?>" class="btn btn-primary text-center">Voir nos jeux</a>
        <?php endif; ?>
    </div>
</body>
<?php include('../include/footer.php'); ?>

</html>