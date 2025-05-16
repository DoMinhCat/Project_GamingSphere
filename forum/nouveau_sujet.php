<?php
session_start();
$login_page = '../connexion/login.php';
require('../include/check_session.php');
require_once('../include/database.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Nouveau sujet';
$pageCategory = 'forum';
echo "<script>const pageCategory = '$pageCategory';</script>";
require('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}

if (!isset($_GET['categorie']) || empty($_GET['categorie'])) {
    header('location:' . forum_main . '?message=' . urlencode('Catégorie non précisée !'));
    exit;
}
$categorie_nom = $_GET['categorie'];

$messageErreur = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titre = trim($_POST['titre']);
    $contenu = trim($_POST['contenu']);
    $auteur = $_SESSION['user_pseudo'] ?? 'Anonyme';

    if (strlen($titre) > 150 || strlen($contenu) > 1000) {
        $messageErreur = "Veuillez respecter la longeur maximum du titre et du contenu !";
    } elseif (empty($titre) || empty($contenu)) {
        $messageErreur = "Veuillez remplir tous les champs.";
    } else {
        try {
            $stmt = $bdd->prepare("INSERT INTO forum_sujets (titre, contenu, date_msg, categories, parent_id, auteur) VALUES (?, ?, NOW(), ?, NULL, ?);");
            $stmt->execute([$titre, $contenu, $categorie_nom, $auteur]);

            header('Location:' . forum_category . '?nom=' . urlencode($categorie_nom) . '&success=' . urlencode('Sujet ajouté !'));
            exit;
        } catch (PDOException) {
            header('Location:' . forum_category . '?message=' . urlencode('Erreur lors de l\'ajoute du sujet !'));
            exit();
        }
    }
}
?>

<body>
    <?php include("../include/header.php"); ?>

    <main class="container my-5">
        <div class="mb-4 d-flex align-items-center gap-2">
            <a href="<?= forum_main ?>" class="text-decoration-none fs-3">
                <i class="bi bi-chevron-left"></i>
            </a>
            <h1 class="m-0">Créer un nouveau sujet dans : <?= htmlspecialchars($categorie_nom) ?></h1>
        </div>

        <?php if (!empty($messageErreur)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($messageErreur) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label for="titre" class="form-label">Titre du sujet</label>
                <input type="text" class="form-control" id="titre" name="titre" maxlength="150" required oninput="updateCounter()">
                <div class="d-flex justify-content-between">
                    <small class="text-muted">Maximum 150 caractères</small>
                    <small id="counter" class="text-muted">0 / 150</small>
                </div>
            </div>

            <div class="mb-3">
                <label for="contenu" class="form-label">Message</label>
                <textarea class="form-control" id="contenu" name="contenu" rows="5" maxlength="1000" oninput="updateContentCounter()" required></textarea>
                <div class="d-flex justify-content-between">
                    <small class="text-muted">Maximum 1000 caractères</small>
                    <small id="contentCounter" class="text-muted">0 / 1000</small>
                </div>

            </div>

            <button type="submit" class="btn btn-primary">Créer le sujet</button>
        </form>
    </main>

    <?php include("../include/footer.php"); ?>
    <script>
        function updateCounter() {
            const input = document.getElementById('titre');
            const counter = document.getElementById('counter');
            counter.textContent = `${input.value.length} / 150`;
        }

        function updateContentCounter() {
            const textarea = document.getElementById('contenu');
            const counter = document.getElementById('contentCounter');
            counter.textContent = `${textarea.value.length} / 1000`;
        }

        // Met à jour les deux compteurs au chargement (utile si champs déjà remplis)
        document.addEventListener("DOMContentLoaded", () => {
            updateCounter();
            updateContentCounter();
        });
    </script>
</body>

</html>