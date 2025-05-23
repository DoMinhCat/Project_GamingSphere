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
$origin_category = $_GET['category'];
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
    $stmt = $bdd->prepare("SELECT id_news, category, titre, date_article, contenue, pseudo from news join utilisateurs on auteur=utilisateurs.id_utilisateurs where category = ?;");
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
    <main class="container mt-2 mb-5">
        <?php
        if (!empty($_GET['message'])) { ?>
            <div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                <?= htmlspecialchars($_GET['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <div class="col d-flex justify-content-center text-center">
            <div class="d-flex col-md-6 pt-3 pb-2">
                <input type="text" id="search" class="form-control searchBoxFront" placeholder="Rechercher par titre ou auteur">
            </div>
        </div>

        <div class="mb-4 mt-5">
            <a href="<?= actualite_main ?>" class="text-decoration-none fs-3 return_arrow d-flex align-items-center gap-2">
                <i class="bi bi-chevron-left"></i>
                <h1 class="m-0"><?= htmlspecialchars($category) ?></h1>
            </a>
        </div>

        <?php foreach ($news as $new) : ?>
            <div id="results">
                <div class="mb-3">
                    <a href="actualite_article.php?id=<?= $new['id_news'] . '&category=' . $origin_category ?>" class="articleBlockLink text-dark">
                        <div class="article border rounded p-3 shadow-sm">
                            <h2>
                                <?= htmlspecialchars($new['titre']) ?>
                            </h2>
                            <p class="mb-0"><?= $new['date_article'] ?> par <strong><?= $new['pseudo'] ?></strong>
                            <p><?= nl2br(htmlspecialchars($new['contenue'])) ?></p>
                        </div>
                    </a>
                </div>
            </div>
        <?php endforeach ?>
    </main>
    <?php
    include("../include/footer.php");
    ?>

    <script>
        const category = <?= json_encode($category) ?>;
        async function fetchArticle() {
            let search = document.getElementById('search').value;
            try {
                const response = await fetch('search_actualite.php?search=' + encodeURIComponent(search) + '&category=' + encodeURIComponent(category), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.text();
                document.getElementById('results').innerHTML = data;
            } catch (error) {
                console.error('Fetch erreur:', error);
            }
        }
        const searchInput = document.getElementById('search');
        searchInput.addEventListener('input', function() {
            fetchArticle();
        });
    </script>
</body>

</html>