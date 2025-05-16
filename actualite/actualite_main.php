<?php
session_start();
require('../include/check_timeout.php');
require('../include/database.php');
require_once __DIR__ . '/../path.php';
?>
<!DOCTYPE html>
<html lang="fr">

<?php
$title = 'Actualités';
$pageCategory = 'actualite';
echo "<script>const pageCategory = '$pageCategory';</script>";
require('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}

//recuper data
try {
    $query = "SELECT * FROM news ORDER BY date_article DESC";
    $stmt = $bdd->query($query);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    header('location:' . index_front . '?message=bdd');
    exit();
}
?>

<body>
    <?php include("../include/header.php"); ?>

    <main class="container my-5">
        <h1 class="text-center mb-5">Actualités</h1>

        <div class="d-flex flex-column mb-5">
            <a href="<?= actualite_categorie . '?category=' . urlencode('À la une') ?>" class="montserrat-titre32 mb-3" style="text-decoration:underline;
  color: black;">
                <h2>À la une</h2>
            </a>

            <!-- Background + Padding -->
            <div class="rounded box_category_news">
                <div class="row gx-2 p-2 rounded align-items-stretch">

                    <!-- LEFT CARD -->
                    <div class="col-md-7 d-flex">
                        <a href="" class="card w-100 card_news" style="text-decoration: none;">

                            <img src="abc" alt="img" class="card-img-top" style="max-height: 250px; object-fit: cover;">
                            <div class="card-body p-3">
                                <h4 class="card-title" style="text-decoration: underline;">Title</h4>
                                <p class="mb-0">Publié .. par ..</p>
                            </div>

                        </a>
                    </div>

                    <!-- RIGHT CARDS STACKED -->
                    <div class="col-md-5 d-flex flex-column h-100">
                        <a href="" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1">Title</h6>
                                <p class="mb-0">Publié .. par ..</p>
                            </div>
                        </a>

                        <a href="" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1">Title</h6>
                                <p class="mb-0">Publié .. par ..</p>
                            </div>
                        </a>
                        <a href="" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1">Title</h6>
                                <p class="mb-0">Publié .. par ..</p>
                            </div>
                        </a>
                        <a href="" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1">Title</h6>
                                <p class="mb-0">Publié .. par ..</p>
                            </div>
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <div class="d-flex flex-column mb-5">
            <a href="<?= actualite_categorie . '?category=' . urlencode('Esport') ?>" class="montserrat-titre32 mb-3" style="text-decoration:underline;
  color: black;">
                <h2>Esport</h2>
            </a>

            <!-- Background + Padding -->
            <div class="rounded box_category_news">
                <div class="row gx-2 p-2 rounded align-items-stretch">

                    <!-- LEFT CARD -->
                    <div class="col-md-7 d-flex">
                        <a href="" class="card w-100 card_news" style="text-decoration: none;">

                            <img src="abc" alt="img" class="card-img-top" style="max-height: 250px; object-fit: cover;">
                            <div class="card-body p-3">
                                <h4 class="card-title" style="text-decoration: underline;">Title</h4>
                                <p class="mb-0">Publié .. par ..</p>
                            </div>

                        </a>
                    </div>

                    <!-- RIGHT CARDS STACKED -->
                    <div class="col-md-5 d-flex flex-column h-100">
                        <a href="" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1">Title</h6>
                                <p class="mb-0">Publié .. par ..</p>
                            </div>
                        </a>

                        <a href="" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1">Title</h6>
                                <p class="mb-0">Publié .. par ..</p>
                            </div>
                        </a>
                        <a href="" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1">Title</h6>
                                <p class="mb-0">Publié .. par ..</p>
                            </div>
                        </a>
                        <a href="" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1">Title</h6>
                                <p class="mb-0">Publié .. par ..</p>
                            </div>
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <div class="d-flex flex-column mb-5">
            <a href="<?= actualite_categorie . '?category=' . urlencode('Évenèment') ?>" class="montserrat-titre32 mb-3" style="text-decoration:underline;
  color: black;">
                <h2>Évenèment</h2>
            </a>

            <!-- Background + Padding -->
            <div class="rounded box_category_news">
                <div class="row gx-2 p-2 rounded align-items-stretch">

                    <!-- LEFT CARD -->
                    <div class="col-md-7 d-flex">
                        <a href="" class="card w-100 card_news" style="text-decoration: none;">

                            <img src="abc" alt="img" class="card-img-top" style="max-height: 250px; object-fit: cover;">
                            <div class="card-body p-3">
                                <h4 class="card-title" style="text-decoration: underline;">Title</h4>
                                <p class="mb-0">Publié .. par ..</p>
                            </div>

                        </a>
                    </div>

                    <!-- RIGHT CARDS STACKED -->
                    <div class="col-md-5 d-flex flex-column h-100">
                        <a href="" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1">Title</h6>
                                <p class="mb-0">Publié .. par ..</p>
                            </div>
                        </a>

                        <a href="" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1">Title</h6>
                                <p class="mb-0">Publié .. par ..</p>
                            </div>
                        </a>
                        <a href="" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1">Title</h6>
                                <p class="mb-0">Publié .. par ..</p>
                            </div>
                        </a>
                        <a href="" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1">Title</h6>
                                <p class="mb-0">Publié .. par ..</p>
                            </div>
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <div class="d-flex flex-column mb-5">
            <a href="<?= actualite_categorie . '?category=' . urlencode('Général') ?>" class="montserrat-titre32 mb-3" style="text-decoration:underline;
  color: black;">
                <h2>Général</h2>
            </a>

            <!-- Background + Padding -->
            <div class="rounded box_category_news">
                <div class="row gx-2 p-2 rounded align-items-stretch">

                    <!-- LEFT CARD -->
                    <div class="col-md-7 d-flex">
                        <a href="" class="card w-100 card_news" style="text-decoration: none;">

                            <img src="abc" alt="img" class="card-img-top" style="max-height: 250px; object-fit: cover;">
                            <div class="card-body p-3">
                                <h4 class="card-title" style="text-decoration: underline;">Title</h4>
                                <p class="mb-0">Publié .. par ..</p>
                            </div>

                        </a>
                    </div>

                    <!-- RIGHT CARDS STACKED -->
                    <div class="col-md-5 d-flex flex-column h-100">
                        <a href="" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1">Title</h6>
                                <p class="mb-0">Publié .. par ..</p>
                            </div>
                        </a>

                        <a href="" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1">Title</h6>
                                <p class="mb-0">Publié .. par ..</p>
                            </div>
                        </a>
                        <a href="" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1">Title</h6>
                                <p class="mb-0">Publié .. par ..</p>
                            </div>
                        </a>
                        <a href="" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1">Title</h6>
                                <p class="mb-0">Publié .. par ..</p>
                            </div>
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <div class="d-flex flex-column mb-5">
            <a href="<?= actualite_categorie . '?category=' . urlencode('Critique') ?>" class="montserrat-titre32 mb-3" style="text-decoration:underline;
  color: black;">
                <h2>Critique</h2>
            </a>

            <!-- Background + Padding -->
            <div class="rounded box_category_news">
                <div class="row gx-2 p-2 rounded align-items-stretch">

                    <!-- LEFT CARD -->
                    <div class="col-md-7 d-flex">
                        <a href="" class="card w-100 card_news" style="text-decoration: none;">

                            <img src="abc" alt="img" class="card-img-top" style="max-height: 250px; object-fit: cover;">
                            <div class="card-body p-3">
                                <h4 class="card-title" style="text-decoration: underline;">Title</h4>
                                <p class="mb-0">Publié .. par ..</p>
                            </div>

                        </a>
                    </div>

                    <!-- RIGHT CARDS STACKED -->
                    <div class="col-md-5 d-flex flex-column h-100">
                        <a href="" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1">Title</h6>
                                <p class="mb-0">Publié .. par ..</p>
                            </div>
                        </a>

                        <a href="" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1">Title</h6>
                                <p class="mb-0">Publié .. par ..</p>
                            </div>
                        </a>
                        <a href="" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1">Title</h6>
                                <p class="mb-0">Publié .. par ..</p>
                            </div>
                        </a>
                        <a href="" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1">Title</h6>
                                <p class="mb-0">Publié .. par ..</p>
                            </div>
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <div class="d-flex flex-column mb-5">
            <a href="<?= actualite_categorie . '?category=' . urlencode('Mise à jour') ?>" class="montserrat-titre32 mb-3" style="text-decoration:underline;
  color: black;">
                <h2>Mise à jour</h2>
            </a>

            <!-- Background + Padding -->
            <div class="rounded box_category_news">
                <div class="row gx-2 p-2 rounded align-items-stretch">

                    <!-- LEFT CARD -->
                    <div class="col-md-7 d-flex">
                        <a href="" class="card w-100 card_news" style="text-decoration: none;">

                            <img src="abc" alt="img" class="card-img-top" style="max-height: 250px; object-fit: cover;">
                            <div class="card-body p-3">
                                <h4 class="card-title" style="text-decoration: underline;">Title</h4>
                                <p class="mb-0">Publié .. par ..</p>
                            </div>

                        </a>
                    </div>

                    <!-- RIGHT CARDS STACKED -->
                    <div class="col-md-5 d-flex flex-column h-100">
                        <a href="" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1">Title</h6>
                                <p class="mb-0">Publié .. par ..</p>
                            </div>
                        </a>

                        <a href="" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1">Title</h6>
                                <p class="mb-0">Publié .. par ..</p>
                            </div>
                        </a>
                        <a href="" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1">Title</h6>
                                <p class="mb-0">Publié .. par ..</p>
                            </div>
                        </a>
                        <a href="" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1">Title</h6>
                                <p class="mb-0">Publié .. par ..</p>
                            </div>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <?php include("../include/footer.php"); ?>
</body>

</html>

<!-- format news -->
<a href="actualite_article.php?id=<?= $row['id_news'] ?>" class="articleBlockLink text-dark mb-4">
    <div class="article border rounded p-3 mb-4 shadow-sm">
        <h2>
            <?= htmlspecialchars($row['titre']) ?>
        </h2>
        <p><strong>Publié le :</strong> <?= htmlspecialchars($row['date_article']) ?></p>
        <p><?= 'auteur ici' ?></p>
    </div>
</a>