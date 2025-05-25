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
    $stmt = $bdd->prepare("SELECT id_news, category, titre, date_article, contenue, pseudo from news join utilisateurs on auteur=utilisateurs.id_utilisateurs where category = ? ORDER BY date_article DESC;");
    $stmt->execute([$category]);
    $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException) {
    header('location:' . index_front . '?message=bdd');
    exit;
}

$articlesPerPage = 10;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$totalArticles = count($news);
$totalPages = ceil($totalArticles / $articlesPerPage);
$offset = ($currentPage - 1) * $articlesPerPage;
$currentPageArticles = array_slice($news, $offset, $articlesPerPage);
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

        <div id="results">
            <?php if (count($currentPageArticles) === 0): ?>
                <div class="alert alert-info text-center py-4">
                    <?php if ($currentPage > 1): ?>
                        <p class="mb-0">Aucun article trouvé sur cette page.</p>
                    <?php else: ?>
                        <p class="mb-0">Aucun article dans cette catégorie pour le moment.</p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php foreach ($currentPageArticles as $new) : ?>
                    <div class="mb-3">
                        <a href="<?= actualite_article . '?id=' . $new['id_news'] . '&category=' . $origin_category ?>" class="articleBlockLink text-dark">
                            <div class="article border rounded p-3 shadow-sm">
                                <h2>
                                    <?= htmlspecialchars($new['titre']) ?>
                                </h2>
                                <p class="mb-0"><?= $new['date_article'] ?> par <strong><?= htmlspecialchars($new['pseudo']) ?></strong></p>
                                <?php $maxLength = 100;
                                $content = strip_tags($new['contenue']);
                                if (mb_strlen($content) > $maxLength) {
                                    $trimmed = mb_substr($content, 0, $maxLength);
                                    $lastSpace = mb_strrpos($trimmed, ' ');
                                    $trimmed = mb_substr($trimmed, 0, $lastSpace);
                                    $contentShow = htmlspecialchars($trimmed . '...');
                                } else {
                                    $contentShow = htmlspecialchars($content);
                                }
                                ?>
                                <p><?= nl2br($contentShow) ?></p>


                            </div>
                        </a>
                    </div>
                <?php endforeach ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Navigation des pages" class="mt-5">
                <ul class="pagination justify-content-center flex-wrap">
                    <!-- Previous -->
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
                    (<?= number_format($totalArticles) ?> article<?= $totalArticles > 1 ? 's' : '' ?> au total)
                </small>
            </div>
        <?php endif; ?>
    </main>
    <?php
    include("../include/footer.php");
    ?>

    <script>
        const category = <?= json_encode($category) ?>;
        const originCategory = <?= json_encode($origin_category) ?>;

        async function fetchArticle() {
            let search = document.getElementById('search').value;
            try {
                const response = await fetch('search_actualite.php?search=' + encodeURIComponent(search) + '&category=' + encodeURIComponent(category) + '&origin_category=' + encodeURIComponent(originCategory), {
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