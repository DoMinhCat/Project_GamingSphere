<?php
session_start();
require('../include/database.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';
?>
<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Tournois';
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
        <h1 class="mb-4 text-center">Liste des Tournois</h1>
        <?php if (isset($_GET['message'])): ?>
            <?php if ($_GET['message'] === 'registration_success'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Vous êtes inscrit au tournoi avec succès !
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif ($_GET['message'] === 'already_registered'): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    Vous êtes déjà inscrit à ce tournoi.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif ($_GET['message'] === 'bdd'): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    Erreur de la base de données, veuillez réessayer plus tard ! <?= $_GET['err'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif ($_GET['message'] === 'missing_id'): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    ID du tournoi manquant.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif ?>
        <div class="mb-4 mt-2 text-center">
            <?php
            $user_id = $_SESSION['user_id'] ?? null;

            if ($user_id) {
                $stmt = $bdd->prepare("
            SELECT e.id_equipe AS id_equipe, e.nom AS nom_equipe
            FROM membres_equipe me
            JOIN equipe e ON me.id_equipe = e.id_equipe
            WHERE me.id_utilisateur = ?
        ");
                $stmt->execute([$user_id]);
                $team = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($team && isset($team['id_equipe'], $team['nom_equipe'])): ?>
                    <a href="<?= team_details ?>?id_equipe=<?= htmlspecialchars($team['id_equipe']) ?>" class="btn btn-primary mb-2">
                        Voir les détails de votre équipe : <?= htmlspecialchars($team['nom_equipe']) ?>
                    </a>
                    <a href="<?= create_team ?>" class="btn btn-warning mb-2">Créer une autre équipe</a>
                    <a href="<?= team_list ?>" class="btn btn-primary mb-2">Voir les équipes</a>
                <?php else: ?>
                    <a href="<?= create_team ?>" class="btn btn-warning">Créer une équipe</a>
                    <a href="<?= team_list ?>" class="btn btn-primary">Voir les équipes</a>
            <?php endif;
            }
            ?>
        </div>
        <?php
        function afficherTournois($bdd, $type_tournoi, $statut, $titre, $category)
        {
            try {
                $stmt = $bdd->prepare("
                SELECT id_tournoi, nom_tournoi, date_debut, date_fin, jeu 
                FROM tournoi 
                WHERE type = ? AND status_ENUM = ? 
                ORDER BY date_debut DESC LIMIT 6;
            ");
                $stmt->execute([$type_tournoi, $statut]);
                $tournois = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo '<a href="' . tournois_category . '?category=' . $category . '" class="category_news_title">
                    <h1 class="mt-5">' . $titre . '</h1>
                </a>';

                if (count($tournois) > 0) {
                    echo "<div class='row row-cols-1 row-cols-md-3 g-4'>";
                    foreach ($tournois as $tournoi) {
                        $user_id = $_SESSION['user_id'] ?? null;
                        $is_registered = false;
                        if ($user_id) {
                            $check_stmt = $bdd->prepare("SELECT COUNT(*) FROM inscription_tournoi WHERE id_tournoi = ? AND user_id = ?;");
                            $check_stmt->execute([$tournoi['id_tournoi'], $user_id]);
                            $is_registered = $check_stmt->fetchColumn() > 0;
                        }
                        echo "<div class='col'>
                            <div class='card h-100 shadow-sm'>
                                <div class='card-body'>
                                    <h5 class='card-title fw-bold text-center mb-3'>" . htmlspecialchars($tournoi['nom_tournoi']) . "</h5>
                                    <p class='card-text'><strong>Jeu :</strong> " . htmlspecialchars($tournoi['jeu']) . "</p>
                                    <p class='card-text'><strong>Date de Début :</strong> " . htmlspecialchars(date('d/m/Y', strtotime($tournoi['date_debut']))) . "</p>
                                    <p class='card-text'><strong>Date de Fin :</strong> " . htmlspecialchars(date('d/m/Y', strtotime($tournoi['date_fin']))) . "</p>
                                </div>
                                <div class='card-footer text-center'>";
                        if ($is_registered): ?>
                            <button class="btn btn-outline-danger btn-sm desinscrire-btn" data-id="<?= $tournoi['id_tournoi'] ?>">
                                <i class="bi bi-x-circle"></i> Se désinscrire
                            </button>
                        <?php else: ?>
                            <button class="btn btn-outline-warning btn-sm participer-btn" data-id="<?= $tournoi['id_tournoi'] ?>">
                                <i class="bi bi-check-circle"></i> Participer
                            </button>
        <?php endif;
                        echo '<a href="' . tournois_details . '?id_tournoi=' . $tournoi['id_tournoi'] . '" class="btn btn-danger btn-sm">Plus d\'informations</a>';
                        echo "
                                </div>
                            </div>
                          </div>";
                    }
                    echo "</div>";
                } else {
                    echo "<div class='alert alert-info'>Aucun tournoi $titre pour le moment.</div>";
                }
            } catch (PDOException $e) {
                echo "<div class='alert alert-danger'>Erreur lors de la récupération des tournois : " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
        afficherTournois($bdd, 'solo', 'en cours', 'Tournois solo en cours', 'solo_encours');
        afficherTournois($bdd, 'Équipe', 'en cours', 'Tournois en équipe en cours', 'equipe_encours');
        afficherTournois($bdd, 'solo', 'en attente', 'Tournois solo en attente', 'solo_attente');
        afficherTournois($bdd, 'Équipe', 'en attente', 'Tournois en équipe en attente', 'equipe_attente');
        afficherTournois($bdd, 'solo', 'terminé', 'Tournois solo terminés', 'solo_termine');
        afficherTournois($bdd, 'Équipe', 'terminé', 'Tournois en équipe terminés', 'equipe_termine');
        ?>
    </main>

    <?php include('../include/footer.php'); ?>
    <script src="fluid.js"></script>
</body>

</html>