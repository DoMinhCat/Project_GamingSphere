<?php
session_start();
require('../include/check_timeout.php');
require('../include/database.php');
require_once __DIR__ . '/../path.php';
?>
<!DOCTYPE html>
<html lang="fr">

<?php
$title = 'Actualités';
require('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}
?>

<body>
    <?php include("../include/header.php"); ?>

    <div class="container my-5">
        <h1 class="text-center mb-4">Actualités</h1>

        <?php
        try {
            $query = "SELECT * FROM news ORDER BY date_article DESC";
            $stmt = $bdd->query($query);

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        ?>
                <a href="actualite_article.php?id=<?= $row['id_news'] ?>" class="articleBlockLink">
                    <div class="article border rounded p-3 mb-4 shadow-sm">
                        <h2>
                            <?= htmlspecialchars($row['titre']) ?>
                        </h2>
                        <p><strong>Publié le :</strong> <?= htmlspecialchars($row['date_article']) ?></p>
                        <p><?= nl2br(htmlspecialchars($row['contenue'])) ?></p>
                    </div>
                </a>
        <?php
            }
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>Une erreur est survenue lors de la récupération des actualités.</div>";
        }
        ?>
    </div>

    <?php include("../include/footer.php"); ?>
</body>

</html>