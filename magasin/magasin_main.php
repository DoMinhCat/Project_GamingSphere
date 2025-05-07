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

        <?php if (count($carouselGames) > 0): ?>
            <div class="d-flex justify-content-center mb-5">
                <div class="carousel-container mx-auto" style="max-width: 800px;">
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
                </div>
            </div>
        <?php endif; ?>

        <h2 class="mb-3 text-center">Tous les jeux</h2>
        <div id="alert-container"></div>
        <div class="row">
            <?php foreach ($games as $game): ?>
                <div class="col-md-4 mb-4 d-flex align-items-stretch">
                    <div class="card shadow-sm w-100 d-flex flex-column">
                        <?php if (!empty($game['image'])): ?>
                            <img src="../back-office/uploads/<?= htmlspecialchars($game['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($game['nom']) ?>">
                        <?php else: ?>
                            <img src="../../assets/img/no_image.png" class="card-img-top" alt="Aucune image">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($game['nom']) ?></h5>
                            <p class="card-text"><strong>Prix :</strong> <?= htmlspecialchars($game['prix']) ?> €</p>
                            <div class="mt-auto d-flex justify-content-between gap-2 align-items-center">
                                <a href="<?= magasin_game ?>?id=<?= $game['id_jeu'] ?>" class="btn btn-outline-primary w-50 mt-3">Voir détails</a>
                                <button class="btn btn-success mt-3 btn-add-to-cart" data-id="<?= $game['id_jeu'] ?>">Ajouter au panier</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll(".btn-add-to-cart").forEach(button => {
                button.addEventListener("click", async () => {
                    const gameId = button.getAttribute("data-id");

                    const response = await fetch(`../panier/add_to_cart.php?id=${gameId}`);
                    const data = await response.json();

                    // Créer une alerte
                    const alertBox = document.createElement("div");
                    alertBox.className = `alert mt-3 text-center alert-${data.status === "success" ? "success" : "danger"}`;
                    alertBox.textContent = data.message;

                    // Ajouter l'alerte au conteneur d'alertes
                    const alertContainer = document.getElementById("alert-container");
                    alertContainer.appendChild(alertBox);

                    // Supprimer l'alerte après 5 secondes
                    setTimeout(() => alertBox.remove(), 5000);

                    // Mettre à jour le badge du panier
                    const panierCount = data.panierCount; // Assurez-vous que vous envoyez ce champ depuis le PHP.
                    updatePanierBadge(panierCount);
                });
            });
        });

        function updatePanierBadge(count) {
            const badge = document.querySelector(".panier-badge");
            if (badge) {
                if (count > 0) {
                    badge.textContent = count;
                    badge.style.display = 'inline'; // Afficher le badge si il y a des articles
                } else {
                    badge.style.display = 'none'; // Cacher le badge si le panier est vide
                }
            }
        }
    </script>
</body>

</html>

<style>
    .card {
        border-radius: 1rem;
        overflow: hidden;
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        display: flex;
        flex-direction: column;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
    }

    .card-body {
        padding: 1.2rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .card-text {
        margin-bottom: auto;
    }

    .btn {
        font-weight: 500;
    }

    .carousel-container {
        padding: 0 !important;
        margin: 0 auto;
        position: relative;
    }

    .carousel-inner {
        text-align: center;
    }

    .carousel-caption {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
    }

    .card-img-top {
        height: 250px;
        object-fit: cover;
    }
</style>