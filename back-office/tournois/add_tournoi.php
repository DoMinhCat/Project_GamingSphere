<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/database.php');
require('../../include/check_timeout.php');
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Ajouter un tournoi';
require('../head.php');
?>

<body>
    <?php include('../navbar.php'); ?>

    <div class="container my-5">
        <h1 class="mb-4 text-center">Ajouter un Nouveau Tournoi</h1>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $nom_tournoi = $_POST['nom_tournoi'] ?? '';
            $date_debut = $_POST['date_debut'] ?? '';
            $date_fin = $_POST['date_fin'] ?? '';
            $jeu = $_POST['jeu'] ?? '';
            $statut = $_POST['statut'] ?? '';


            if (empty($nom_tournoi) || empty($date_debut) || empty($date_fin) || empty($jeu) || empty($statut)) {
                echo "<div class='alert alert-danger'>Tous les champs sont obligatoires.</div>";
            } else {
                try {

                    $stmt = $bdd->prepare("INSERT INTO tournoi (nom_tournoi, date_debut, date_fin, jeu, status_ENUM) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$nom_tournoi, $date_debut, $date_fin, $jeu, $statut]);
                    header("Location: tournois_main.php?message=tournoi_added");
                    exit();
                } catch (PDOException $e) {
                    echo "<div class='alert alert-danger'>Erreur lors de l'ajout du tournoi : " . htmlspecialchars($e->getMessage()) . "</div>";
                }
            }
        }
        ?>

        <form method="POST" class="p-4 border rounded shadow-sm bg-light">
            <div class="mb-3">
                <label for="nom_tournoi" class="form-label">Nom du Tournoi</label>
                <input type="text" class="form-control" id="nom_tournoi" name="nom_tournoi" required>
            </div>
            <div class="mb-3">
                <label for="date_debut" class="form-label">Date de Début</label>
                <input type="date" class="form-control" id="date_debut" name="date_debut" required>
            </div>
            <div class="mb-3">
                <label for="date_fin" class="form-label">Date de Fin</label>
                <input type="date" class="form-control" id="date_fin" name="date_fin" required>
            </div>
            <div class="mb-3">
                <label for="jeu" class="form-label">Jeu</label>
                <input type="text" class="form-control" id="jeu" name="jeu" required>
            </div>
            <div class="mb-3">
                <label for="statut" class="form-label">Statut</label>
                <select class="form-select" id="statut" name="statut" required>
                    <option value="En attente">En attente</option>
                    <option value="En cours">En cours</option>
                    <option value="Terminé">Terminé</option>
                </select>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Ajouter le Tournoi</button>
                <a href="tournois_main.php" class="btn btn-secondary">Retour</a>
            </div>
        </form>
    </div>

</body>

</html>