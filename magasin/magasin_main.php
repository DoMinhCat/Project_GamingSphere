<?php
session_start();
require('../include/check_timeout.php');
require('../include/database.php');
require_once __DIR__ . '/../path.php';

$stmt = $bdd->query("SELECT id_jeu, nom, prix, image FROM jeu LIMIT 3");
$carouselGames = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmtAllGames = $bdd->query("SELECT id_jeu, nom, prix, image FROM jeu");
$games = $stmtAllGames->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<?php
$title = 'Boutique';
include('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}
?>

<body>
    <?php include('../include/header.php'); ?>

    <div class="container mt-4">
        <h1 class="text-center mb-4">Boutique de jeux</h1>

        <!-- Carousel Auto-déroulant Centré -->
        <?php if (count($carouselGames) > 0): ?>
            <div class="d-flex justify-content-center mb-5">
                <div class="carousel-container mx-auto" style="max-width: 800px; width: 100%; padding: 0; position: relative;">
                    <div id="gameCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
                        <div class="carousel-indicators">
                            <?php foreach ($carouselGames as $index => $game): ?>
                                <button type="button" data-bs-target="#gameCarousel" data-bs-slide-to="<?= $index ?>" class="<?= $index === 0 ? 'active' : '' ?>" aria-current="<?= $index === 0 ? 'true' : 'false' ?>" aria-label="Slide <?= $index + 1 ?>"></button>
                            <?php endforeach; ?>
                        </div>
                        <div class="carousel-inner">
                            <?php foreach ($carouselGames as $index => $game): ?>
                                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                    <?php if (!empty($game['image'])): ?>
                                        <img src="../back-office/uploads/<?= htmlspecialchars($game['image']) ?>" class="d-block w-100" alt="<?= htmlspecialchars($game['nom']) ?>" style="height: 400px; object-fit: cover;">
                                    <?php else: ?>
                                        <img src="../../assets/img/no_image.png" class="d-block w-100" alt="Aucune image" style="height: 400px; object-fit: cover;">
                                    <?php endif; ?>
                                    <div class="carousel-caption d-none d-md-block">
                                        <h5><?= htmlspecialchars($game['nom']) ?></h5>
                                        <p><strong>Prix :</strong> <?= htmlspecialchars($game['prix']) ?> €</p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#gameCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Précédent</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#gameCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Suivant</span>
                        </button>
                    </div>

                    <!-- Arrière-plan défilant -->
                    <div class="carousel-background" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;">
                        <div class="carousel-bg-content">
                            <?php foreach ($games as $game): ?>
                                <div class="carousel-bg-item" style="position: absolute; width: 100%; height: 100%; background-image: url('../back-office/uploads/<?= htmlspecialchars($game['image']) ?>'); background-size: cover; background-position: center; opacity: 0.5;"></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <h2 class="mb-3 text-center">Tous les jeux</h2>
        <div class="row">
            <?php
            foreach ($games as $game) {
            ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <?php if (!empty($game['image'])): ?>
                            <img src="../back-office/uploads/<?= htmlspecialchars($game['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($game['nom']) ?>" style="height: 250px; object-fit: cover;">
                        <?php else: ?>
                            <img src="../../assets/img/no_image.png" class="card-img-top" alt="Aucune image" style="height: 250px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($game['nom']) ?></h5>
                            <p class="card-text"><strong>Prix :</strong> <?= htmlspecialchars($game['prix']) ?> €</p>
                            <a href="buy_game.php?id=<?= $game['id_jeu'] ?>" class="btn btn-success mt-auto">Acheter</a>
                        </div>
                    </div>
                </div>
            <?php }
            ?>
        </div>
    </div>

</body>

</html>

<style>
    /* Supprimer toute marge externe indésirable */
    .carousel-container {
        padding: 0 !important;
        /* Enlever le padding */
        margin: 0 !important;
        /* Enlever la marge */
        position: relative;
    }

    /* Centrer le carousel */
    #gameCarousel {
        width: 100%;
        max-width: 800px;
        /* Limiter la largeur */
        margin: 0 auto;
        /* Centrer horizontalement */
    }

    /* Centrer le contenu du carousel */
    .carousel-inner {
        text-align: center;
    }

    /* Centrer le texte des captions */
    .carousel-caption {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
    }

    /* Centrer les titres */
    h1,
    h2 {
        text-align: center;
    }

    /* Ajustements pour les images dans la grille */
    .card-img-top {
        height: 250px;
        object-fit: cover;
    }

    /* Animation de défilement des jeux en arrière-plan */
    .carousel-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        overflow: hidden;
    }

    .carousel-bg-content {
        display: flex;
        animation: slideBackground 15s linear infinite;
    }

    .carousel-bg-item {
        width: 100%;
        height: 100%;
        opacity: 0.5;
        background-size: cover;
        background-position: center;
    }

    /* Définir l'animation de défilement */
    @keyframes slideBackground {
        0% {
            transform: translateX(0);
        }

        100% {
            transform: translateX(-100%);
        }
    }
</style>