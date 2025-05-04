<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/database.php');
require('../../include/check_timeout.php');
require_once __DIR__ . '/../../path.php';
?>
<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Ajouter un tournoi';
require('../head.php');
?>

<body class="pb-4">
    <?php
    $page = tournois_back;
    include('../navbar.php'); ?>

    <div class="container my-5">
        <h1 class="mb-5 text-center">Ajouter un Nouveau Tournoi</h1>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom_tournoi = $_POST['nom_tournoi'] ?? '';
            $date_debut = $_POST['date_debut'] ?? '';
            $date_fin = $_POST['date_fin'] ?? '';
            $jeu = $_POST['jeu'] ?? '';
            $statut = $_POST['statut'] ?? '';
            $type_tournoi = $_POST['type_tournoi'] ?? '';
            if (empty($nom_tournoi) || empty($date_debut) || empty($date_fin) || empty($jeu) || empty($statut) || empty($type_tournoi)) {
                echo "<div class='alert alert-danger'>Tous les champs sont obligatoires.</div>";
            } else {
                try {
                    $stmt = $bdd->prepare("INSERT INTO tournoi (nom_tournoi, date_debut, date_fin, jeu, status_ENUM, type) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$nom_tournoi, $date_debut, $date_fin, $jeu, $statut, $type_tournoi]);
                    header('Location:' . tournois_back . '?message=tournoi_added');
                    exit();
                } catch (PDOException $e) {
                    $_SESSION['error'] = htmlspecialchars($e->getMessage());
                    header('Location:' . tournois_back . '?message=error');
                    exit();
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
            <div class="mb-4">
                <label for="type_tournoi" class="form-label">Type de Tournoi</label>
                <select class="form-select" id="type_tournoi" name="type_tournoi" required>
                    <option value="Solo">Solo</option>
                    <option value="Équipe">Équipe</option>
                </select>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Ajouter</button>
                <a href="<?= tournois_back ?>" class="btn btn-secondary">Retour</a>
            </div>
        </form>
    </div>
</body>

</html>