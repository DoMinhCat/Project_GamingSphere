<?php
session_start();
require('../include/check_timeout.php');
require('../include/database.php');
require_once __DIR__ . '/../path.php';
function fetchNews(PDO $bdd, string $category): array
{
    try {
        $stmt = $bdd->prepare("SELECT id_news, titre, date_article, category, pseudo 
                               FROM news 
                               JOIN utilisateurs ON id_utilisateurs = news.auteur 
                               WHERE news.category = :category 
                               ORDER BY RAND() 
                               LIMIT 5;");
        $stmt->bindParam(':category', $category);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $nb_row = count($rows);

        // Adjust number of articles to return if less than 5
        if ($nb_row < 5) {
            if ($nb_row < 1) $row_take = 0;
            elseif ($nb_row < 2) $row_take = 1;
            elseif ($nb_row < 4) $row_take = 2;
            else $row_take = 4;

            $stmt = $bdd->prepare("SELECT id_news, titre, date_article, category, pseudo 
                                   FROM news 
                                   JOIN utilisateurs ON id_utilisateurs = news.auteur 
                                   WHERE news.category = :category 
                                   ORDER BY RAND() 
                                   LIMIT $row_take;");
            $stmt->bindParam(':category', $category);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $row_take = 5;
        }

        return [
            'rows' => $rows,
            'nb_row' => $nb_row,
            'row_take' => $row_take
        ];
    } catch (PDOException) {
        header('Location: ' . index_front . '?message=bdd');
        exit();
    }
}
$result = fetchNews($bdd, 'À la une');
$rows_alaune = $result['rows'];
$nb_row_alaune = $result['nb_row'];
$row_take_alaune = $result['row_take'];

$result = fetchNews($bdd, 'Esport');
$rows_esport = $result['rows'];
$nb_row_esport = $result['nb_row'];
$row_take_esport = $result['row_take'];

$result = fetchNews($bdd, 'Évènement');
$rows_event = $result['rows'];
$nb_row_event = $result['nb_row'];
$row_take_event = $result['row_take'];

$result = fetchNews($bdd, 'Général');
$rows_general = $result['rows'];
$nb_row_general = $result['nb_row'];
$row_take_general = $result['row_take'];

$result = fetchNews($bdd, 'Mise à jour');
$rows_update = $result['rows'];
$nb_row_update = $result['nb_row'];
$row_take_update = $result['row_take'];

$result = fetchNews($bdd, 'Critique');
$rows_critique = $result['rows'];
$nb_row_critique = $result['nb_row'];
$row_take_critique = $result['row_take'];

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
        // 5 items case
        if ($nb_row_alaune == 5) {
            $big_article = $rows_alaune[0];
            $small_articles = array_slice($rows_alaune, 1);
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
        <?php } elseif ($row_take_alaune == 4 || $row_take_alaune == 2) { ?>
            <div class="rounded box_category_news">
                <div class="article-container p-3">

                    <?php foreach ($rows_alaune as $row) : ?>
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
        <?php } elseif ($row_take_alaune == 0) { ?>
            <div class="rounded box_category_news">
                <div class="article-container p-3">
                    <p class="text-center m-0">Aucun article de cette catégorie en ce moment.</p>
                </div>
            </div>
        <?php } ?>

        <!-- Esport -->
        <?php
        // 5 items case
        if ($nb_row_esport == 5) {
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
        <?php } elseif ($row_take_esport == 4 || $row_take_esport == 2) { ?>
            <div class="rounded box_category_news">
                <div class="article-container p-3">

                    <?php foreach ($rows_esport as $row) : ?>
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
        <?php } elseif ($row_take_esport == 0) { ?>
            <div class="rounded box_category_news">
                <div class="article-container p-3">
                    <p class="text-center m-0">Aucun article de cette catégorie en ce moment.</p>
                </div>
            </div>
        <?php } ?>

        <!-- event -->
        <?php
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
        <?php } elseif ($row_take_event == 4 || $row_take_event == 2) { ?>
            <div class="rounded box_category_news">
                <div class="article-container p-3">

                    <?php foreach ($rows_event as $row) : ?>
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
        <?php } elseif ($row_take_event == 0) { ?>
            <div class="rounded box_category_news">
                <div class="article-container p-3">
                    <p class="text-center m-0">Aucun article de cette catégorie en ce moment.</p>
                </div>
            </div>
        <?php } ?>


        <!-- general -->
        <?php
        // 5 items case
        if ($nb_row_general == 5) {
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
        <?php } elseif ($row_take_general == 4 || $row_take_general == 2) { ?>
            <div class="rounded box_category_news">
                <div class="article-container p-3">

                    <?php foreach ($rows_general as $row) : ?>
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
        <?php } elseif ($row_take_general == 0) { ?>
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
        if ($nb_row_critique == 5) {
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
        <?php } elseif ($row_take_critique == 4 || $row_take_critique == 2) { ?>
            <div class="rounded box_category_news">
                <div class="article-container p-3">

                    <?php foreach ($rows_critique as $row) : ?>
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
        <?php } elseif ($row_take_critique == 0) { ?>
            <div class="rounded box_category_news">
                <div class="article-container p-3">
                    <p class="text-center m-0">Aucun article de cette catégorie en ce moment.</p>
                </div>
            </div>
        <?php } ?>


        <!-- mis a jour -->
        <?php
        // 5 items case
        if ($nb_row_update == 5) {
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
        <?php } elseif ($row_take_update == 4 || $row_take_update == 2) { ?>
            <div class="rounded box_category_news">
                <div class="article-container p-3">

                    <?php foreach ($rows_update as $row) : ?>
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
        <?php } elseif ($row_take_update == 0) { ?>
            <div class="rounded box_category_news">
                <div class="article-container p-3">
                    <p class="text-center m-0">Aucun article de cette catégorie en ce moment.</p>
                </div>
            </div>
        <?php } ?>


        <!-- divers/d'autres -->
        <?php
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