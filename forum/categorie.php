<?php
session_start();
require('../include/database.php');
require('../include/check_timeout.php');
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Forum - catégorie';
require('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}

if (!isset($bdd)) {
    die("Erreur de connexion à la base de données");
}

// Vérification de la catégorie dans l'URL
if (!isset($_GET['nom']) || empty($_GET['nom'])) {
    die("Catégorie non spécifiée.");
}

$categorie_nom = $_GET['nom'];
?>

<body>
    <?php include("../include/header.php"); ?>

    <div class="container my-5">
        <h2 class="mb-4">Catégorie : <?= htmlspecialchars($categorie_nom) ?></h2>

        <a href="nouveau_sujet.php?categorie=<?= urlencode($categorie_nom) ?>" class="btn btn-primary mb-4">+ Nouveau sujet</a>

        <?php
        // Récupération des sujets de la catégorie
        $stmt = $bdd->prepare("SELECT * FROM forum_sujets WHERE categories = ? AND parent_id IS NULL ORDER BY date_msg DESC");
        $stmt->execute([$categorie_nom]);
        $sujets = $stmt->fetchAll();

        if (count($sujets) === 0) {
            echo "<p class='text-muted'>Aucun sujet dans cette catégorie pour le moment.</p>";
        }

        foreach ($sujets as $sujet) {
            // Compter les réponses
            $stmt_reponses = $bdd->prepare("SELECT COUNT(*) FROM forum_reponses WHERE id_sujet = ?");
            $stmt_reponses->execute([$sujet['id_sujet']]);
            $nb_reponses = $stmt_reponses->fetchColumn();
        ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5>
                        <a href="sujet.php?id=<?= $sujet['id_sujet'] ?>" class="text-decoration-none">
                            <?= htmlspecialchars($sujet['titre']) ?>
                        </a>
                    </h5>
                    <p class="text-muted mb-1">Posté le <?= date("d/m/Y à H:i", strtotime($sujet['date_msg'])) ?> par <?= htmlspecialchars($sujet['auteur'] ?? 'Anonyme') ?></p>
                    <p class="mb-0"><strong><?= $nb_reponses ?></strong> réponse<?= $nb_reponses != 1 ? 's' : '' ?></p>
                </div>
            </div>
        <?php } ?>
    </div>

    <?php include("../include/footer.php"); ?>
</body>

</html>