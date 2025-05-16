<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require_once('../../include/database.php');
require('../../include/check_timeout.php');
require_once __DIR__ . '/../../path.php';
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Modification de tournois';
require('../head.php');
?>

<body class="pb-4">
    <?php
    $page = tournois_back;
    include('../navbar.php');
    ?>
    <main class="container my-5">
        <h1 class="my-5 text-center">Modifier un tournoi</h1>
        <?php
        if (empty($_GET['id_tournoi'])) {
            header('Location:' . tournois_back . '?error=missing_id');
            exit();
        }
        $id_tournoi = $_GET['id_tournoi'];
        try {
            $stmt = $bdd->prepare("SELECT * from tournoi WHERE id_tournoi=?;");
            $stmt->execute([$id_tournoi]);
            $tournois = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            header('Location:' . tournois_back . '?error=db');
            exit();
        }
        ?>
        <form method="POST" action="update_tournoi.php" class="p-4 border rounded shadow-sm bg-light">
            <input type="hidden" name="id_tournoi" value="<?= $id_tournoi ?>">
            <div class="mb-3">
                <label for="nom_tournoi" class="form-label">Nom du Tournoi</label>
                <input type="text" class="form-control" id="nom_tournoi" name="nom_tournoi" value="<?= $tournois['nom_tournoi'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="date_debut" class="form-label">Date de Début</label>
                <input type="date" class="form-control" id="date_debut" name="date_debut" value="<?= $tournois['date_debut'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="date_fin" class="form-label">Date de Fin</label>
                <input type="date" class="form-control" id="date_fin" name="date_fin" value="<?= $tournois['date_fin'] ?> required" required>
            </div>
            <div class="mb-3">
                <label for="jeu" class="form-label">Jeu</label>
                <input type="text" class="form-control" id="jeu" name="jeu" value="<?= $tournois['jeu'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="statut" class="form-label">Statut</label>
                <select class="form-select" id="statut" name="statut" value="<?= $tournois['statut_ENUM'] ?>" required>
                    <option value="En attente">En attente</option>
                    <option value="En cours">En cours</option>
                    <option value="Terminé">Terminé</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="type_tournoi" class="form-label">Type de Tournoi</label>
                <select class="form-select" id="type_tournoi" name="type_tournoi" value="<?= $tournois['type'] ?>" required>
                    <option value="Solo">Solo</option>
                    <option value="Équipe">Équipe</option>
                </select>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a href="<?= tournois_back ?>" class="btn btn-secondary">Retour</a>
            </div>
        </form>
    </main>
</body>

</html>