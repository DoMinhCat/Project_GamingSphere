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
        <div class="mb-4 mt-5">
            <a href="<?= actualite_categorie . '?category=' . $origin_category ?>" class="text-decoration-none fs-3 return_arrow d-flex align-items-center gap-2">
                <i class="bi bi-chevron-left"></i>
                <h1 class="m-0"><?= htmlspecialchars($category) ?></h1>
            </a>
        </div>

        <div class="d-flex mb-5 flex-column">
            <h1 class="text-center mb-3"><?= htmlspecialchars($article['titre']) ?></h1>
            <div class="mb-5">
                picture here
            </div>
            <p><?= 'Publié le : ' . $article['date_article'] ?></p>
            <p class="mt-5"><?= nl2br(htmlspecialchars($article['contenue'])) ?></p>
        </div>
        <!-- D'autres article meme cate -->
        <div class="d-flex mb-5 flex-column">

        </div>
    </main>
    <?php
    include("../include/footer.php");
    ?>
</body>

</html>