<?php 
include('../../include/database.php');
include ('../navbar.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gameId'])) {
    $gameId = $_POST['gameId'];
    $category = $_POST['category'];
    $releaseDate = $_POST['releaseDate'];
    $gameName = $_POST['gameName'];
    $gameRating = $_POST['gameRating'];
    $platform = $_POST['platform'];
    $gamePrice = $_POST['gamePrice'];
    $gameType = $_POST['gameType'];
    $gamePublisher = $_POST['gamePublisher'];
    $gameDescription = $_POST['gameDescription'];

    $imagePath = null;

    if (isset($_FILES['gameImage']) && $_FILES['gameImage']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        $filename = basename($_FILES['gameImage']['name']);
        $imagePath = $uploadDir . $filename;
        
if (file_exists($filepath)) {
    $filename = uniqid() . "_" . $filename; 
}

        if (move_uploaded_file($_FILES['gameImage']['tmp_name'], $imagePath)) {
            $stmt = $bdd->prepare("SELECT image FROM jeu WHERE id_jeu = ?");
            $stmt->execute([$gameId]);
            $oldImage = $stmt->fetchColumn();

            if (!empty($oldImage) && file_exists($uploadDir . $oldImage)) {
                unlink($uploadDir . $oldImage);
            }

            $imagePath = $filename;
        } else {
            echo "<p class='text-danger'>Erreur lors de l'upload de l'image.</p>";
        }
    } else {
        $stmt = $bdd->prepare("SELECT image FROM jeu WHERE id_jeu = ?");
        $stmt->execute([$gameId]);
        $imagePath = $stmt->fetchColumn();
    }

    $query = "UPDATE jeu SET 
              nom = ?, catégorie = ?, date_sortie = ?, note_jeu = ?, plateforme = ?, prix = ?, type = ?, éditeur = ?, description = ?;";

    if ($imagePath) {
        $query .= ", image = ?";
    }

    $query .= " WHERE id_jeu = ?";

    $params = [
        $gameName,
        $category,
        $releaseDate,
        $gameRating,
        $platform,
        $gamePrice,
        $gameType,
        $gamePublisher,
        $gameDescription,
    ];

    if ($imagePath) {
        $params[] = $imagePath; 
    }

    $params[] = $gameId; 

    try {
        $stmt = $bdd->prepare($query);
        $stmt->execute($params);
        echo "<div class='alert alert-success text-center'>Jeu modifié avec succès !</div>";
        header("Location: jeux.php?message=updated");
        exit();
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger text-center'>Erreur lors de la modification du jeu : " . $e->getMessage() . "</div>";
    }
}

if (isset($_GET['id'])) {
    $gameId = $_GET['id'];
    $stmt = $bdd->prepare("SELECT * FROM jeu WHERE id_jeu = ?");
    $stmt->execute([$gameId]);
    $game = $stmt->fetch();

    if (!$game) {
        echo "<div class='alert alert-danger text-center'>Jeu introuvable !</div>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un jeu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Modifier le jeu</h2>
        <?php if (isset($game)): ?>
    <form action="modify_game.php" method="POST" enctype="multipart/form-data" class="p-4 border rounded shadow-sm bg-light">
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
            <label for="gameImage" class="form-label">Image du jeu</label>
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
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
