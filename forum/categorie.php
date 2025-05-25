<?php
session_start();
require_once('../include/database.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';

if (!isset($_GET['nom']) || empty($_GET['nom'])) {
    header('location:' . forum_main . '?message=' . urlencode('Catégorie non précisée !'));
    exit;
}

$categorie_nom = $_GET['nom'];

try {
    $stmt = $bdd->prepare("SELECT * FROM forum_sujets WHERE categories = ? ORDER BY date_creation DESC");
    $stmt->execute([$categorie_nom]);
    $sujets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException) {
    header('location:' . forum_main . '?message=' . urlencode('Erreur de la base de données, veuillez réessayer plus tard.'));
    exit();
}

$sujetsPerPage = 10;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$totalSujets = count($sujets);
$totalPages = ceil($totalSujets / $sujetsPerPage);
$offset = ($currentPage - 1) * $sujetsPerPage;
$currentPageSujets = array_slice($sujets, $offset, $sujetsPerPage);

function getPaginationUrl($page)
{
    $params = $_GET;
    $params['page'] = $page;
    return '?' . http_build_query($params);
}
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
        ?>

        <div id="results">
            <?php if (count($currentPageSujets) === 0): ?>
                <div class="alert alert-info text-center py-4">
                    <?php if ($currentPage > 1): ?>
                        <p class="mb-0">Aucun sujet trouvé sur cette page.</p>
                    <?php else: ?>
                        <p class="mb-0">Aucun sujet dans cette catégorie pour le moment.</p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php foreach ($currentPageSujets as $sujet): ?>
                    <?php
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
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h5 class="mb-2">
                                            <?= htmlspecialchars($sujet['titre']) ?>
                                        </h5>
                                        <p class="text-muted mb-1 small">
                                            Posté le <?= date("d/m/Y à H:i", strtotime($sujet['date_creation'])) ?>
                                            par <?= htmlspecialchars($sujet['auteur'] ?? 'Anonyme') ?>
                                        </p>
                                    </div>
                                    <div class="text-end ms-3">
                                        <span class="badge bg-primary rounded-pill">
                                            <?= $nb_reponses ?> réponse<?= $nb_reponses != 1 ? 's' : '' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Navigation des pages" class="mt-5">
                <ul class="pagination justify-content-center flex-wrap">
                    <!-- Previous  -->
                    <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $currentPage > 1 ? getPaginationUrl($currentPage - 1) : '#' ?>"
                            aria-label="Page précédente" <?= $currentPage <= 1 ? 'tabindex="-1"' : '' ?>>
                            <span aria-hidden="true">&laquo;</span>
                            <span class="d-none d-sm-inline ms-1">Précédent</span>
                        </a>
                    </li>

                    <?php
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($totalPages, $currentPage + 2);

                    if ($endPage - $startPage < 4) {
                        if ($startPage == 1) {
                            $endPage = min($totalPages, $startPage + 4);
                        } else {
                            $startPage = max(1, $endPage - 4);
                        }
                    }
                    ?>

                    <?php if ($startPage > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= getPaginationUrl(1) ?>">1</a>
                        </li>
                        <?php if ($startPage > 2): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Page number -->
                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="<?= getPaginationUrl($i) ?>">
                                <?= $i ?>
                                <?php if ($i == $currentPage): ?>
                                    <span class="visually-hidden">(page courante)</span>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($endPage < $totalPages): ?>
                        <?php if ($endPage < $totalPages - 1): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= getPaginationUrl($totalPages) ?>"><?= $totalPages ?></a>
                        </li>
                    <?php endif; ?>

                    <!-- Next -->
                    <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $currentPage < $totalPages ? getPaginationUrl($currentPage + 1) : '#' ?>"
                            aria-label="Page suivante" <?= $currentPage >= $totalPages ? 'tabindex="-1"' : '' ?>>
                            <span class="d-none d-sm-inline me-1">Suivant</span>
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Pagination -->
            <div class="text-center text-muted mt-3">
                <small>
                    Page <?= $currentPage ?> sur <?= $totalPages ?>
                    (<?= number_format($totalSujets) ?> sujet<?= $totalSujets > 1 ? 's' : '' ?> au total)
                </small>
            </div>
        <?php endif; ?>
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

                const paginationNav = document.querySelector('nav[aria-label="Navigation des pages"]');
                const paginationInfo = document.querySelector('.text-center.text-muted');
                if (paginationNav) {
                    paginationNav.style.display = search.trim() ? 'none' : 'block';
                }
                if (paginationInfo) {
                    paginationInfo.style.display = search.trim() ? 'none' : 'block';
                }
            } catch (error) {
                console.error('Fetch erreur:', error);
            }
        }

        const searchInput = document.getElementById('search');
        searchInput.addEventListener('input', function() {
            fetchArticle();
        });

        searchInput.addEventListener('input', function() {
            if (this.value.trim() === '') {
                setTimeout(() => {
                    const paginationNav = document.querySelector('nav[aria-label="Navigation des pages"]');
                    const paginationInfo = document.querySelector('.text-center.text-muted');
                    if (paginationNav) {
                        paginationNav.style.display = 'block';
                    }
                    if (paginationInfo) {
                        paginationInfo.style.display = 'block';
                    }
                }, 100);
            }
        });
    </script>
</body>

</html>
