<?php
session_start();
require('include/database.php');
require('include/check_timeout.php');
require_once __DIR__ . '/path.php';

$success = $_GET['success'] ?? "";
$user_pseudo = htmlspecialchars($_GET['user_pseudo'] ?? "");
$pseudo = htmlspecialchars($_GET['pseudo'] ?? "");

try {
    $stmt = $bdd->prepare("SELECT id_tournoi, nom_tournoi, date_debut, date_fin, jeu 
                FROM tournoi 
                WHERE status_ENUM='En cours' LIMIT 6;");
    $stmt->execute();
    $tournois = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $bdd->query("SELECT id_jeu, nom, prix, image FROM jeu ORDER BY note_jeu DESC LIMIT 6;");
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $bdd->query("SELECT id_news, category, titre, date_article, contenue, pseudo from news join utilisateurs on auteur=utilisateurs.id_utilisateurs where category = 'A la une' ORDER BY date_article DESC LIMIT 4;");
    $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException) {
    http_response_code(500);
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Acceuil';
$pageCategory = 'accueil';
echo "<script>const pageCategory = '$pageCategory';</script>";
require('include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="include/check_timeout.js"></script>';
}
?>

<body>
    <?php include('include/header.php');
    if ($success == '1' || $success == 'connected') : ?>
        <div class="feedback text-center mb-4 mt-4" style="background-color: #f5f0e1; color:#1E3D59;">
            <?php
            if (!empty($pseudo)) {
                echo "Bienvenue " . $pseudo;
            } elseif (!empty($user_pseudo)) {
                echo "Connecté en tant que " . $user_pseudo;
            }
            ?>
        </div>
    <?php endif;

    if (isset($_GET['message']) && $_GET['message'] == 'email_verifie') { ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            Vous avez déjà vérifié votre email !
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php }
    if (isset($_GET['message']) && $_GET['message'] == 'bdd') { ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            Erreur de la base de données, veuillez reéssayer plus tard ! !
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php } ?>

    <main class="mb-5">
        <div class="carousel-container">
            <div id="carouselExampleIndicators" class="carousel slide carousel-fade" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
                </div>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="include/img/fortnite-tournaments.webp" class="d-block img-fluid c-img" alt="Fortnite" onclick="window.location.href='<?= tournois_main ?>'">
                        <div class="carousel-caption d-none d-md-block lato24">
                            <h5>Fortnite Tournaments</h5>
                            <p>Join the latest Fortnite tournaments and compete with the best.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="include/img/Wasted-League-of-Legends-Temps-perdus-sur-LOL-1024x538.webp" class="d-block c-img img-fluid" alt="LOL" onclick="window.location.href='<?= actualite_main ?>'">
                        <div class="carousel-caption d-none d-md-block lato24">
                            <h5>League of Legends News</h5>
                            <p>Stay updated with the latest news in the League of Legends community.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="include/img/Valorant-Skin-Hub.avif" class="d-block c-img img-fluid" alt="Skin Valorant" onclick="window.location.href='<?= magasin_main ?>'">
                        <div class="carousel-caption d-none d-md-block lato24">
                            <h5>Valorant Skins</h5>
                            <p>Explore the newest skins available in Valorant.</p>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
        <div id="bande_sepe"></div>

        <!-- tournois -->
        <div class="container-fluid py-5">
            <div class="text-center mb-5">
                <a href="<?= tournois_main ?>" class="text-decoration-none category_news_title">
                    <h2 class="display-5 fw-bold mb-3">
                        Tournois en cours
                    </h2>
                    <p class="lead text-muted">Participez aux meilleurs tournois gaming du moment</p>
                </a>
            </div>

            <div class="container">
                <div class="row g-4">
                    <?php if (!empty($tournois)): ?>
                        <?php foreach ($tournois as $tournoi):
                            $user_id = $_SESSION['user_id'] ?? null;
                            $is_registered = false;
                            if ($user_id) {
                                $check_stmt = $bdd->prepare("SELECT COUNT(*) FROM inscription_tournoi WHERE id_tournoi = ? AND user_id = ?;");
                                $check_stmt->execute([$tournoi['id_tournoi'], $user_id]);
                                $is_registered = $check_stmt->fetchColumn() > 0;
                            } ?>
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class='card h-100 shadow-sm border-0'>
                                    <div class='card-body'>
                                        <h5 class='card-title fw-bold text-center mb-3'><?= htmlspecialchars($tournoi['nom_tournoi']) ?></h5>
                                        <div class="tournament-info">
                                            <p class='card-text mb-2'>
                                                <strong>Jeu :</strong> <?= htmlspecialchars($tournoi['jeu']) ?>
                                            </p>
                                            <p class='card-text mb-2'>
                                                <strong>Début :</strong> <?= htmlspecialchars(date('d/m/Y', strtotime($tournoi['date_debut']))) ?>
                                            </p>
                                            <p class='card-text mb-0'>
                                                <strong>Fin :</strong> <?= htmlspecialchars(date('d/m/Y', strtotime($tournoi['date_fin']))) ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class='card-footer bg-light text-center'>
                                        <div class="d-flex gap-2 justify-content-center flex-wrap">
                                            <?php
                                            if ($is_registered): ?>
                                                <button class="btn btn-outline-danger btn-sm desinscrire-btn" data-id="<?= $tournoi['id_tournoi'] ?>">
                                                    <i class="bi bi-x-circle"></i> Se désinscrire
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-outline-warning btn-sm participer-btn" data-id="<?= $tournoi['id_tournoi'] ?>">
                                                    <i class="bi bi-check-circle"></i> Participer
                                                </button>
                                            <?php endif; ?>
                                            <a href="<?= tournois_details ?>?id_tournoi=<?= $tournoi['id_tournoi'] ?>" class="btn btn-danger btn-sm">
                                                <i class="bi bi-info-circle"></i> Plus d'infos
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class='alert alert-info text-center py-4'>
                                <i class="bi bi-info-circle display-4 text-primary mb-3"></i>
                                <h4>Aucun tournoi en cours pour le moment</h4>
                                <p class="mb-0">Revenez bientôt pour découvrir les prochains tournois !</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Magasin -->
        <div id="bande_sepe"></div>
        <div class="container-fluid py-5 bg-secondary">
            <div class="text-center mb-5">
                <a href="<?= magasin_main ?>" class="text-decoration-none category_news_title">
                    <h2 class="display-5 fw-bold mb-3">
                        Magasin
                    </h2>
                    <p class="lead text-muted">Découvrez notre sélection des meilleurs jeux</p>
                </a>
            </div>

            <div class="container">
                <div class="row g-4">
                    <?php if (!empty($games)): ?>
                        <?php foreach ($games as $game): ?>
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="card h-100 shadow-sm border-0">
                                    <div class="position-relative">
                                        <?php if (!empty($game['image'])): ?>
                                            <img src="../back-office/uploads/<?= htmlspecialchars($game['image']) ?>"
                                                class="card-img-top"
                                                alt="<?= htmlspecialchars($game['nom']) ?>"
                                                style="height: 200px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;">
                                                <i class="bi bi-image text-white display-4"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge bg-primary fs-6"><?= ($game['prix'] != 0 ? htmlspecialchars($game['prix']) . ' €' : 'Gratuit') ?></span>
                                        </div>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title fw-bold text-center"><?= htmlspecialchars($game['nom']) ?></h5>
                                        <div class="mt-auto">
                                            <div class="d-grid gap-2">
                                                <a href="<?= magasin_game ?>?id=<?= $game['id_jeu'] ?>"
                                                    class="btn btn-outline-primary">
                                                    Voir détails
                                                </a>
                                                <button class="btn btn-success btn-add-to-cart" data-id="<?= $game['id_jeu'] ?>">
                                                    Ajouter au panier
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-info text-center py-4">
                                <i class="bi bi-shop display-4 text-success mb-3"></i>
                                <h4>Aucun jeu disponible pour le moment</h4>
                                <p class="mb-0">Notre catalogue sera bientôt mis à jour !</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- actualités -->
        <div id="bande_sepe"></div>
        <div class="container-fluid py-5">
            <div class="text-center mb-5">
                <a href="<?= actualite_main ?>" class="text-decoration-none category_news_title">
                    <h2 class="display-5 fw-bold mb-3">
                        Actualités
                    </h2>
                    <p class="lead text-muted">Restez informé des dernières nouvelles gaming</p>
                </a>
            </div>

            <div class="container">
                <div class="row g-4">
                    <?php if (!empty($news)): ?>
                        <?php foreach ($news as $new): ?>
                            <div class="col-lg-6 col-md-12">
                                <div class="mb-3">
                                    <a href="<?= actualite_article ?>?id=<?= $new['id_news'] ?>&category=alaune"
                                        class="text-decoration-none">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-header bg-info text-white">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge bg-light text-dark">
                                                        À la une
                                                    </span>
                                                    <small>
                                                        <i class="bi bi-calendar3 me-1"></i>
                                                        <?= $new['date_article'] ?>
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <h4 class="card-title text-dark fw-bold mb-3">
                                                    <?= htmlspecialchars($new['titre']) ?>
                                                </h4>
                                                <div class="mb-3">
                                                    <span class="text-muted">
                                                        <i class="bi bi-person-circle me-1"></i>
                                                        <strong class="text-dark"><?= htmlspecialchars($new['pseudo']) ?></strong>
                                                    </span>
                                                </div>
                                                <?php
                                                $maxLength = 150;
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
                                                <p class="card-text text-dark lh-lg"><?= nl2br($contentShow) ?></p>
                                            </div>
                                            <div class="card-footer bg-transparent">
                                                <div class="text-end">
                                                    <span class="text-primary">
                                                        Lire la suite <i class="bi bi-arrow-right"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-info text-center py-4">
                                <h4>Aucune actualité disponible</h4>
                                <p class="mb-0">Les dernières nouvelles seront publiées bientôt !</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($news)): ?>
                    <div class="text-center mt-5">
                        <a href="<?= actualite_main ?>" class="btn btn-info btn-lg">
                            Voir toutes les actualités
                            <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <?php include('include/footer.php'); ?>
</body>

</html>