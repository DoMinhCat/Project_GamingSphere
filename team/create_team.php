<?php
session_start();
require('../include/database.php');
require('../include/check_timeout.php');
if (!isset($_SESSION['user_id'])) {
    header('Location: ../connexion/login.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teamName = $_POST['team_name'] ?? '';
    $teamLevel = $_POST['team_level'] ?? '';
    $userId = $_SESSION['user_id'];
    if (empty($teamName)) {
        $error = "Le nom de l'équipe est obligatoire.";
    } else {
        try {
            $stmt = $bdd->prepare("INSERT INTO equipe (nom, niveau, date_creation) VALUES (?, ?, CURDATE())");
            $stmt->execute([$teamName, $teamLevel,]);
            $teamId = $bdd->lastInsertId();
            $stmt = $bdd->prepare("INSERT INTO membres_equipe (id_equipe, id_utilisateur, role, date_rejoint) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$teamId, $userId, 'capitaine']);
            header('Location: team_details.php?id_equipe=' . $teamId);
            exit();
        } catch (PDOException $e) {
            $error = "Erreur lors de la création de l'équipe : " . htmlspecialchars($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Créer une équipe';
require('../include/head.php');
?>

<body>
    <?php include('../include/header.php'); ?>

    <div class="container my-5">
        <h1 class="mb-4 text-center">Créer une équipe</h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="p-4 border rounded shadow-sm bg-light">
            <div class="mb-3">
                <label for="team_name" class="form-label">Nom de l'équipe</label>
                <input type="text" class="form-control" id="team_name" name="team_name" required>
            </div>
            <div class="mb-3">
                <label for="team_level" class="form-label">Niveau de l'équipe</label>
                <select class="form-select" id="team_level" name="team_level" required>
                    <option value="Débutant">Débutant</option>
                    <option value="Intermédiaire">Intermédiaire</option>
                    <option value="Avancé">Avancé</option>
                </select>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Créer l'équipe</button>
                <a href="team_list.php" class="btn btn-secondary">Retour</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>