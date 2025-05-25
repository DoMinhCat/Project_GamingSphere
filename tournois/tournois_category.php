<?php
session_start();
require('../include/database.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';

if (empty($_GET['category'])) {
    header('location:' . tournois_main . '?error=' . urlencode('Catégorie non précisée !'));
    exit;
}

$display_category = '';
$query = '';
$category = $_GET['category'];

switch ($category) {
    case 'solo_encours':
        $display_category = 'Tournois solo en cours';
        $query = "type='solo' AND status_ENUM='En cours'";
        break;
    case 'solo_termine':
        $display_category = 'Tournois solo terminés';
        $query = "type='solo' AND status_ENUM='Terminé'";
        break;
    case 'solo_attente':
        $display_category = 'Tournois solo en attente';
        $query = "type='solo' AND status_ENUM='En attente'";
        break;
    case 'equipe_encours':
        $display_category = 'Tournois en équipe en cours';
        $query = "type='equipe' AND status_ENUM='En cours'";
        break;
    case 'equipe_attente':
        $display_category = 'Tournois en équipe en attente';
        $query = "type='equipe' AND status_ENUM='En attente'";
        break;
    case 'equipe_termine':
        $display_category = 'Tournois en équipe terminés';
        $query = "type='equipe' AND status_ENUM='Terminé'";
        break;
    default:
        header('location:' . tournois_main . '?error=' . urlencode('Catégorie non précisée !'));
        exit;
}

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$tournois_per_page = 12;
$offset = ($page - 1) * $tournois_per_page;

try {
    $count_stmt = $bdd->prepare("SELECT COUNT(*) FROM tournoi WHERE " . $query);
    $count_stmt->execute();
    $total_tournois = $count_stmt->fetchColumn();
    $total_pages = ceil($total_tournois / $tournois_per_page);

    $stmt = $bdd->prepare("SELECT * FROM tournoi WHERE " . $query . " ORDER BY date_debut DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $tournois_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $tournois = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $user_registrations = [];
    if (isset($_SESSION['user_id'])) {
        $reg_stmt = $bdd->prepare("SELECT id_tournoi FROM inscription_tournoi WHERE id_tournoi = ? AND user_id = ?;");
        $reg_stmt->execute([[$tournoi['id_tournoi'], $user_id]]);
        $user_registrations = $reg_stmt->fetchAll(PDO::FETCH_COLUMN);
    }
} catch (PDOException $e) {
    header('location:' . tournois_main . '?message=bdd&err=' . urlencode($e->getMessage()));
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Tournois - ' . $display_category;
$pageCategory = 'tournois';
echo "<script>const pageCategory = '$pageCategory';</script>";
include('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}
?>

<body>
    <?php include('../include/header.php'); ?>
    <main class="container my-5">
        <div class="mb-4 mt-5">
            <a href="<?= tournois_main ?>" class="text-decoration-none fs-3 return_arrow d-flex align-items-center gap-2">
                <i class="bi bi-chevron-left"></i>
                <h1 class="m-0"><?= htmlspecialchars($display_category) ?></h1>
            </a>
        </div>

        <?php if (empty($tournois)): ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle me-2"></i>
                Aucun tournoi trouvé dans cette catégorie.
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($tournois as $tournoi):
                    $is_registered = in_array($tournoi['id_tournoi'], $user_registrations);
                ?>
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($tournoi['nom_tournoi']) ?></h5>
                                <p class="card-text"><strong>Jeu :</strong> <?= htmlspecialchars($tournoi['jeu']) ?></p>
                                <p class="card-text"><strong>Date de Début :</strong> <?= htmlspecialchars(date('d/m/Y', strtotime($tournoi['date_debut']))) ?></p>
                                <p class="card-text"><strong>Date de Fin :</strong> <?= htmlspecialchars(date('d/m/Y', strtotime($tournoi['date_fin']))) ?></p>
                            </div>
                            <div class="card-footer text-center">
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <?php if ($is_registered): ?>
                                        <button class="btn btn-outline-danger btn-sm desinscrire-btn me-2" data-id="<?= $tournoi['id_tournoi'] ?>">
                                            <i class="bi bi-x-circle"></i> Se désinscrire
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-outline-warning btn-sm participer-btn me-2" data-id="<?= $tournoi['id_tournoi'] ?>">
                                            <i class="bi bi-check-circle"></i> Participer
                                        </button>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <button class="btn btn-outline-secondary btn-sm me-2" disabled title="Connectez-vous pour participer">
                                        <i class="bi bi-lock"></i> Connexion requise
                                    </button>
                                <?php endif; ?>
                                <a href="<?= tournois_details ?>?id_tournoi=<?= $tournoi['id_tournoi'] ?>" class="btn btn-danger btn-sm">Plus d'informations</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav aria-label="Navigation pagination" class="mt-5">
                    <ul class="pagination justify-content-center">
                        <!-- Précédent -->
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?category=<?= urlencode($category) ?>&page=<?= $page - 1 ?>">Précédent</a>
                            </li>
                        <?php else: ?>
                            <li class="page-item disabled">
                                <span class="page-link">Précédent</span>
                            </li>
                        <?php endif; ?>

                        <?php
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $page + 2);

                        if ($page <= 3) {
                            $end_page = min($total_pages, 5);
                        }
                        if ($page > $total_pages - 3) {
                            $start_page = max(1, $total_pages - 4);
                        }

                        if ($start_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?category=<?= urlencode($category) ?>&page=1">1</a>
                            </li>
                            <?php if ($start_page > 2): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?category=<?= urlencode($category) ?>&page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($end_page < $total_pages): ?>
                            <?php if ($end_page < $total_pages - 1): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="?category=<?= urlencode($category) ?>&page=<?= $total_pages ?>"><?= $total_pages ?></a>
                            </li>
                        <?php endif; ?>

                        <!-- Suivant -->
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?category=<?= urlencode($category) ?>&page=<?= $page + 1 ?>">Suivant</a>
                            </li>
                        <?php else: ?>
                            <li class="page-item disabled">
                                <span class="page-link">Suivant</span>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>

            <div class="text-center mt-3 text-muted">
                <small>
                    Affichage de <?= $offset + 1 ?> à <?= min($offset + $tournois_per_page, $total_tournois) ?>
                    sur <?= $total_tournois ?> tournoi<?= $total_tournois > 1 ? 's' : '' ?>
                </small>
            </div>
        <?php endif; ?>
    </main>

    <?php include('../include/footer.php'); ?>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="fluis.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page tournois chargée');

            attachTournoiEventListeners();

            const urlParams = new URLSearchParams(window.location.search);
            const error = urlParams.get('error');
            const message = urlParams.get('message');

            if (error) {
                fluis.showNotification(decodeURIComponent(error), 'error');
            }
            if (message) {
                fluis.showNotification(decodeURIComponent(message), 'success');
            }
        });

        function attachTournoiEventListeners() {
            document.querySelectorAll('.participer-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const tournoiId = this.getAttribute('data-id');
                    fluis.participerTournoi(tournoiId);
                });
            });

            document.querySelectorAll('.desinscrire-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const tournoiId = this.getAttribute('data-id');
                    fluis.desinscrireTournoi(tournoiId);
                });
            });
        }
    </script>
</body>

</html>