<?php
session_start();
require('../include/check_timeout.php');
require('../include/database.php');
require_once __DIR__ . '/../path.php';
?>
<!DOCTYPE html>
<html lang="fr">

<?php
$title = 'Détail de l\'actualité';
require('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}
?>

<body>
    <?php
    include("../include/header.php");
    ?>
    <main class="container my-5">
        <?php
        try {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id_article = (int)$_GET['id'];

                $query = "SELECT * FROM news WHERE id_news = :id";
                $stmt = $bdd->prepare($query);
                $stmt->bindParam(':id', $id_article, PDO::PARAM_INT);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $article = $stmt->fetch();

                    echo "<h1 class=\"text-center mb-3\">" . htmlspecialchars($article['titre']) . "</h1>";
                    echo "<p><strong>Publié le :</strong> " . $article['date_article'] . "</p>";
                    echo "<p class=\"mt-5\">" . nl2br(htmlspecialchars($article['contenue'])) . "</p>";
                } else {
                    echo "<p>Aucun article trouvé.</p>";
                }
            } else {
                echo "<p>Pas d'article sélectionné.</p>";
            }
        } catch (PDOException $e) {
            echo "Erreur de connexion à la base de données : " . $e->getMessage();
        }
        ?>
        <main class="container my-5">

            <?php
            include("../include/footer.php");
            ?>
</body>

</html>