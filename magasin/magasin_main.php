<?php
session_start();
require('../include/check_timeout.php');
require('../include/database.php');
require_once __DIR__ . '/../path.php';
try {
    $stmt = $bdd->query("SELECT * FROM jeu ORDER BY RAND() LIMIT 3;");
    $carouselGames = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $meilleurs = $bdd->query("SELECT * FROM jeu ORDER BY note_jeu DESC LIMIT 3;");
    $games = $meilleurs->fetchAll(PDO::FETCH_ASSOC);

    $nouveaux = $bdd->query("SELECT * FROM jeu ORDER BY date_sortie DESC LIMIT 3;");
    $new_games = $nouveaux->fetchAll(PDO::FETCH_ASSOC);

    $gratuits = $bdd->query("SELECT * FROM jeu WHERE prix=0 LIMIT 3;");
    $free_games = $gratuits->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $bdd->query("SELECT * FROM jeu ORDER BY RAND() LIMIT 3;");
    $random_games = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException) {
    header('location:' . index_front . '?message=bdd');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<?php
$title = 'Magasin';
$pageCategory = 'magasin';
echo "<script>const pageCategory = '$pageCategory';</script>";
include('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}
?>

<body>
    <?php include('../include/header.php'); ?>

    <main class="mb-5">
        <?php if (isset($_GET['message']) && $_GET['message'] == 'bdd') { ?>
            <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
                Erreur de la base de données, veuillez reéssayer plus tard !
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } elseif (!empty($_GET['error'])) { ?>
            <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
                <?= htmlspecialchars($_GET['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <?php if (count($carouselGames) > 0): ?>
            <div class="d-flex justify-content-center mb-5">
                <div class="carousel-container mx-auto" style="width: 100%;">
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
                                        <img src="../back-office/uploads/<?= htmlspecialchars($game['image']) ?>" onclick="window.location.href='<?= magasin_game . '?id=' . $game['id_jeu'] ?>'" class="d-block w-100" alt="<?= htmlspecialchars($game['nom']) ?>" style="height: 400px; object-fit: contain;">
                                    <?php else: ?>
                                        <img src="/magasin/img/no_image2.png" class="d-block w-100" alt="Aucune image" onclick="window.location.href='<?= magasin_game . '?id=' . $game['id_jeu'] ?>'" style="height: 400px; object-fit: contain;">
                                    <?php endif; ?>
                                    <div class="carousel-caption d-none d-md-block">
                                        <h5><?= htmlspecialchars($game['nom']) ?></h5>
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

        <!-- Content all categories-->
        <div class="container mt-5">
            <!-- Random -->
            <div class="d-flex flex-column mb-5">
                <a href="<?= magasin_category . '?category=random' ?>" class="mb-3 category_news_title">
                    <h1>Découvrir un nouveau jeu</h1>
                </a>
                <div class="row">
                    <?php foreach ($random_games as $game): ?>
                        <div class="col-md-4 mb-4 d-flex align-items-stretch px-0">
                            <div class="card shadow-sm w-100 d-flex flex-column">
                                <?php if (!empty($game['image'])): ?>
                                    <img src="../back-office/uploads/<?= htmlspecialchars($game['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($game['nom']) ?>">
                                <?php else: ?>
                                    <img src="/magasin/img/no_image2.png" class="card-img-top" alt="Aucune image">
                                <?php endif; ?>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?= htmlspecialchars($game['nom']) ?></h5>
                                    <p class="card-text mb-2"><strong>Prix :</strong> <?= ($game['prix'] != 0 ? htmlspecialchars($game['prix']) . '€' : 'Gratuit') ?> </p>
                                    <p class="p-0"><strong>Note :</strong></p>
                                    <div class="d-flex align-items-center">
                                        <?php
                                        $note = (float)$game['note_jeu'];
                                        for ($i = 1; $i <= 5; $i++):
                                            echo '<i class="bi bi-star-fill text-warning"></i>';
                                        ?>
                                        <?php endfor; ?>
                                        <span class="ms-2 text-muted">(<?= $note ?>/5)</span>
                                    </div>
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
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- Meilleurs -->
            <div class="d-flex flex-column mb-5">
                <a href="<?= magasin_category . '?category=meilleur' ?>" class="mb-3 category_news_title">
                    <h1>Meilleurs jeux</h1>
                </a>
                <div class="row">
                    <?php foreach ($games as $game): ?>
                        <div class="col-md-4 mb-4 d-flex align-items-stretch px-0">
                            <div class="card shadow-sm w-100 d-flex flex-column">
                                <?php if (!empty($game['image'])): ?>
                                    <img src="../back-office/uploads/<?= htmlspecialchars($game['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($game['nom']) ?>">
                                <?php else: ?>
                                    <img src="/magasin/img/no_image2.png" class="card-img-top" alt="Aucune image">
                                <?php endif; ?>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?= htmlspecialchars($game['nom']) ?></h5>
                                    <p class="card-text mb-2"><strong>Prix :</strong> <?= ($game['prix'] != 0 ? htmlspecialchars($game['prix']) . '€' : 'Gratuit') ?> </p>
                                    <p class="p-0"><strong>Note :</strong></p>
                                    <div class="d-flex align-items-center">
                                        <?php
                                        $note = (float)$game['note_jeu'];
                                        for ($i = 1; $i <= 5; $i++):
                                            echo '<i class="bi bi-star-fill text-warning"></i>';
                                        ?>
                                        <?php endfor; ?>
                                        <span class="ms-2 text-muted">(<?= $note ?>/5)</span>
                                    </div>
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
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- Nouveaux -->
            <div class="d-flex flex-column mb-5">
                <a href="<?= magasin_category . '?category=nouveau' ?>" class="mb-3 category_news_title">
                    <h1>Nouveaux jeux</h1>
                </a>
                <div class="row">
                    <?php foreach ($new_games as $game): ?>
                        <div class="col-md-4 mb-4 d-flex align-items-stretch px-0">
                            <div class="card shadow-sm w-100 d-flex flex-column">
                                <?php if (!empty($game['image'])): ?>
                                    <img src="../back-office/uploads/<?= htmlspecialchars($game['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($game['nom']) ?>">
                                <?php else: ?>
                                    <img src="/magasin/img/no_image2.png" class="card-img-top" alt="Aucune image">
                                <?php endif; ?>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?= htmlspecialchars($game['nom']) ?></h5>
                                    <p class="card-text mb-2"><strong>Prix :</strong> <?= ($game['prix'] != 0 ? htmlspecialchars($game['prix']) . '€' : 'Gratuit') ?> </p>
                                    <p class="p-0"><strong>Note :</strong></p>
                                    <div class="d-flex align-items-center">
                                        <?php
                                        $note = (float)$game['note_jeu'];
                                        for ($i = 1; $i <= 5; $i++):
                                            echo '<i class="bi bi-star-fill text-warning"></i>';
                                        ?>
                                        <?php endfor; ?>
                                        <span class="ms-2 text-muted">(<?= $note ?>/5)</span>
                                    </div>
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
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- Gratuit -->
            <div class="d-flex flex-column mb-5">
                <a href="<?= magasin_category . '?category=gratuit' ?>" class="mb-3 category_news_title">
                    <h1>Jeux gratuits</h1>
                </a>
                <div class="row">
                    <?php foreach ($free_games as $game): ?>
                        <div class="col-md-4 mb-4 d-flex align-items-stretch px-0">
                            <div class="card shadow-sm w-100 d-flex flex-column">
                                <?php if (!empty($game['image'])): ?>
                                    <img src="../back-office/uploads/<?= htmlspecialchars($game['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($game['nom']) ?>">
                                <?php else: ?>
                                    <img src="/magasin/img/no_image2.png" class="card-img-top" alt="Aucune image">
                                <?php endif; ?>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?= htmlspecialchars($game['nom']) ?></h5>
                                    <p class="card-text mb-2"><strong>Prix :</strong> <?= ($game['prix'] != 0 ? htmlspecialchars($game['prix']) . '€' : 'Gratuit') ?> </p>
                                    <p class="p-0"><strong>Note :</strong></p>
                                    <div class="d-flex align-items-center">
                                        <?php
                                        $note = (float)$game['note_jeu'];
                                        for ($i = 1; $i <= 5; $i++):
                                            echo '<i class="bi bi-star-fill text-warning"></i>';
                                        ?>
                                        <?php endfor; ?>
                                        <span class="ms-2 text-muted">(<?= $note ?>/5)</span>
                                    </div>
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
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </main>
    <?php include('../include/footer.php'); ?>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll(".btn-add-to-cart").forEach(button => {
                button.addEventListener("click", async () => {
                    const gameId = button.getAttribute("data-id");

                    const response = await fetch(`../panier/add_to_cart.php?id=${gameId}`);
                    const data = await response.json();

                    const alertBox = document.createElement("div");
                    alertBox.className = `alert mt-3 text-center alert-${data.status === "success" ? "success" : "danger"}`;
                    alertBox.textContent = data.message;

                    const alertContainer = document.getElementById("alert-container");
                    alertContainer.appendChild(alertBox);

                    setTimeout(() => alertBox.remove(), 5000);

                    const panierCount = data.panierCount;
                    PHP.
                    updatePanierBadge(panierCount);
                });
            });
        });

        function updatePanierBadge(count) {
            const badge = document.querySelector(".panier-badge");
            if (badge) {
                if (count > 0) {
                    badge.textContent = count;
                    badge.style.display = 'inline';
                } else {
                    badge.style.display = 'none';
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

    .btn-magasin {
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
        object-fit: contain;
    }
</style>