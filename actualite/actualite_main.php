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

?>

<body>
    <?php include("../include/header.php"); ?>

    <main class="container my-5">
        <h1 class="text-center mb-5">Actualités</h1>
        <!-- A la une -->
        <?php
        try {
            $stmt = $bdd->prepare("SELECT id_news, titre, date_article, category,pseudo FROM news join utilisateurs on id_utilisateurs=news.auteur where news.category='À la une' order by rand() limit 5;");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $nb_row = $stmt->rowCount();


            if ($nb_row < 1) {
                $row_take = 0;
            } elseif ($nb_row < 2) {
                $row_take = 1;
            } elseif ($nb_row < 4) {
                $row_take = 2;
            } else {
                $row_take = 4;
            }

            if ($nb_row != 5) {
                $stmt = $bdd->prepare("SELECT id_news, titre, date_article, category,pseudo FROM news join utilisateurs on id_utilisateurs=news.auteur where news.category='À la une' order by rand() limit" . $row_take . ";");
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException) {
            header('location:' . index_front . '?message=bdd');
            exit();
        }
        // 5 items case
        if ($nb_row == 5) {
            $big_article = $rows[0];
            $small_articles = array_slice($rows, 1);
        ?>
            <div class="d-flex flex-column mb-5">
                <a href="<?= actualite_categorie . '?category=' . urlencode('À la une') ?>" class="mb-3 category_news_title">
                    <h2>À la une</h2>
                </a>

                <!-- Background + Padding -->
                <div class="rounded box_category_news">
                    <div class="row gx-2 p-2 rounded align-items-stretch">

                        <!-- LEFT CARD -->
                        <div class="col-md-7 d-flex">
                            <a href="actualite_article.php?id=<?= $big_article['id_news'] ?>" class="card w-100 card_news" style="text-decoration: none;">

                                <img src="lienDeImage.jpg" alt="Image de l'article" class="card-img-top" style="max-height: 250px; object-fit: cover;">
                                <div class="card-body p-3">
                                    <h4 class="card-title" style="text-decoration: underline;"><?= $big_article['titre'] ?></h4>
                                    <p class="mb-1"><strong><?= $big_article['pseudo'] ?></strong></p>
                                    <p class="mb-0"><?= $big_article['date_article'] ?></p>
                                </div>

                            </a>
                        </div>

                        <!-- RIGHT CARDS STACKED -->
                        <div class="col-md-5 d-flex flex-column h-100">
                            <?php foreach ($small_articles as $small_article) : ?>
                                <a href="actualite_article.php?id=<?= $small_article['id_news'] ?>" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                                    <div class="card-body p-2">
                                        <h6 class="card-title mb-1"><?= $small_article['titre'] ?></h6>
                                        <p class="mb-0"><strong><?= $small_article['pseudo'] . ' a publié le ' ?></strong><?= $small_article['date_article'] ?>
                                        </p>
                                    </div>
                                </a>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } elseif ($row_take == 4 || $row_take == 2) { ?>
            <div class="rounded box_category_news">
                <div class="article-container p-3">

                    <?php foreach ($rows as $row) : ?>
                        <a href="actualite_article.php?id=<?= $row['id_news'] ?>" class="articleBlockLink text-dark">
                            <div class="article border rounded px-3 py-2 mb-2 shadow-sm">
                                <h2>
                                    <?= htmlspecialchars($row['titre']) ?>
                                </h2>
                                <p class="mb-1">
                                    <strong><?= htmlspecialchars($row['pseudo']) ?></strong>
                                </p>
                                <p class="mb-0"><?= htmlspecialchars($row['date_article']) ?></p>
                            </div>
                        </a>
                    <?php endforeach ?>
                </div>
            </div>
        <?php } elseif ($row_take == 0) { ?>
            <div class="rounded box_category_news">
                <div class="article-container p-3">
                    <p class="text-center m-0">Aucun article de cette catégorie en ce moment.</p>
                </div>
            </div>
        <?php } ?>



        <!-- Esport -->
        <?php
        try {
            $stmt = $bdd->prepare("SELECT id_news, titre, date_article, category,pseudo FROM news join utilisateurs on id_utilisateurs=news.auteur where news.category='Esport' order by rand() limit 5;");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $nb_row = $stmt->rowCount();


            if ($nb_row < 1) {
                $row_take = 0;
            } elseif ($nb_row < 2) {
                $row_take = 1;
            } elseif ($nb_row < 4) {
                $row_take = 2;
            } else {
                $row_take = 4;
            }

            if ($nb_row != 5) {
                $stmt = $bdd->prepare("SELECT id_news, titre, date_article, category,pseudo FROM news join utilisateurs on id_utilisateurs=news.auteur where news.category='Esport' order by rand() limit" . $row_take . ";");
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException) {
            header('location:' . index_front . '?message=bdd');
            exit();
        }
        // 5 items case
        if ($nb_row == 5) {
            $big_article = $rows[0];
            $small_articles = array_slice($rows, 1);
        ?>
            <div class="d-flex flex-column mb-5">
                <a href="<?= actualite_categorie . '?category=' . urlencode('Esport') ?>" class="mb-3 category_news_title">
                    <h2>Esport</h2>
                </a>

                <!-- Background + Padding -->
                <div class="rounded box_category_news">
                    <div class="row gx-2 p-2 rounded align-items-stretch">

                        <!-- LEFT CARD -->
                        <div class="col-md-7 d-flex">
                            <a href="actualite_article.php?id=<?= $big_article['id_news'] ?>" class="card w-100 card_news" style="text-decoration: none;">

                                <img src="lienDeImage.jpg" alt="Image de l'article" class="card-img-top" style="max-height: 250px; object-fit: cover;">
                                <div class="card-body p-3">
                                    <h4 class="card-title" style="text-decoration: underline;"><?= $big_article['titre'] ?></h4>
                                    <p class="mb-1"><strong><?= $big_article['pseudo'] ?></strong></p>
                                    <p class="mb-0"><?= $big_article['date_article'] ?></p>
                                </div>

                            </a>
                        </div>

                        <!-- RIGHT CARDS STACKED -->
                        <div class="col-md-5 d-flex flex-column h-100">
                            <?php foreach ($small_articles as $small_article) : ?>
                                <a href="actualite_article.php?id=<?= $small_article['id_news'] ?>" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                                    <div class="card-body p-2">
                                        <h6 class="card-title mb-1"><?= $small_article['titre'] ?></h6>
                                        <p class="mb-0"><strong><?= $small_article['pseudo'] . ' a publié le ' ?></strong><?= $small_article['date_article'] ?>
                                        </p>
                                    </div>
                                </a>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } elseif ($row_take == 4 || $row_take == 2) { ?>
            <div class="rounded box_category_news">
                <div class="article-container p-3">

                    <?php foreach ($rows as $row) : ?>
                        <a href="actualite_article.php?id=<?= $row['id_news'] ?>" class="articleBlockLink text-dark">
                            <div class="article border rounded px-3 py-2 mb-2 shadow-sm">
                                <h2>
                                    <?= htmlspecialchars($row['titre']) ?>
                                </h2>
                                <p class="mb-1">
                                    <strong><?= htmlspecialchars($row['pseudo']) ?></strong>
                                </p>
                                <p class="mb-0"><?= htmlspecialchars($row['date_article']) ?></p>
                            </div>
                        </a>
                    <?php endforeach ?>
                </div>
            </div>
        <?php } elseif ($row_take == 0) { ?>
            <div class="rounded box_category_news">
                <div class="article-container p-3">
                    <p class="text-center m-0">Aucun article de cette catégorie en ce moment.</p>
                </div>
            </div>
        <?php } ?>


        <!-- event -->
        <?php
        try {
            $stmt = $bdd->prepare("SELECT id_news, titre, date_article, category,pseudo FROM news join utilisateurs on id_utilisateurs=news.auteur where news.category='Évènement' order by rand() limit 5;");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $nb_row = $stmt->rowCount();


            if ($nb_row < 1) {
                $row_take = 0;
            } elseif ($nb_row < 2) {
                $row_take = 1;
            } elseif ($nb_row < 4) {
                $row_take = 2;
            } else {
                $row_take = 4;
            }

            if ($nb_row != 5) {
                $stmt = $bdd->prepare("SELECT id_news, titre, date_article, category,pseudo FROM news join utilisateurs on id_utilisateurs=news.auteur where news.category='Évènement' order by rand() limit" . $row_take . ";");
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException) {
            header('location:' . index_front . '?message=bdd');
            exit();
        }
        // 5 items case
        if ($nb_row == 5) {
            $big_article = $rows[0];
            $small_articles = array_slice($rows, 1);
        ?>
            <div class="d-flex flex-column mb-5">
                <a href="<?= actualite_categorie . '?category=' . urlencode('Évènement') ?>" class="mb-3 category_news_title">
                    <h2>Évènement</h2>
                </a>

                <!-- Background + Padding -->
                <div class="rounded box_category_news">
                    <div class="row gx-2 p-2 rounded align-items-stretch">

                        <!-- LEFT CARD -->
                        <div class="col-md-7 d-flex">
                            <a href="actualite_article.php?id=<?= $big_article['id_news'] ?>" class="card w-100 card_news" style="text-decoration: none;">

                                <img src="lienDeImage.jpg" alt="Image de l'article" class="card-img-top" style="max-height: 250px; object-fit: cover;">
                                <div class="card-body p-3">
                                    <h4 class="card-title" style="text-decoration: underline;"><?= $big_article['titre'] ?></h4>
                                    <p class="mb-1"><strong><?= $big_article['pseudo'] ?></strong></p>
                                    <p class="mb-0"><?= $big_article['date_article'] ?></p>
                                </div>

                            </a>
                        </div>

                        <!-- RIGHT CARDS STACKED -->
                        <div class="col-md-5 d-flex flex-column h-100">
                            <?php foreach ($small_articles as $small_article) : ?>
                                <a href="actualite_article.php?id=<?= $small_article['id_news'] ?>" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                                    <div class="card-body p-2">
                                        <h6 class="card-title mb-1"><?= $small_article['titre'] ?></h6>
                                        <p class="mb-0"><strong><?= $small_article['pseudo'] . ' a publié le ' ?></strong><?= $small_article['date_article'] ?>
                                        </p>
                                    </div>
                                </a>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } elseif ($row_take == 4 || $row_take == 2) { ?>
            <div class="rounded box_category_news">
                <div class="article-container p-3">

                    <?php foreach ($rows as $row) : ?>
                        <a href="actualite_article.php?id=<?= $row['id_news'] ?>" class="articleBlockLink text-dark">
                            <div class="article border rounded px-3 py-2 mb-2 shadow-sm">
                                <h2>
                                    <?= htmlspecialchars($row['titre']) ?>
                                </h2>
                                <p class="mb-1">
                                    <strong><?= htmlspecialchars($row['pseudo']) ?></strong>
                                </p>
                                <p class="mb-0"><?= htmlspecialchars($row['date_article']) ?></p>
                            </div>
                        </a>
                    <?php endforeach ?>
                </div>
            </div>
        <?php } elseif ($row_take == 0) { ?>
            <div class="rounded box_category_news">
                <div class="article-container p-3">
                    <p class="text-center m-0">Aucun article de cette catégorie en ce moment.</p>
                </div>
            </div>
        <?php } ?>


        <!-- general -->
        <?php
        try {
            $stmt = $bdd->prepare("SELECT id_news, titre, date_article, category,pseudo FROM news join utilisateurs on id_utilisateurs=news.auteur where news.category='Général' order by rand() limit 5;");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $nb_row = $stmt->rowCount();


            if ($nb_row < 1) {
                $row_take = 0;
            } elseif ($nb_row < 2) {
                $row_take = 1;
            } elseif ($nb_row < 4) {
                $row_take = 2;
            } else {
                $row_take = 4;
            }

            if ($nb_row != 5) {
                $stmt = $bdd->prepare("SELECT id_news, titre, date_article, category,pseudo FROM news join utilisateurs on id_utilisateurs=news.auteur where news.category='Général' order by rand() limit" . $row_take . ";");
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException) {
            header('location:' . index_front . '?message=bdd');
            exit();
        }
        // 5 items case
        if ($nb_row == 5) {
            $big_article = $rows[0];
            $small_articles = array_slice($rows, 1);
        ?>
            <div class="d-flex flex-column mb-5">
                <a href="<?= actualite_categorie . '?category=' . urlencode('Général') ?>" class="mb-3 category_news_title">
                    <h2>Général</h2>
                </a>

                <!-- Background + Padding -->
                <div class="rounded box_category_news">
                    <div class="row gx-2 p-2 rounded align-items-stretch">

                        <!-- LEFT CARD -->
                        <div class="col-md-7 d-flex">
                            <a href="actualite_article.php?id=<?= $big_article['id_news'] ?>" class="card w-100 card_news" style="text-decoration: none;">

                                <img src="lienDeImage.jpg" alt="Image de l'article" class="card-img-top" style="max-height: 250px; object-fit: cover;">
                                <div class="card-body p-3">
                                    <h4 class="card-title" style="text-decoration: underline;"><?= $big_article['titre'] ?></h4>
                                    <p class="mb-1"><strong><?= $big_article['pseudo'] ?></strong></p>
                                    <p class="mb-0"><?= $big_article['date_article'] ?></p>
                                </div>

                            </a>
                        </div>

                        <!-- RIGHT CARDS STACKED -->
                        <div class="col-md-5 d-flex flex-column h-100">
                            <?php foreach ($small_articles as $small_article) : ?>
                                <a href="actualite_article.php?id=<?= $small_article['id_news'] ?>" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                                    <div class="card-body p-2">
                                        <h6 class="card-title mb-1"><?= $small_article['titre'] ?></h6>
                                        <p class="mb-0"><strong><?= $small_article['pseudo'] . ' a publié le ' ?></strong><?= $small_article['date_article'] ?>
                                        </p>
                                    </div>
                                </a>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } elseif ($row_take == 4 || $row_take == 2) { ?>
            <div class="rounded box_category_news">
                <div class="article-container p-3">

                    <?php foreach ($rows as $row) : ?>
                        <a href="actualite_article.php?id=<?= $row['id_news'] ?>" class="articleBlockLink text-dark">
                            <div class="article border rounded px-3 py-2 mb-2 shadow-sm">
                                <h2>
                                    <?= htmlspecialchars($row['titre']) ?>
                                </h2>
                                <p class="mb-1">
                                    <strong><?= htmlspecialchars($row['pseudo']) ?></strong>
                                </p>
                                <p class="mb-0"><?= htmlspecialchars($row['date_article']) ?></p>
                            </div>
                        </a>
                    <?php endforeach ?>
                </div>
            </div>
        <?php } elseif ($row_take == 0) { ?>
            <div class="rounded box_category_news">
                <div class="article-container p-3">
                    <p class="text-center m-0">Aucun article de cette catégorie en ce moment.</p>
                </div>
            </div>
        <?php } ?>


        <!-- critique -->
        <?php
        try {
            $stmt = $bdd->prepare("SELECT id_news, titre, date_article, category,pseudo FROM news join utilisateurs on id_utilisateurs=news.auteur where news.category='Critique' order by rand() limit 5;");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $nb_row = $stmt->rowCount();


            if ($nb_row < 1) {
                $row_take = 0;
            } elseif ($nb_row < 2) {
                $row_take = 1;
            } elseif ($nb_row < 4) {
                $row_take = 2;
            } else {
                $row_take = 4;
            }

            if ($nb_row != 5) {
                $stmt = $bdd->prepare("SELECT id_news, titre, date_article, category,pseudo FROM news join utilisateurs on id_utilisateurs=news.auteur where news.category='Critique' order by rand() limit" . $row_take . ";");
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException) {
            header('location:' . index_front . '?message=bdd');
            exit();
        }
        // 5 items case
        if ($nb_row == 5) {
            $big_article = $rows[0];
            $small_articles = array_slice($rows, 1);
        ?>
            <div class="d-flex flex-column mb-5">
                <a href="<?= actualite_categorie . '?category=' . urlencode('Critique') ?>" class="mb-3 category_news_title">
                    <h2>Critique</h2>
                </a>

                <!-- Background + Padding -->
                <div class="rounded box_category_news">
                    <div class="row gx-2 p-2 rounded align-items-stretch">

                        <!-- LEFT CARD -->
                        <div class="col-md-7 d-flex">
                            <a href="actualite_article.php?id=<?= $big_article['id_news'] ?>" class="card w-100 card_news" style="text-decoration: none;">

                                <img src="lienDeImage.jpg" alt="Image de l'article" class="card-img-top" style="max-height: 250px; object-fit: cover;">
                                <div class="card-body p-3">
                                    <h4 class="card-title" style="text-decoration: underline;"><?= $big_article['titre'] ?></h4>
                                    <p class="mb-1"><strong><?= $big_article['pseudo'] ?></strong></p>
                                    <p class="mb-0"><?= $big_article['date_article'] ?></p>
                                </div>

                            </a>
                        </div>

                        <!-- RIGHT CARDS STACKED -->
                        <div class="col-md-5 d-flex flex-column h-100">
                            <?php foreach ($small_articles as $small_article) : ?>
                                <a href="actualite_article.php?id=<?= $small_article['id_news'] ?>" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                                    <div class="card-body p-2">
                                        <h6 class="card-title mb-1"><?= $small_article['titre'] ?></h6>
                                        <p class="mb-0"><strong><?= $small_article['pseudo'] . ' a publié le ' ?></strong><?= $small_article['date_article'] ?>
                                        </p>
                                    </div>
                                </a>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } elseif ($row_take == 4 || $row_take == 2) { ?>
            <div class="rounded box_category_news">
                <div class="article-container p-3">

                    <?php foreach ($rows as $row) : ?>
                        <a href="actualite_article.php?id=<?= $row['id_news'] ?>" class="articleBlockLink text-dark">
                            <div class="article border rounded px-3 py-2 mb-2 shadow-sm">
                                <h2>
                                    <?= htmlspecialchars($row['titre']) ?>
                                </h2>
                                <p class="mb-1">
                                    <strong><?= htmlspecialchars($row['pseudo']) ?></strong>
                                </p>
                                <p class="mb-0"><?= htmlspecialchars($row['date_article']) ?></p>
                            </div>
                        </a>
                    <?php endforeach ?>
                </div>
            </div>
        <?php } elseif ($row_take == 0) { ?>
            <div class="rounded box_category_news">
                <div class="article-container p-3">
                    <p class="text-center m-0">Aucun article de cette catégorie en ce moment.</p>
                </div>
            </div>
        <?php } ?>


        <!-- mis a jour -->
        <?php
        try {
            $stmt = $bdd->prepare("SELECT id_news, titre, date_article, category,pseudo FROM news join utilisateurs on id_utilisateurs=news.auteur where news.category='Mise à jour' order by rand() limit 5;");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $nb_row = $stmt->rowCount();


            if ($nb_row < 1) {
                $row_take = 0;
            } elseif ($nb_row < 2) {
                $row_take = 1;
            } elseif ($nb_row < 4) {
                $row_take = 2;
            } else {
                $row_take = 4;
            }

            if ($nb_row != 5) {
                $stmt = $bdd->prepare("SELECT id_news, titre, date_article, category,pseudo FROM news join utilisateurs on id_utilisateurs=news.auteur where news.category='Mise à jour' order by rand() limit" . $row_take . ";");
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException) {
            header('location:' . index_front . '?message=bdd');
            exit();
        }
        // 5 items case
        if ($nb_row == 5) {
            $big_article = $rows[0];
            $small_articles = array_slice($rows, 1);
        ?>
            <div class="d-flex flex-column mb-5">
                <a href="<?= actualite_categorie . '?category=' . urlencode('Mise à jour') ?>" class="mb-3 category_news_title">
                    <h2>Mise à jour</h2>
                </a>

                <!-- Background + Padding -->
                <div class="rounded box_category_news">
                    <div class="row gx-2 p-2 rounded align-items-stretch">

                        <!-- LEFT CARD -->
                        <div class="col-md-7 d-flex">
                            <a href="actualite_article.php?id=<?= $big_article['id_news'] ?>" class="card w-100 card_news" style="text-decoration: none;">

                                <img src="lienDeImage.jpg" alt="Image de l'article" class="card-img-top" style="max-height: 250px; object-fit: cover;">
                                <div class="card-body p-3">
                                    <h4 class="card-title" style="text-decoration: underline;"><?= $big_article['titre'] ?></h4>
                                    <p class="mb-1"><strong><?= $big_article['pseudo'] ?></strong></p>
                                    <p class="mb-0"><?= $big_article['date_article'] ?></p>
                                </div>

                            </a>
                        </div>

                        <!-- RIGHT CARDS STACKED -->
                        <div class="col-md-5 d-flex flex-column h-100">
                            <?php foreach ($small_articles as $small_article) : ?>
                                <a href="actualite_article.php?id=<?= $small_article['id_news'] ?>" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                                    <div class="card-body p-2">
                                        <h6 class="card-title mb-1"><?= $small_article['titre'] ?></h6>
                                        <p class="mb-0"><strong><?= $small_article['pseudo'] . ' a publié le ' ?></strong><?= $small_article['date_article'] ?>
                                        </p>
                                    </div>
                                </a>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } elseif ($row_take == 4 || $row_take == 2) { ?>
            <div class="rounded box_category_news">
                <div class="article-container p-3">

                    <?php foreach ($rows as $row) : ?>
                        <a href="actualite_article.php?id=<?= $row['id_news'] ?>" class="articleBlockLink text-dark">
                            <div class="article border rounded px-3 py-2 mb-2 shadow-sm">
                                <h2>
                                    <?= htmlspecialchars($row['titre']) ?>
                                </h2>
                                <p class="mb-1">
                                    <strong><?= htmlspecialchars($row['pseudo']) ?></strong>
                                </p>
                                <p class="mb-0"><?= htmlspecialchars($row['date_article']) ?></p>
                            </div>
                        </a>
                    <?php endforeach ?>
                </div>
            </div>
        <?php } elseif ($row_take == 0) { ?>
            <div class="rounded box_category_news">
                <div class="article-container p-3">
                    <p class="text-center m-0">Aucun article de cette catégorie en ce moment.</p>
                </div>
            </div>
        <?php } ?>


        <!-- divers/d'autres -->
        <?php
        try {
            $stmt = $bdd->prepare("SELECT id_news, titre, date_article, category,pseudo FROM news join utilisateurs on id_utilisateurs=news.auteur where news.category<>'Général' and news.category<>'Esport' and news.category<>'Évènement' and news.category<>'Critique' and news.category<>'Mise à jour' order by rand() limit 5;");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $nb_row = $stmt->rowCount();


            if ($nb_row < 1) {
                $row_take = 0;
            } elseif ($nb_row < 2) {
                $row_take = 1;
            } elseif ($nb_row < 4) {
                $row_take = 2;
            } else {
                $row_take = 4;
            }

            if ($nb_row != 5) {
                $stmt = $bdd->prepare("SELECT id_news, titre, date_article, category,pseudo FROM news join utilisateurs on id_utilisateurs=news.auteur where news.category<>'Général' and news.category<>'Esport' and news.category<>'Évènement' and news.category<>'Critique' and news.category<>'Mise à jour' order by rand() limit" . $row_take . ";");
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException) {
            header('location:' . index_front . '?message=bdd');
            exit();
        }
        // 5 items case
        if ($nb_row == 5) {
            $big_article = $rows[0];
            $small_articles = array_slice($rows, 1);
        ?>
            <div class="d-flex flex-column mb-5">
                <a href="<?= actualite_categorie . '?category=' . urlencode('Divers') ?>" class="mb-3 category_news_title">
                    <h2>Divers</h2>
                </a>

                <!-- Background + Padding -->
                <div class="rounded box_category_news">
                    <div class="row gx-2 p-2 rounded align-items-stretch">

                        <!-- LEFT CARD -->
                        <div class="col-md-7 d-flex">
                            <a href="actualite_article.php?id=<?= $big_article['id_news'] ?>" class="card w-100 card_news" style="text-decoration: none;">

                                <img src="lienDeImage.jpg" alt="Image de l'article" class="card-img-top" style="max-height: 250px; object-fit: cover;">
                                <div class="card-body p-3">
                                    <h4 class="card-title" style="text-decoration: underline;"><?= $big_article['titre'] ?></h4>
                                    <p class="mb-1"><strong><?= $big_article['pseudo'] ?></strong></p>
                                    <p class="mb-0"><?= $big_article['date_article'] ?></p>
                                </div>

                            </a>
                        </div>

                        <!-- RIGHT CARDS STACKED -->
                        <div class="col-md-5 d-flex flex-column h-100">
                            <?php foreach ($small_articles as $small_article) : ?>
                                <a href="actualite_article.php?id=<?= $small_article['id_news'] ?>" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                                    <div class="card-body p-2">
                                        <h6 class="card-title mb-1"><?= $small_article['titre'] ?></h6>
                                        <p class="mb-0"><strong><?= $small_article['pseudo'] . ' a publié le ' ?></strong><?= $small_article['date_article'] ?>
                                        </p>
                                    </div>
                                </a>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } elseif ($row_take == 4 || $row_take == 2) { ?>
            <div class="rounded box_category_news">
                <div class="article-container p-3">

                    <?php foreach ($rows as $row) : ?>
                        <a href="actualite_article.php?id=<?= $row['id_news'] ?>" class="articleBlockLink text-dark">
                            <div class="article border rounded px-3 py-2 mb-2 shadow-sm">
                                <h2>
                                    <?= htmlspecialchars($row['titre']) ?>
                                </h2>
                                <p class="mb-1">
                                    <strong><?= htmlspecialchars($row['pseudo']) ?></strong>
                                </p>
                                <p class="mb-0"><?= htmlspecialchars($row['date_article']) ?></p>
                            </div>
                        </a>
                    <?php endforeach ?>
                </div>
            </div>
        <?php } elseif ($row_take == 0) { ?>
            <div class="rounded box_category_news">
                <div class="article-container p-3">
                    <p class="text-center m-0">Aucun article de cette catégorie en ce moment.</p>
                </div>
            </div>
        <?php } ?>
    </main>

    <?php include("../include/footer.php"); ?>
</body>

</html>