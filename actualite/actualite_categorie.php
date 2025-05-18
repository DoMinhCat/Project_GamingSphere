<?php
session_start();
require('../include/check_timeout.php');
require_once('../include/database.php');
require_once __DIR__ . '/../path.php';

if (empty($_GET['category'])) {
    header('location:' . actualite_main . '?message=' . urlencode('Catégorie non trouvée !'));
    exit;
}
$category = $_GET['category'];
try {
    $stmt = $bdd->prepare("SELECT id_news, category titre, date_article, contenue, pseudo from news join utilisateurs on auteur=utilisateurs.id_utilisateurs where category = ?;");
    $stmt->execute([$category]);
    $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException) {
    header('location:' . index_front . '?message=bdd');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<?php
$title = 'Actualités - catégorie';
$pageCategory = 'actualite';
echo "<script>const pageCategory = '$pageCategory';</script>";
require('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}

?>

<body>
    <?php
    include("../include/header.php");
    ?>
    <main class="container mb-5">
        <div class="mb-4 mt-5 d-flex align-items-center gap-2">
            <a href="<?= actualite_main ?>" class="text-decoration-none fs-3 return_arrow">
                <i class="bi bi-chevron-left"></i>
            </a>
            <h1 class="m-0"><?= htmlspecialchars($news['category']) ?></h1>
        </div>

        <!-- foreach loop -->
        <?php foreach ($news as $new) : ?>
            <div class="mb-2">
                <a href="actualite_article.php?id=<?= $news['id_news'] ?>" class="articleBlockLink text-dark">
                    <div class="article border rounded p-3 shadow-sm">
                        <h2>
                            <?= htmlspecialchars($news['titre']) ?>
                        </h2>
                        <p class="mb-0"><?= $news['date_article'] ?> par <strong><?= $news['pseudo'] ?></strong>
                        <p><?= nl2br(htmlspecialchars($news['contenue'])) ?></p>
                    </div>
                </a>
            </div>
        <?php endforeach ?>
    </main>
    <?php
    include("../include/footer.php");
    ?>
</body>

</html>