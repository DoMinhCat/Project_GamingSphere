<?php
session_start();
require('../include/check_timeout.php');
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
<?php
include("../include/header.php");

// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=gamingsphère', 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les actualités
    $query = "SELECT * FROM news ORDER BY date_article DESC";
    $stmt = $pdo->query($query);

    echo "<h1>Actualités</h1>";

    // Affichage des actualités
    while ($row = $stmt->fetch()) {
        echo "<div class='article'>";
        echo "<h2><a href='actualite_article.php?id=" . $row['id_news'] . "'>" . htmlspecialchars($row['titre']) . "</a></h2>";
        echo "<p><strong>Publié le :</strong> " . $row['date_article'] . "</p>";
        echo "<p>" . nl2br(htmlspecialchars($row['contenue'])) . "</p>";
        echo "</div>";
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
