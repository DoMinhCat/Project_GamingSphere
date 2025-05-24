<?php
session_start();
require('../include/check_timeout.php');
require('../include/database.php');
require_once __DIR__ . '/../path.php';

if (!isset($_GET['category']) || empty($_GET['category'])) {
    header('location:' . magasin_main . '?error=' . urlencode('Aucune catégorie précise !'));
    exit;
}
$category = $_GET['category'];
$display_category = '';
$query = '';
switch ($category) {
    case 'meilleur':
        $query = "ORDER BY note_jeu DESC";
        $display_category = "Meilleurs jeux";
        break;
    case 'nouveau':
        $query = "ORDER BY date_sortie DESC";
        $display_category = "Nouveaux jeux";
        break;
    case 'gratuit':
        $query = "WHERE prix=0";
        $display_category = "Jeux gratuits";
        break;
    default:
        header('location:' . magasin_main . '?error=' . urlencode('Aucune catégorie précise !'));
        exit;
}
try {
    $stmt = $bdd->prepare("SELECT * FROM jeu " . $query . ";");
    $stmt->execute();
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException) {
    header('location:' . magasin_main . '?message=bdd');
    exit;
}

// Pagination logic
$gamesPerPage = 10;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$totalGames = count($games);
$totalPages = ceil($totalGames / $gamesPerPage);
$offset = ($currentPage - 1) * $gamesPerPage;
$currentPageGames = array_slice($games, $offset, $gamesPerPage);

// Generate pagination URL with existing GET parameters
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
$title = 'Magasin - catégorie';
$pageCategory = 'magasin';
echo "<script>const pageCategory = '$pageCategory';</script>";
include('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}
?>

<body>
    <?php include('../include/header.php'); ?>
    <main class="container mt-2 mb-5">
        <?php
        if (!empty($_GET['message'])) { ?>
            <div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                <?= htmlspecialchars($_GET['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <!-- search box -->
        <div class="col d-flex justify-content-center text-center">
            <div class="d-flex col-md-6 pt-3 pb-2">
                <input type="text" id="search" class="form-control searchBoxFront" placeholder="Rechercher par nom du jeu">
            </div>
        </div>

        <div class="mb-4 mt-5">
            <a href="<?= magasin_main ?>" class="text-decoration-none fs-3 return_arrow d-flex align-items-center gap-2">
                <i class="bi bi-chevron-left"></i>
                <h1 class="m-0"><?= htmlspecialchars($display_category) ?></h1>
            </a>
        </div>

        <!-- Games Grid Container -->
        <div class="container-fluid px-0">
            <!-- Games Grid -->
            <div class="row g-3 g-md-4">
                <?php foreach ($currentPageGames as $game) : ?>
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3 d-flex align-items-stretch">
                        <div class="card shadow-sm w-100 d-flex flex-column h-100">
                            <!-- Game Image -->
                            <div class="card-img-container" style="height: 200px; overflow: hidden;">
                                <?php if (!empty($game['image'])): ?>
                                    <img src="../back-office/uploads/<?= htmlspecialchars($game['image']) ?>"
                                        class="card-img-top w-100 h-100"
                                        alt="<?= htmlspecialchars($game['nom']) ?>"
                                        style="object-fit: cover;">
                                <?php else: ?>
                                    <img src="/magasin/img/no_image2.png"
                                        class="card-img-top w-100 h-100"
                                        alt="Aucune image"
                                        style="object-fit: cover;">
                                <?php endif; ?>
                            </div>

                            <!-- Card Body -->
                            <div class="card-body d-flex flex-column p-3">
                                <h5 class="card-title mb-2 text-truncate" title="<?= htmlspecialchars($game['nom']) ?>">
                                    <?= htmlspecialchars($game['nom']) ?>
                                </h5>
                                <p class="card-text mb-3">
                                    <strong class="text-success fs-5"><?= htmlspecialchars($game['prix']) ?> €</strong>
                                </p>

                                <!-- Buttons Container -->
                                <div class="mt-auto">
                                    <div class="d-grid gap-2">
                                        <a href="<?= magasin_game ?>?id=<?= $game['id_jeu'] ?>"
                                            class="btn btn-magasin btn-outline-primary btn-sm">
                                            <span class="d-none d-sm-inline">Voir détails</span>
                                            <span class="d-sm-none">Détails</span>
                                        </a>
                                        <button class="btn btn-magasin btn-success btn-sm btn-add-to-cart"
                                            data-id="<?= $game['id_jeu'] ?>">
                                            <span class="d-none d-md-inline">Ajouter au panier</span>
                                            <span class="d-md-none d-none d-sm-inline">Ajouter</span>
                                            <span class="d-sm-none">+</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- No Games Message -->
            <?php if (empty($currentPageGames)): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info text-center py-5">
                            <h4>Aucun jeu trouvé</h4>
                            <p class="mb-0">Il n'y a actuellement aucun jeu disponible dans cette catégorie.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Navigation des pages" class="mt-5">
                <ul class="pagination justify-content-center flex-wrap">
                    <!-- Previous Button -->
                    <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $currentPage > 1 ? getPaginationUrl($currentPage - 1) : '#' ?>"
                            aria-label="Page précédente" <?= $currentPage <= 1 ? 'tabindex="-1"' : '' ?>>
                            <span aria-hidden="true">&laquo;</span>
                            <span class="d-none d-sm-inline ms-1">Précédent</span>
                        </a>
                    </li>

                    <?php
                    // Calculate page range to display
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($totalPages, $currentPage + 2);

                    // Adjust range if we're near the beginning or end
                    if ($endPage - $startPage < 4) {
                        if ($startPage == 1) {
                            $endPage = min($totalPages, $startPage + 4);
                        } else {
                            $startPage = max(1, $endPage - 4);
                        }
                    }
                    ?>

                    <!-- First page (if not in range) -->
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

                    <!-- Page numbers -->
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

                    <!-- Last page (if not in range) -->
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

                    <!-- Next Button -->
                    <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $currentPage < $totalPages ? getPaginationUrl($currentPage + 1) : '#' ?>"
                            aria-label="Page suivante" <?= $currentPage >= $totalPages ? 'tabindex="-1"' : '' ?>>
                            <span class="d-none d-sm-inline me-1">Suivant</span>
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Pagination Info -->
            <div class="text-center text-muted mt-3">
                <small>
                    Page <?= $currentPage ?> sur <?= $totalPages ?>
                    (<?= number_format($totalGames) ?> jeu<?= $totalGames > 1 ? 's' : '' ?> au total)
                </small>
            </div>
        <?php endif; ?>

    </main>
    <?php include('../include/footer.php'); ?>
</body>

</html>