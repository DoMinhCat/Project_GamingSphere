<?php
session_start();
require('../include/check_timeout.php');
require('../include/database.php');
require_once __DIR__ . '/../path.php';
if (empty($_GET['category']) || empty($_GET['id'])) {
    header('location:' . actualite_main . '?message=' . urlencode('L\'article non trouvé'));
    exit;
}
$origin_category  = $_GET['category'];
$category = $_GET['category'];
$id_article = (int)$_GET['id'];
switch ($category) {
    case 'general':
        $category = 'Général';
        break;
    case 'alaune':
        $category = 'À la une';
        break;
    case 'evenement':
        $category = 'Évènement';
        break;
    default:
        $category = $_GET['category'];
        break;
}
try {
    $stmt = $bdd->prepare("SELECT * FROM news WHERE id_news = ?;");
    $stmt->execute([$id_article]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$article) {
        header('location:' . actualite_categorie . '?category=' . $category . '&message=' . urlencode('L\'article non trouvé'));
        exit;
    }
    $stmt = $bdd->prepare("SELECT pseudo FROM news JOIN utilisateurs on auteur=id_utilisateurs WHERE id_news = ?;");
    $stmt->execute([$id_article]);
    $auteur = $stmt->fetch(PDO::FETCH_ASSOC);
    if (empty($auteur)) $auteur = 'Anonyme';

    $stmt = $bdd->prepare("SELECT id_news, category, titre, date_article, contenue, pseudo from news join utilisateurs on auteur=utilisateurs.id_utilisateurs where category = ? ORDER BY date_article DESC;");
    $stmt->execute([$origin_category]);
    $others = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException) {
    header('location:' . index_front . '&message=bdd');
    exit;
}

?>


<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Détail de l\'actualité';
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
    <main class="container my-5">
        <!-- Navigation -->
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="<?= actualite_main ?>" class="text-decoration-none footer-link">Actualités</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="<?= actualite_categorie . '?category=' . $origin_category ?>" class="text-decoration-none footer-link"><?= htmlspecialchars($category) ?></a>
                        </li>
                        <li class="breadcrumb-item active footer-link" aria-current="page">Article</li>
                    </ol>
                </nav>

                <div class="mb-4">
                    <a href="<?= actualite_categorie . '?category=' . $origin_category ?>" class="text-decoration-none fs-3 return_arrow d-flex align-items-center gap-2">
                        <i class="bi bi-chevron-left"></i>
                        <h1 class="m-0"><?= htmlspecialchars($category) ?></h1>
                    </a>
                </div>

            </div>
        </div>

        <!-- Article content -->
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-10">
                <article class="bg-white shadow-sm rounded p-4 mb-5">
                    <header class="text-center mb-4">
                        <h1 class="display-5 fw-bold text-primary mb-3"><?= htmlspecialchars($article['titre']) ?></h1>

                        <div class="d-flex justify-content-center align-items-center flex-wrap gap-3 text-muted">
                            <span class="badge bg-secondary"><?= htmlspecialchars($category) ?></span>
                            <span><i class="bi bi-calendar3"></i> <?= $article['date_article'] ?></span>
                            <span><i class="bi bi-person-fill"></i> <?= $auteur['pseudo'] ?></span>
                        </div>
                    </header>

                    <!-- Image placeholder -->
                    <!-- TODO: Replace this div with actual image code -->
                    <!-- Example: <img src="path/to/image.jpg" class="img-fluid rounded mb-4" alt="Article image"> -->
                    <div class="bg-light rounded d-flex align-items-center justify-content-center mb-4" style="height: 300px;">
                        <div class="text-center text-muted">
                            <i class="bi bi-image display-4"></i>
                            <p class="mt-2">Image de l'article</p>
                            <!-- EDIT IMAGE CODE HERE -->
                        </div>
                    </div>

                    <div class="article-content">
                        <p class="lead text-dark"><?= nl2br(htmlspecialchars($article['contenue'])) ?></p>
                    </div>
                </article>
            </div>
        </div>

        <!-- D'autres articles meme cate -->
        <div class="row">
            <div class="col-12">
                <div class="border-top pt-5">
                    <h2 class="h3 mb-4 text-center">
                        Autres articles de la catégorie "<?= htmlspecialchars($category) ?>"
                    </h2>

                    <div class="row g-4">
                        <?php foreach ($others as $other) { ?>
                            <div class="col-lg-6 col-md-6">
                                <div class="card h-100 shadow-sm border-0">
                                    <div class="card-body">
                                        <h3 class="card-title h5">
                                            <a href="<?= actualite_article . '?id=' . $other['id_news'] . '&category=' . $origin_category ?>"
                                                class="text-decoration-none text-dark stretched-link">
                                                <?= htmlspecialchars($other['titre']) ?>
                                            </a>
                                        </h3>

                                        <?php
                                        $maxLength = 100;
                                        $content = strip_tags($other['contenue']);
                                        if (mb_strlen($content) > $maxLength) {
                                            $trimmed = mb_substr($content, 0, $maxLength);
                                            $lastSpace = mb_strrpos($trimmed, ' ');
                                            $trimmed = mb_substr($trimmed, 0, $lastSpace);
                                            $contentShow = htmlspecialchars($trimmed . '...');
                                        } else {
                                            $contentShow = htmlspecialchars($content);
                                        }
                                        ?>
                                        <p class="card-text text-muted mb-3"><?= nl2br($contentShow) ?></p>
                                    </div>

                                    <div class="card-footer bg-transparent border-0 pt-0">
                                        <small class="text-muted d-flex justify-content-between align-items-center">
                                            <span><i class="bi bi-calendar3"></i> <?= $other['date_article'] ?></span>
                                            <span><i class="bi bi-person"></i> <?= htmlspecialchars($other['pseudo']) ?></span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="text-center mt-4">
                        <a href="<?= actualite_categorie . '?category=' . $origin_category ?>"
                            class="btn btn-primary btn-lg">
                            Voir tous les articles de <?= htmlspecialchars($category) ?>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php
    include("../include/footer.php");
    ?>
</body>

</html>