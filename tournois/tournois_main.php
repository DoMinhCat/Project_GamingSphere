<?php
session_start();
require('../include/database.php');
require('../include/check_timeout.php');
?>

<!DOCTYPE html>
<html lang="fr">

<?php
$title = 'Tournois';
include('../include/head.php');
?>

<body>
  <?php include('../include/header.php'); ?>

  <main class="container my-5">
    <h1 class="mb-4 text-center">Liste des Tournois</h1>

    <!-- Gestion des messages -->
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
        <?php elseif ($_GET['message'] === 'missing_id'): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                ID du tournoi manquant.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Boutons pour gérer les équipes -->
    <div class="mb-4 text-center">
        <?php
        $user_id = $_SESSION['user_id'] ?? null;

        if ($user_id) {
            // Vérifier si l'utilisateur a rejoint une équipe
            $stmt = $bdd->prepare("SELECT e.id_équipe, e.nom FROM membres_equipe me JOIN equipe e ON me.id_equipe = e.id_équipe WHERE me.id_utilisateur = ?");
            $stmt->execute([$user_id]);
            $team = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($team): ?>
                <a href="../team/team_details.php?id_equipe=<?= $team['id_equipe'] ?>" class="btn btn-primary">
                    Voir les détails de votre équipe : <?= htmlspecialchars($team['nom_equipe']) ?>
                </a>
            <?php else: ?>
                <a href="../team/join_team.php" class="btn btn-success">Rejoindre une équipe</a>
                <a href="../team/create_team.php" class="btn btn-secondary">Créer une équipe</a>
            <?php endif;
        } else {
            echo "<div class='alert alert-warning'>Vous devez être connecté pour gérer vos équipes.</div>";
        }
        ?>
    </div>

    <?php
    // Fonction pour afficher les tournois par statut sous forme de cartes
    function afficherTournoisParStatut($bdd, $statut, $titre)
    {
        try {
            $stmt = $bdd->prepare("SELECT id_tournoi, nom_tournoi, date_debut, date_fin, jeu FROM tournoi WHERE status_ENUM = ? ORDER BY date_debut DESC");
            $stmt->execute([$statut]);
            $tournois = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo "<h2 class='mt-5'>$titre</h2>";

            if (count($tournois) > 0) {
                echo "<div class='row row-cols-1 row-cols-md-3 g-4'>"; // Grille Bootstrap
                foreach ($tournois as $tournoi) {
                    $user_id = $_SESSION['user_id'] ?? null; // ID de l'utilisateur connecté
                    $is_registered = false;
                    if ($user_id) {
                        $check_stmt = $bdd->prepare("SELECT COUNT(*) FROM inscription_tournoi WHERE id_tournoi = ? AND user_id = ?");
                        $check_stmt->execute([$tournoi['id_tournoi'], $user_id]);
                        $is_registered = $check_stmt->fetchColumn() > 0;
                    }

                    echo "<div class='col'>
                            <div class='card h-100 shadow-sm'>
                                <div class='card-body'>
                                    <h5 class='card-title'>" . htmlspecialchars($tournoi['nom_tournoi']) . "</h5>
                                    <p class='card-text'><strong>Jeu :</strong> " . htmlspecialchars($tournoi['jeu']) . "</p>
                                    <p class='card-text'><strong>Date de Début :</strong> " . htmlspecialchars($tournoi['date_debut']) . "</p>
                                    <p class='card-text'><strong>Date de Fin :</strong> " . htmlspecialchars($tournoi['date_fin']) . "</p>
                                </div>
                                <div class='card-footer text-center'>";
                                if ($is_registered): ?>
                                    <button class="btn btn-outline-danger desinscrire-btn" data-id="<?= $tournoi['id_tournoi'] ?>">
                                        <i class="bi bi-x-circle"></i> Se désinscrire
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-outline-warning participer-btn" data-id="<?= $tournoi['id_tournoi'] ?>">
                                        <i class="bi bi-check-circle"></i> Participer
                                    </button>
                                <?php endif;
                                echo "<a href='tournois_details.php?id_tournoi=" . $tournoi['id_tournoi'] . "' class='btn btn-danger btn-sm'>Plus d'informations !</a>
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

    afficherTournoisParStatut($bdd, 'en cours', 'Tournois en cours');
    afficherTournoisParStatut($bdd, 'en attente', 'Tournois en attente');
    afficherTournoisParStatut($bdd, 'terminé', 'Tournois terminés');
    ?>
  </main>

  <?php include('../include/footer.php'); ?>
<script src="fluid.js"></script>
</body>

</html>