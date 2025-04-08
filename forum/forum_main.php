<?php
session_start();
require('../include/database.php');
require('../include/check_timeout.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Forum';
require('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}


if (!isset($bdd)) {
    die("Erreur de connexion à la base de données");
}
?>

<body>
    <?php include("../include/header.php"); ?>

    <div class="container my-5">
        <h1 class="mb-4">Forum - Catégories</h1>

        <div class="mb-4 text-end">
        <a href="nouveau_sujet.php" class="btn btn-primary">Ajouter un sujet</a>
    </div>

        <?php
        $query = $bdd->query("SELECT DISTINCT catégories FROM messages WHERE parent_id IS NULL");
        if (!$query) {
            die("Erreur dans la requête SQL: " . $bdd->errorInfo()[2]);
        }

        while ($row = $query->fetch()) {
            $categorie = $row['catégories'];

            $stmt = $bdd->prepare("SELECT COUNT(*) FROM messages WHERE catégories = ? AND parent_id IS NULL");
            $stmt->execute([$categorie]);
            $nb_sujets = $stmt->fetchColumn();

            $stmt = $bdd->prepare("SELECT id_message FROM messages WHERE catégories = ? AND parent_id IS NULL");
            $stmt->execute([$categorie]);
            $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $nb_messages = 0;
            if (!empty($ids)) {
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $stmt = $bdd->prepare("SELECT COUNT(*) FROM reponses_forum WHERE id_sujet IN ($placeholders)");
                $stmt->execute($ids);
                $nb_messages = $stmt->fetchColumn();
            }
        ?>
            <div class="card mb-3 shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-1">
                            <a href="categorie.php?nom=<?= urlencode($categorie) ?>" class="text-decoration-none text-dark">
                                <?= htmlspecialchars($categorie) ?>
                            </a>
                        </h5>
                    </div>
                    <div class="text-end">
                        <div><strong><?= $nb_sujets ?></strong> sujets</div>
                        <div><strong><?= $nb_messages ?></strong> messages</div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <?php include("../include/footer.php"); ?>
</body>

</html>