<?php
session_start();
require('../include/check_timeout.php');
require('../include/database.php');
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

// Connexion à la base de données
try {
    // Vérifier si un ID d'article est passé dans l'URL
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $id_article = (int)$_GET['id'];

        // Requête pour récupérer l'article spécifique
        $query = "SELECT * FROM news WHERE id_news = :id";
        $stmt = $bdd->prepare($query);
        $stmt->bindParam(':id', $id_article, PDO::PARAM_INT);
        $stmt->execute();

        // Vérifier si l'article existe
        if ($stmt->rowCount() > 0) {
            $article = $stmt->fetch();

            // Affichage de l'article
            echo "<h1>" . htmlspecialchars($article['titre']) . "</h1>";
            echo "<p><strong>Publié le :</strong> " . $article['date_article'] . "</p>";
            echo "<p>" . nl2br(htmlspecialchars($article['contenue'])) . "</p>";
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

<?php
include("../include/footer.php");
?>
</body>

</html>
