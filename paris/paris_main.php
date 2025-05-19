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

    <?php foreach ($rencontres as $match): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">
                    <?php
                    if ($match['type'] === 'equipe') {
                        
                        $eq1 = $bdd->prepare("SELECT nom_equipe FROM equipe WHERE id_equipe = ?");
                        $eq1->execute([$match['id_equipe1']]);
                        $equipe1 = $eq1->fetchColumn();

                        $eq2 = $bdd->prepare("SELECT nom_equipe FROM equipe WHERE id_equipe = ?");
                        $eq2->execute([$match['id_equipe2']]);
                        $equipe2 = $eq2->fetchColumn();

                        echo htmlspecialchars($equipe1) . " vs " . htmlspecialchars($equipe2);
                    } else {
                        
                        $pl1 = $bdd->prepare("SELECT pseudo FROM utilisateurs WHERE id_utilisateurs = ?");
                        $pl1->execute([$match['id_joueur1']]);
                        $joueur1 = $pl1->fetchColumn();

                        $pl2 = $bdd->prepare("SELECT pseudo FROM utilisateurs WHERE id_utilisateurs = ?");
                        $pl2->execute([$match['id_joueur2']]);
                        $joueur2 = $pl2->fetchColumn();

                        echo htmlspecialchars($joueur1) . " vs " . htmlspecialchars($joueur2);
                    }
                    ?>
                </h5>
                <form method="post" action="parier.php" class="row g-2 align-items-center">
                    <input type="hidden" name="id_match" value="<?= $match['id_rencontre'] ?>">
                    <input type="hidden" name="type_pari" value="<?= htmlspecialchars($match['type']) ?>">
                    <?php if ($match['type'] === 'equipe'): ?>
                        <div class="col-auto">
                            <label>
                                <input type="radio" name="choix" value="<?= $match['id_equipe1'] ?>" required>
                                <?= htmlspecialchars($equipe1) ?>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label>
                                <input type="radio" name="choix" value="<?= $match['id_equipe2'] ?>" required>
                                <?= htmlspecialchars($equipe2) ?>
                            </label>
                        </div>
                    <?php else: ?>
                        <div class="col-auto">
                            <label>
                                <input type="radio" name="choix" value="<?= $match['id_joueur1'] ?>" required>
                                <?= htmlspecialchars($joueur1) ?>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label>
                                <input type="radio" name="choix" value="<?= $match['id_joueur2'] ?>" required>
                                <?= htmlspecialchars($joueur2) ?>
                            </label>
                        </div>
                    <?php endif; ?>
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