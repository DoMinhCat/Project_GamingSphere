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

    <?php
    include('../include/database.php');

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
                    echo "<div class='col'>
                            <div class='card h-100 shadow-sm'>
                                <div class='card-body'>
                                    <h5 class='card-title'>" . htmlspecialchars($tournoi['nom_tournoi']) . "</h5>
                                    <p class='card-text'><strong>Jeu :</strong> " . htmlspecialchars($tournoi['jeu']) . "</p>
                                    <p class='card-text'><strong>Date de Début :</strong> " . htmlspecialchars($tournoi['date_debut']) . "</p>
                                    <p class='card-text'><strong>Date de Fin :</strong> " . htmlspecialchars($tournoi['date_fin']) . "</p>
                                </div>
                                <div class='card-footer text-center'>
                                    <a href='modify_tournoi.php?id_tournoi=" . $tournoi['id_tournoi'] . "' class='btn btn-warning btn-sm'>Participer !</a>
                                    <a href='tournois_details.php?id_tournoi=" . $tournoi['id_tournoi'] . "' class='btn btn-danger btn-sm'>Plus d'informations !</a>
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
    afficherTournoisParStatut($bdd, 'terminé', 'Tournois terminés');
    afficherTournoisParStatut($bdd, 'en attente', 'Tournois en attente');
    ?>
  </main>

  <?php include('../include/footer.php'); ?>
</body>

</html>