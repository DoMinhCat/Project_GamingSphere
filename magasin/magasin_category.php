<?php

use Stripe\Terminal\Location;

session_start();
require('../include/check_timeout.php');
require('../include/database.php');
require_once __DIR__ . '/../path.php';

if (!isset($_GET['category']) || empty($_GET['category'])) {
    header('location:' . magasin_main . '?error=' . urlencode('Aucune catégorie précise !'));
    exit;
}
$category = $_GET['category'];
$display_category = '';
$query = '';
switch ($category) {
    case 'meilleur':
        $query = "ORDER BY note_jeu DESC";
        $display_category = "Meilleurs jeux";
        break;
    case 'nouveau':
        $query = "ORDER BY date_sortie DESC";
        $display_category = "Nouveaux jeux";
        break;
    case 'gratuit':
        $query = "WHERE prix=0";
        $display_category = "Jeux gratuits";
        break;
    default:
        header('location:' . magasin_main . '?error=' . urlencode('Aucune catégorie précise !'));
        exit;
}
try {
    $stmt = $bdd->prepare("SELECT * FROM jeu " . $query . ";");
    $stmt->execute();
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException) {
    header('location:' . magasin_main . '?message=bdd');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Magasin - catégorie';
$pageCategory = 'magasin';
echo "<script>const pageCategory = '$pageCategory';</script>";
include('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}
?>

<body>
    <?php include('../include/header.php'); ?>
    <main class="container mt-2 mb-5">
        <?php
        if (!empty($_GET['message'])) { ?>
            <div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                <?= htmlspecialchars($_GET['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <!-- search box -->
        <div class="col d-flex justify-content-center text-center">
            <div class="d-flex col-md-6 pt-3 pb-2">
                <input type="text" id="search" class="form-control searchBoxFront" placeholder="Rechercher par nom du jeu">
            </div>
        </div>

        <div class="mb-4 mt-5">
            <a href="<?= magasin_main ?>" class="text-decoration-none fs-3 return_arrow d-flex align-items-center gap-2">
                <i class="bi bi-chevron-left"></i>
                <h1 class="m-0"><?= htmlspecialchars($display_category) ?></h1>
            </a>
        </div>

        <!-- jeux display -->
        <?php foreach ($games as $game) : ?>
            <div class="col-md-4 mb-4 d-flex align-items-stretch px-0">
                <div class="card shadow-sm w-100 d-flex flex-column">
                    <?php if (!empty($game['image'])): ?>
                        <img src="../back-office/uploads/<?= htmlspecialchars($game['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($game['nom']) ?>">
                    <?php else: ?>
                        <img src="/magasin/img/no_image2.png" class="card-img-top" alt="Aucune image">
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($game['nom']) ?></h5>
                        <p class="card-text mb-2"><strong>Prix :</strong> <?= htmlspecialchars($game['prix']) ?> €</p>
                        <div class="mt-auto d-flex flex-column flex-sm-row justify-content-between gap-2 align-items-stretch">
                            <a href="<?= magasin_game ?>?id=<?= $game['id_jeu'] ?>"
                                class="btn btn-magasin btn-outline-primary flex-fill mt-3 d-flex align-items-center justify-content-center text-center small">
                                Voir détails
                            </a>
                            <button class="btn btn-magasin btn-success mt-3 flex-fill btn-add-to-cart d-flex align-items-center justify-content-center text-center small"
                                data-id="<?= $game['id_jeu'] ?>">
                                Ajouter au panier
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach ?>


    </main>
    <?php include('../include/footer.php'); ?>
</body>

</html>