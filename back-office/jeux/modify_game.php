<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/check_timeout.php');
require('../../include/database.php');
require_once __DIR__ . '/../../path.php';



if (isset($_GET['id'])) {
    $gameId = $_GET['id'];
    try {
        $stmt = $bdd->prepare("SELECT * FROM jeu WHERE id_jeu = ?");
        $stmt->execute([$gameId]);
        $game = $stmt->fetch();

        if (!$game) {
            header('Location: ' . jeux_back . '?error=id_invalid');
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = htmlspecialchars($e->getMessage());
        header('Location:' . jeux_back . '?error=bdd');
        exit();
    }
} else {
    header('Location: ' . jeux_back . '?error=id_invalid');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Modifier un jeu';
require('../head.php');
?>

<body>
    <?php
    $page = jeux_back;
    include('../navbar.php');
    ?>
    <main class="container mb-5">
        <?php if (!empty($_GET['message'])) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif ?>
        <h1 class="text-center mt-5 mb-4">Modifier le jeu</h1>
        <?php if (isset($game)): ?>
            <form action="update_jeux.php" method="POST" enctype="multipart/form-data" class="p-4 border rounded shadow-sm bg-light">
                <input type="hidden" name="gameId" value="<?php echo htmlspecialchars($game['id_jeu'] ?? ''); ?>">

                <div class="mb-3">
                    <label for="gameName" class="form-label">Nom du jeu</label>
                    <input type="text" class="form-control" id="gameName" name="gameName" value="<?php echo htmlspecialchars($game['nom'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="category" class="form-label">Catégorie</label>
                    <input type="text" class="form-control" id="category" name="category" value="<?php echo htmlspecialchars($game['catégorie'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="releaseDate" class="form-label">Date de sortie</label>
                    <input type="date" class="form-control" id="releaseDate" name="releaseDate" value="<?php echo htmlspecialchars($game['date_sortie'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="gameRating" class="form-label">Note du jeu</label>
                    <input type="number" step="0.1" class="form-control" id="gameRating" name="gameRating" value="<?php echo htmlspecialchars($game['note_jeu'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="platform" class="form-label">Plateforme</label>
                    <input type="text" class="form-control" id="platform" name="platform" value="<?php echo htmlspecialchars($game['plateforme'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="gamePrice" class="form-label">Prix</label>
                    <input type="number" step="0.01" class="form-control" id="gamePrice" name="gamePrice" value="<?php echo htmlspecialchars($game['prix'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="gameType" class="form-label">Type</label>
                    <input type="text" class="form-control" id="gameType" name="gameType" value="<?php echo htmlspecialchars($game['type'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="gamePublisher" class="form-label">Éditeur</label>
                    <input type="text" class="form-control" id="gamePublisher" name="gamePublisher" value="<?php echo htmlspecialchars($game['éditeur'] ?? ''); ?>" required>
                </div>

                <div class="mb-2">
                    <label for="gameDescription" class="form-label">Description:</label>
                    <input type="text" id="gameDescription" name="gameDescription" class="form-control" value="<?php echo htmlspecialchars($game['description'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="gameImage" class="form-label">Image du jeu (png, gif, jpeg autorisé)</label>
                    <input type="file" class="form-control" id="gameImage" name="gameImage">
                    <?php if (!empty($game['image'])): ?>
                        <p class="mt-2">Image actuelle : <img src="../uploads/<?php echo htmlspecialchars($game['image']); ?>" alt="Image du jeu" class="img-thumbnail" style="max-width: 100%;"></p>
                    <?php endif; ?>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Modifier le jeu</button>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-danger text-center">Aucune donnée pour ce jeu.</div>
        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>