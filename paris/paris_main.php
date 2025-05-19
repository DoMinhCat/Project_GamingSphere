<?php
session_start();
require('../include/database.php');
require('../include/check_session.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';

$rencontres = $bdd->query("SELECT * FROM tournoi WHERE status_ENUM = 'en cours'")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Paris';
$pageCategory = 'paris';
echo "<script>const pageCategory = '$pageCategory';</script>";
require('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}
?>
<body>
<?php include('../include/header.php'); ?>
<div class="container mt-4">
    <h1 class="mb-4">Matchs e-sport en cours</h1>
    <?php if (isset($_GET['message'])): ?>
        <div class="alert alert-info"><?= htmlspecialchars($_GET['message']) ?></div>
    <?php endif; ?>

    <?php if (empty($rencontres)): ?>
        <p>Aucun match en cours.</p>
    <?php endif; ?>

   <?php foreach ($rencontres as $tournoi): ?>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">
                <?= htmlspecialchars($tournoi['nom_tournoi']) ?> (<?= htmlspecialchars($tournoi['type']) ?>)
            </h5>
            <form method="post" action="parier.php" class="row g-2 align-items-center">
                <input type="hidden" name="id_tournoi" value="<?= $tournoi['id_tournoi'] ?>">
                <input type="hidden" name="type_pari" value="<?= htmlspecialchars($tournoi['type']) ?>">
                <?php
                if ($tournoi['type'] === 'equipe') {
                    // Récupérer les équipes inscrites à ce tournoi
                    $stmt = $bdd->prepare("
                        SELECT e.id_equipe, e.nom 
                        FROM inscription_tournoi it
                        JOIN equipe e ON it.id_team = e.id_equipe
                        WHERE it.id_tournoi = ?
                    ");
                    $stmt->execute([$tournoi['id_tournoi']]);
                    $equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($equipes as $equipe) {
                        ?>
                        <div class="col-auto">
                            <label>
                                <input type="radio" name="choix" value="<?= $equipe['id_equipe'] ?>" required>
                                <?= htmlspecialchars($equipe['nom']) ?>
                            </label>
                        </div>
                        <?php
                    }
                } else {
                    // Récupérer les joueurs inscrits à ce tournoi
                    $stmt = $bdd->prepare("
                        SELECT u.id_utilisateurs, u.pseudo 
                        FROM inscription_tournoi it
                        JOIN utilisateurs u ON it.user_id = u.id_utilisateurs
                        WHERE it.id_tournoi = ?
                    ");
                    $stmt->execute([$tournoi['id_tournoi']]);
                    $joueurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($joueurs as $joueur) {
                        ?>
                        <div class="col-auto">
                            <label>
                                <input type="radio" name="choix" value="<?= $joueur['id_utilisateurs'] ?>" required>
                                <?= htmlspecialchars($joueur['pseudo']) ?>
                            </label>
                        </div>
                        <?php
                    }
                }
                ?>
                <div class="col-auto">
                    <input type="number" name="montant" min="1" class="form-control" placeholder="Montant (€)" required>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Parier</button>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>
</div>
</body>
</html>