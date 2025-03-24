<?php

include('database.php');

$query = $_POST['query'] ?? '';
$category = $_POST['category'] ?? '';  


$users = $articles = $games = [];

try {
  
    if ($category == 'users' || empty($category)) {
        $stmtUsers = $bdd->prepare("
            SELECT pseudo, nom, prenom 
            FROM utilisateurs 
            WHERE pseudo LIKE ? OR nom LIKE ? OR prenom LIKE ?");
        $stmtUsers->execute(['%' . $query . '%', '%' . $query . '%', '%' . $query . '%']);
        $users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);
    }

    if ($category == 'articles' || empty($category)) {
        $stmtArticles = $bdd->prepare("SELECT titre FROM news WHERE titre LIKE ? OR contenue LIKE ?");
        $stmtArticles->execute(['%' . $query . '%', '%' . $query . '%']);
        $articles = $stmtArticles->fetchAll(PDO::FETCH_ASSOC);
    }

    if ($category == 'games' || empty($category)) {
        $stmtGames = $bdd->prepare("SELECT nom FROM jeu WHERE nom LIKE ?");
        $stmtGames->execute(['%' . $query . '%']);
        $games = $stmtGames->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {

    $errorMessage = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<?php $title = 'Recherche';
include('head.php');
include('header.php'); 
?>
<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Résultats de recherche pour: <?php echo htmlspecialchars($query); ?></h1>
        
        <?php if (isset($errorMessage)): ?>
            <div class="alert alert-danger" role="alert">
                Une erreur est survenue : <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>


        <?php if ($users): ?>
            <div class="section mb-4">
            <h3 class="search-title">Utilisateurs</h3>
            <ul class="list-group">
                <?php foreach ($users as $user): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?php echo htmlspecialchars($user['pseudo']); ?>
                    <a href="/PA/profil/profil.php?user=<?php echo urlencode($user['pseudo']); ?>" class="btn btn-primary btn-sm">Voir le profil</a>
                </li>
                <?php endforeach; ?>
            </ul>
            </div>
        <?php endif; ?>


        <?php if ($articles): ?>
            <div class="section mb-4">
            <h3 class="search-title">Articles</h3>
            <ul class="list-group">
                <?php foreach ($articles as $article): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?php echo htmlspecialchars($article['titre']); ?>
                    <a href="article.php?title=<?php echo urlencode($article['titre']); ?>" class="btn btn-primary btn-sm">Lire l'article</a>
                </li>
                <?php endforeach; ?>
            </ul>
            </div>
        <?php endif; ?>


        <?php if ($games): ?>
            <div class="section mb-4">
            <h3 class="search-title">Jeux</h3>
            <ul class="list-group">
                <?php foreach ($games as $game): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?php echo htmlspecialchars($game['nom']); ?>
                    <a href="game.php?name=<?php echo urlencode($game['nom']); ?>" class="btn btn-primary btn-sm">Voir le jeu</a>
                </li>
                <?php endforeach; ?>
            </ul>
            </div>
        <?php endif; ?>


        <?php if (empty($users) && empty($articles) && empty($games)): ?>
            <div class="alert alert-info" role="alert">
            Aucun résultat trouvé pour votre recherche.
            </div>
        <?php endif; ?>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
