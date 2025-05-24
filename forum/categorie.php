<?php
session_start();
require_once('../include/database.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Forum - catégorie';
$pageCategory = 'forum';
echo "<script>const pageCategory = '$pageCategory';</script>";
require('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}

if (!isset($_GET['nom']) || empty($_GET['nom'])) {
    header('location:' . forum_main . '?message=' . urlencode('Catégorie non précisée !'));
    exit;
}

$categorie_nom = $_GET['nom'];
?>

<body>
    <?php include("../include/header.php"); ?>

    <div class="container mt-2 mb-5">
        <?php if (!empty($_GET['message'])) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif ?>
        <?php if (!empty($_GET['success'])) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif ?>

        <div class="col d-flex justify-content-center text-center">
            <div class="d-flex col-md-6 pt-3 pb-2">
                <input type="text" id="search" class="form-control searchBoxFront" placeholder="Rechercher par titre ou auteur">
            </div>
        </div>

        <div class="mb-4 mt-5">
            <a href="<?= forum_main ?>" class="text-decoration-none fs-3 return_arrow d-flex align-items-center gap-2">
                <i class="bi bi-chevron-left"></i>
                <h1 class="m-0"><?= htmlspecialchars($categorie_nom) ?></h1>
            </a>
        </div>

        <?php
        if (!empty($_SESSION['user_id'])) {
            echo '<a href="' . nouveau_sujet . '?categorie=' . urlencode($categorie_nom) . '" class="btn btn-primary mb-2">+ Nouveau sujet</a>';
        }

        $stmt = $bdd->prepare("SELECT * FROM forum_sujets WHERE categories = ? AND parent_id IS NULL ORDER BY date_creation DESC");
        $stmt->execute([$categorie_nom]);
        $sujets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($sujets) === 0) {
            echo "<p class='text-muted'>Aucun sujet dans cette catégorie pour le moment.</p>";
        } ?>
        <div id="results">
            <?php foreach ($sujets as $sujet) {
                try {
                    $stmt_reponses = $bdd->prepare("SELECT COUNT(*) FROM forum_reponses WHERE id_sujet = ?");
                    $stmt_reponses->execute([$sujet['id_sujet']]);
                    $nb_reponses = $stmt_reponses->fetchColumn();
                } catch (PDOException) {
                    header('location:' . forum_main . '?message=' . urlencode('Erreur de la base de données, veuillez réessayer plus tard.'));
                    exit();
                }
            ?>

                <a href="<?= sujet ?>?id=<?= $sujet['id_sujet'] . '&category=' . $categorie_nom ?>" class="text-decoration-none forumBlockLink">

                    <div class="card mx-0 mb-3">
                        <div class="card-body">
                            <h5>
                                <?= htmlspecialchars($sujet['titre']) ?>
                            </h5>
                            <p class="text-muted mb-1">Posté le <?= date("d/m/Y à H:i", strtotime($sujet['date_creation'])) ?> par <?= htmlspecialchars($sujet['auteur'] ?? 'Anonyme') ?></p>
                            <p class="mb-0"><strong><?= $nb_reponses ?></strong> réponse<?= $nb_reponses != 1 ? 's' : '' ?></p>
                        </div>
                    </div>
                </a>

            <?php } ?>
        </div>
    </div>

    <?php include("../include/footer.php"); ?>

    <script>
        const category = <?= json_encode($categorie_nom) ?>;
        async function fetchArticle() {
            let search = document.getElementById('search').value;
            try {
                const response = await fetch('search_forum.php?search=' + encodeURIComponent(search) + '&category=' + encodeURIComponent(category), {
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