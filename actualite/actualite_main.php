<?php
session_start();
require('../include/check_timeout.php');
require_once('../include/database.php');
require_once __DIR__ . '/../path.php';
function fetchNews(PDO $bdd, string $category): array
{
    try {
        $stmt = $bdd->prepare("SELECT id_news, titre, date_article, category, pseudo, image
                               FROM news 
                               JOIN utilisateurs ON id_utilisateurs = news.auteur 
                               WHERE news.category = :category 
                               ORDER BY date_article desc
                               LIMIT 5;");
        $stmt->bindParam(':category', $category);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $nb_row = count($rows);


        if ($nb_row < 5) {
            if ($nb_row < 1) $row_take = 0;
            elseif ($nb_row < 2) $row_take = 1;
            elseif ($nb_row < 4) $row_take = 2;
            else $row_take = 4;

            $stmt = $bdd->prepare("SELECT id_news, titre, date_article, category, pseudo 
                                   FROM news 
                                   JOIN utilisateurs ON id_utilisateurs = news.auteur 
                                   WHERE news.category = :category 
                                   ORDER BY date_article desc
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
$result = fetchNews($bdd, 'A la une');
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
    $stmt = $bdd->prepare("SELECT id_news, titre, date_article, category,pseudo FROM news join utilisateurs on id_utilisateurs=news.auteur where news.category<>'Général' and news.category<>'Esport' and news.category<>'Évènement' and news.category<>'Critique' and news.category<>'Mise à jour' and news.category<>'À la une' order by date_article desc limit 5;");
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
        $stmt = $bdd->prepare("SELECT id_news, titre, date_article, category,pseudo FROM news join utilisateurs on id_utilisateurs=news.auteur where news.category<>'Général' and news.category<>'Esport' and news.category<>'Évènement' and news.category<>'Critique' and news.category<>'Mise à jour' and news.category<>'À la une' order by date_article desc limit " . $row_take . ";");
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
    <?php include("../include/header.php");    ?>

    <main class="container mt-2 mb-5">
        <?php
        if (!empty($_GET['message'])) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>
        <h1 class="text-center my-5">Actualités</h1>
        <?php
        if ($nb_row_alaune == 5) {
            $big_article = $rows_alaune[0];
            $small_articles = array_slice($rows_alaune, 1);
        ?>
            <div class="d-flex flex-column mb-5">
                <a href="<?= actualite_categorie . '?category=alaune' ?>" class="mb-3 category_news_title">
                    <h2>À la une</h2>
                </a>
                <div class="rounded box_category_news">
                    <div class="row gx-2 p-2 rounded align-items-stretch">
                        <div class="col-md-7 d-flex">
                            <a href="actualite_article.php?id=<?= $big_article['id_news'] . '&category=alaune' ?>" class="card w-100 card_news" style="text-decoration: none;">
                                <?php if (!empty($big_article['image'])): ?>
                                    <img src="../back-office/uploads/<?= htmlspecialchars($big_article['image']) ?>" alt="Image de l'article" class="card-img-top" style="max-height: 250px; object-fit: cover;">
                                <?php else: ?>
                                    <img src="../assets/img/default_article.jpg" alt="Image par défaut" class="card-img-top" style="max-height: 250px; object-fit: cover;">
                                <?php endif; ?>
                                <div class="card-body p-3">
                                    <h4 class="card-title" style="text-decoration: underline;"><?= $big_article['titre'] ?></h4>
                                    <p class="mb-1"><strong><?= $big_article['pseudo'] ?></strong></p>
                                    <p class="mb-0"><?= $big_article['date_article'] ?></p>
                                </div>

                            </a>
                        </div>
                        <div class="col-md-5 d-flex flex-column h-100">
                            <?php foreach ($small_articles as $small_article) : ?>
                                <a href="actualite_article.php?id=<?= $small_article['id_news'] . '&category=alaune' ?>" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                                    <div class="card-body p-2">
                                        <h6 class="card-title mb-1"><?= $small_article['titre'] ?></h6>
                                        <p class="mb-0"><?= $small_article['date_article'] ?> par <strong><?= $small_article['pseudo'] ?></strong>
                                        </p>
                                    </div>
                                </a>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } elseif ($row_take_alaune == 4 || $row_take_alaune == 2 || $row_take_alaune == 1) { ?>
            <div class="d-flex flex-column mb-5">
                <a href="<?= actualite_categorie . '?category=alaune' ?>" class="mb-3 category_news_title">
                    <h2>À la une</h2>
                </a>
                <div class="rounded box_category_news">
                    <div class="article-container p-3">

                        <?php foreach ($rows_alaune as $row) : ?>
                            <a href="actualite_article.php?id=<?= $row['id_news'] . '&category=alaune' ?>" class="articleBlockLink text-dark">
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
            </div>
        <?php } elseif ($row_take_alaune == 0) { ?>
            <div class="d-flex flex-column mb-5">
                <a href="<?= actualite_categorie . '?category=alaune' ?>" class="mb-3 category_news_title">
                    <h2>À la une</h2>
                </a>
                <div class="rounded box_category_news">
                    <div class="article-container p-3">
                        <p class="text-center m-0">Aucun article de cette catégorie en ce moment.</p>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="bande_sepe"></div>
        <?php
        if ($nb_row_general == 5) {
            $big_article = $rows_general[0];
            $small_articles = array_slice($rows_general, 1);
        ?>
            <div class="d-flex flex-column mb-5">
                <a href="<?= actualite_categorie . '?category=general' ?>" class="mb-3 category_news_title">
                    <h2>Général</h2>
                </a>
                <div class="rounded box_category_news">
                    <div class="row gx-2 p-2 rounded align-items-stretch">
                        <div class="col-md-7 d-flex">
                            <a href="actualite_article.php?id=<?= $big_article['id_news'] . '&category=general' ?>" class="card w-100 card_news" style="text-decoration: none;">

                                <img src="lienDeImage.jpg" alt="Image de l'article" class="card-img-top" style="max-height: 250px; object-fit: cover;">
                                <div class="card-body p-3">
                                    <h4 class="card-title" style="text-decoration: underline;"><?= $big_article['titre'] ?></h4>
                                    <p class="mb-1"><strong><?= $big_article['pseudo'] ?></strong></p>
                                    <p class="mb-0"><?= $big_article['date_article'] ?></p>
                                </div>

                            </a>
                        </div>
                        <div class="col-md-5 d-flex flex-column h-100">
                            <?php foreach ($small_articles as $small_article) : ?>
                                <a href="actualite_article.php?id=<?= $small_article['id_news'] . '&category=general' ?>" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                                    <div class="card-body p-2">
                                        <h6 class="card-title mb-1"><?= $small_article['titre'] ?></h6>
                                        <p class="mb-0"><?= $small_article['date_article'] ?> par <strong><?= $small_article['pseudo'] ?></strong>
                                        </p>
                                    </div>
                                </a>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } elseif ($row_take_general == 4 || $row_take_general == 2 || $row_take_general == 1) { ?>
            <div class="d-flex flex-column mb-5">
                <a href="<?= actualite_categorie . '?category=general' ?>" class="mb-3 category_news_title">
                    <h2>Général</h2>
                </a>
                <div class="rounded box_category_news">
                    <div class="article-container p-3">

                        <?php foreach ($rows_general as $row) : ?>
                            <a href="actualite_article.php?id=<?= $row['id_news'] . '&category=general' ?>" class="articleBlockLink text-dark">
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
            </div>
        <?php } elseif ($row_take_general == 0) { ?>
            <div class="d-flex flex-column mb-5">
                <a href="<?= actualite_categorie . '?category=general' ?>" class="mb-3 category_news_title">
                    <h2>Général</h2>
                </a>
                <div class="rounded box_category_news">
                    <div class="article-container p-3">
                        <p class="text-center m-0">Aucun article de cette catégorie en ce moment.</p>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="bande_sepe"></div>
        <?php
        if ($nb_row_esport == 5) {
            $big_article = $rows_esport[0];
            $small_articles = array_slice($rows_esport, 1);
        ?>
            <div class="d-flex flex-column mb-5">
                <a href="<?= actualite_categorie . '?category=' . urlencode('Esport') ?>" class="mb-3 category_news_title">
                    <h2>Esport</h2>
                </a>
                <div class="rounded box_category_news">
                    <div class="row gx-2 p-2 rounded align-items-stretch">
                        <div class="col-md-7 d-flex">
                            <a href="actualite_article.php?id=<?= $big_article['id_news'] . '&category=Esport' ?>" class="card w-100 card_news" style="text-decoration: none;">

                                <img src="lienDeImage.jpg" alt="Image de l'article" class="card-img-top" style="max-height: 250px; object-fit: cover;">
                                <div class="card-body p-3">
                                    <h4 class="card-title" style="text-decoration: underline;"><?= $big_article['titre'] ?></h4>
                                    <p class="mb-1"><strong><?= $big_article['pseudo'] ?></strong></p>
                                    <p class="mb-0"><?= $big_article['date_article'] ?></p>
                                </div>

                            </a>
                        </div>
                        <div class="col-md-5 d-flex flex-column h-100">
                            <?php foreach ($small_articles as $small_article) : ?>
                                <a href="actualite_article.php?id=<?= $small_article['id_news'] . '&category=Esport' ?>" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                                    <div class="card-body p-2">
                                        <h6 class="card-title mb-1"><?= $small_article['titre'] ?></h6>
                                        <p class="mb-0"><?= $small_article['date_article'] ?> par <strong><?= $small_article['pseudo'] ?></strong>
                                        </p>
                                    </div>
                                </a>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } elseif ($row_take_esport == 4 || $row_take_esport == 2 || $row_take_esport == 1) { ?>
            <div class="d-flex flex-column mb-5">
                <a href="<?= actualite_categorie . '?category=' . urlencode('Esport') ?>" class="mb-3 category_news_title">
                    <h2>Esport</h2>
                </a>
                <div class="rounded box_category_news">

                    <div class="article-container p-3">

                        <?php foreach ($rows_esport as $row) : ?>
                            <a href="actualite_article.php?id=<?= $row['id_news'] . '&category=Esport' ?>" class="articleBlockLink text-dark">
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
            </div>
        <?php } elseif ($row_take_esport == 0) { ?>
            <div class="d-flex flex-column mb-5">
                <div class="rounded box_category_news">
                    <div class="article-container p-3">
                        <p class="text-center m-0">Aucun article de cette catégorie en ce moment.</p>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="bande_sepe"></div>
        <?php
        if ($nb_row_event == 5) {
            $big_article = $rows_event[0];
            $small_articles = array_slice($rows_event, 1);
        ?>
            <div class="d-flex flex-column mb-5">
                <a href="<?= actualite_categorie . '?category=evenement' ?>" class="mb-3 category_news_title">
                    <h2>Évènement</h2>
                </a>
                <div class="rounded box_category_news">
                    <div class="row gx-2 p-2 rounded align-items-stretch">
                        <div class="col-md-7 d-flex">
                            <a href="actualite_article.php?id=<?= $big_article['id_news'] . '&category=evenement' ?>" class="card w-100 card_news" style="text-decoration: none;">
                                <img src="lienDeImage.jpg" alt="Image de l'article" class="card-img-top" style="max-height: 250px; object-fit: cover;">
                                <div class="card-body p-3">
                                    <h4 class="card-title" style="text-decoration: underline;"><?= $big_article['titre'] ?></h4>
                                    <p class="mb-1"><strong><?= $big_article['pseudo'] ?></strong></p>
                                    <p class="mb-0"><?= $big_article['date_article'] ?></p>
                                </div>

                            </a>
                        </div>
                        <div class="col-md-5 d-flex flex-column h-100">
                            <?php foreach ($small_articles as $small_article) : ?>
                                <a href="actualite_article.php?id=<?= $small_article['id_news'] . '&category=evenement' ?>" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                                    <div class="card-body p-2">
                                        <h6 class="card-title mb-1"><?= $small_article['titre'] ?></h6>
                                        <p class="mb-0"><?= $small_article['date_article'] ?> par <strong><?= $small_article['pseudo'] ?></strong>
                                        </p>
                                    </div>
                                </a>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } elseif ($row_take_event == 4 || $row_take_event == 2 || $row_take_event == 1) { ?>
            <div class="d-flex flex-column mb-5">
                <a href="<?= actualite_categorie . '?category=evenement'  ?>" class="mb-3 category_news_title">
                    <h2>Évènement</h2>
                </a>
                <div class="rounded box_category_news">
                    <div class="article-container p-3">

                        <?php foreach ($rows_event as $row) : ?>
                            <a href="actualite_article.php?id=<?= $row['id_news'] . '&category=evenement' ?>" class="articleBlockLink text-dark">
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
            </div>
        <?php } elseif ($row_take_event == 0) { ?>
            <div class="d-flex flex-column mb-5">
                <a href="<?= actualite_categorie . '?category=evenement'  ?>" class="mb-3 category_news_title">
                    <h2>Évènement</h2>
                </a>
                <div class="rounded box_category_news">
                    <div class="article-container p-3">
                        <p class="text-center m-0">Aucun article de cette catégorie en ce moment.</p>
                    </div>
                </div>
            </div>
        <?php } ?>

        <div class="bande_sepe"></div>
        <?php
        if ($nb_row_critique == 5) {
            $big_article = $rows_critique[0];
            $small_articles = array_slice($rows_critique, 1);
        ?>
            <div class="d-flex flex-column mb-5">
                <a href="<?= actualite_categorie . '?category=' . urlencode('Critique') ?>" class="mb-3 category_news_title">
                    <h2>Critique</h2>
                </a>
                <div class="rounded box_category_news">
                    <div class="row gx-2 p-2 rounded align-items-stretch">
                        <div class="col-md-7 d-flex">
                            <a href="actualite_article.php?id=<?= $big_article['id_news'] . '&category=' .  urlencode('Critique') ?>" class="card w-100 card_news" style="text-decoration: none;">

                                <img src="lienDeImage.jpg" alt="Image de l'article" class="card-img-top" style="max-height: 250px; object-fit: cover;">
                                <div class="card-body p-3">
                                    <h4 class="card-title" style="text-decoration: underline;"><?= $big_article['titre'] ?></h4>
                                    <p class="mb-1"><strong><?= $big_article['pseudo'] ?></strong></p>
                                    <p class="mb-0"><?= $big_article['date_article'] ?></p>
                                </div>

                            </a>
                        </div>
                        <div class="col-md-5 d-flex flex-column h-100">
                            <?php foreach ($small_articles as $small_article) : ?>
                                <a href="actualite_article.php?id=<?= $small_article['id_news'] . '&category=' . urlencode('Critique') ?>" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                                    <div class="card-body p-2">
                                        <h6 class="card-title mb-1"><?= $small_article['titre'] ?></h6>
                                        <p class="mb-0"><?= $small_article['date_article'] ?> par <strong><?= $small_article['pseudo'] ?></strong>
                                        </p>
                                    </div>
                                </a>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } elseif ($row_take_critique == 4 || $row_take_critique == 2 || $row_take_critique == 1) { ?>
            <div class="d-flex flex-column mb-5">
                <a href="<?= actualite_categorie . '?category=' . urlencode('Critique') ?>" class="mb-3 category_news_title">
                    <h2>Critique</h2>
                </a>
                <div class="rounded box_category_news">
                    <div class="article-container p-3">

                        <?php foreach ($rows_critique as $row) : ?>
                            <a href="actualite_article.php?id=<?= $row['id_news'] . '&category=' . urlencode('Critique') ?>" class="articleBlockLink text-dark">
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
            </div>
        <?php } elseif ($row_take_critique == 0) { ?>
            <div class="d-flex flex-column mb-5">
                <a href="<?= actualite_categorie . '?category=' . urlencode('Critique') ?>" class="mb-3 category_news_title">
                    <h2>Critique</h2>
                </a>
                <div class="rounded box_category_news">
                    <div class="article-container p-3">
                        <p class="text-center m-0">Aucun article de cette catégorie en ce moment.</p>
                    </div>
                </div>
            </div>
        <?php } ?>

        <div class="bande_sepe"></div>
        <?php
        if ($nb_row_update == 5) {
            $big_article = $rows_update[0];
            $small_articles = array_slice($rows_update, 1);
        ?>
            <div class="d-flex flex-column mb-5">
                <a href="<?= actualite_categorie . '?category=miseajour' ?>" class="mb-3 category_news_title">
                    <h2>Mise à jour</h2>
                </a>
                <div class="rounded box_category_news">
                    <div class="row gx-2 p-2 rounded align-items-stretch">
                        <div class="col-md-7 d-flex">
                            <a href="actualite_article.php?id=<?= $big_article['id_news'] . '&category=miseajour' ?>" class="card w-100 card_news" style="text-decoration: none;">

                                <img src="lienDeImage.jpg" alt="Image de l'article" class="card-img-top" style="max-height: 250px; object-fit: cover;">
                                <div class="card-body p-3">
                                    <h4 class="card-title" style="text-decoration: underline;"><?= $big_article['titre'] ?></h4>
                                    <p class="mb-1"><strong><?= $big_article['pseudo'] ?></strong></p>
                                    <p class="mb-0"><?= $big_article['date_article'] ?></p>
                                </div>

                            </a>
                        </div>
                        <div class="col-md-5 d-flex flex-column h-100">
                            <?php foreach ($small_articles as $small_article) : ?>
                                <a href="actualite_article.php?id=<?= $small_article['id_news'] . '&category=miseajour' ?>" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                                    <div class="card-body p-2">
                                        <h6 class="card-title mb-1"><?= $small_article['titre'] ?></h6>
                                        <p class="mb-0"><?= $small_article['date_article'] ?> par <strong><?= $small_article['pseudo'] ?></strong>
                                        </p>
                                    </div>
                                </a>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } elseif ($row_take_update == 4 || $row_take_update == 2 || $row_take_update == 1) { ?>
            <div class="d-flex flex-column mb-5">
                <a href="<?= actualite_categorie . '?category=miseajour' ?>" class="mb-3 category_news_title">
                    <h2>Mise à jour</h2>
                </a>
                <div class="rounded box_category_news">
                    <div class="article-container p-3">

                        <?php foreach ($rows_update as $row) : ?>
                            <a href="actualite_article.php?id=<?= $row['id_news'] . '&category=miseajour' ?>" class="articleBlockLink text-dark">
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
            </div>
        <?php } elseif ($row_take_update == 0) { ?>
            <div class="d-flex flex-column mb-5">
                <a href="<?= actualite_categorie . '?category=miseajour' ?>" class="mb-3 category_news_title">
                    <h2>Mise à jour</h2>
                </a>
                <div class="rounded box_category_news">
                    <div class="article-container p-3">
                        <p class="text-center m-0">Aucun article de cette catégorie en ce moment.</p>
                    </div>
                </div>
            </div>
        <?php } ?>

        <div class="bande_sepe"></div>
        <?php
        if ($nb_row == 5) {
            $big_article = $rows[0];
            $small_articles = array_slice($rows, 1);
        ?>
            <div class="d-flex flex-column mb-5">
                <a href="<?= actualite_categorie . '?category=' . urlencode('Divers') ?>" class="mb-3 category_news_title">
                    <h2>Divers</h2>
                </a>
                <div class="rounded box_category_news">
                    <div class="row gx-2 p-2 rounded align-items-stretch">
                        <div class="col-md-7 d-flex">
                            <a href="actualite_article.php?id=<?= $big_article['id_news'] . '&category=' . urlencode('Divers') ?>" class="card w-100 card_news" style="text-decoration: none;">
                                <img src="lienDeImage.jpg" alt="Image de l'article" class="card-img-top" style="max-height: 250px; object-fit: cover;">
                                <div class="card-body p-3">
                                    <h4 class="card-title" style="text-decoration: underline;"><?= $big_article['titre'] ?></h4>
                                    <p class="mb-1"><strong><?= $big_article['pseudo'] ?></strong></p>
                                    <p class="mb-0"><?= $big_article['date_article'] ?></p>
                                </div>

                            </a>
                        </div>
                        <div class="col-md-5 d-flex flex-column h-100">
                            <?php foreach ($small_articles as $small_article) : ?>
                                <a href="actualite_article.php?id=<?= $small_article['id_news'] . '&category=' . urlencode('Divers') ?>" class="card flex-fill mb-2 card_news" style="text-decoration: none;">
                                    <div class="card-body p-2">
                                        <h6 class="card-title mb-1"><?= $small_article['titre'] ?></h6>
                                        <p class="mb-0"><?= $small_article['date_article'] ?> par <strong><?= $small_article['pseudo'] ?></strong>
                                        </p>
                                    </div>
                                </a>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } elseif ($row_take == 4 || $row_take == 2 || $row_take == 1) { ?>
            <div class="d-flex flex-column mb-5">
                <a href="<?= actualite_categorie . '?category=' . urlencode('Divers') ?>" class="mb-3 category_news_title">
                    <h2>Divers</h2>
                </a>
                <div class="rounded box_category_news">
                    <div class="article-container p-3">

                        <?php foreach ($rows as $row) : ?>
                            <a href="actualite_article.php?id=<?= $row['id_news'] . '&category=' . urlencode('Divers') ?>" class="articleBlockLink text-dark">
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
            </div>
        <?php } elseif ($row_take == 0) { ?>
            <div class="d-flex flex-column mb-5">
                <a href="<?= actualite_categorie . '?category=' . urlencode('Divers') ?>" class="mb-3 category_news_title">
                    <h2>Divers</h2>
                </a>
                <div class="rounded box_category_news">
                    <div class="article-container p-3">
                        <p class="text-center m-0">Aucun article de cette catégorie en ce moment.</p>
                    </div>
                </div>
            </div>
        <?php } ?>
    </main>

    <?php include("../include/footer.php"); ?>
</body>

</html>