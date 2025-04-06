<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Nouveau sujet';
include('../include/head.php');
include('../include/database.php');

if (!isset($bdd)) {
    die("Erreur de connexion à la base de données");
}

if (!isset($_GET['categorie']) || empty($_GET['categorie'])) {
    die("Catégorie non précisée.");
}
$categorie_nom = $_GET['categorie'];

$messageErreur = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titre = trim($_POST['titre']);
    $contenu = trim($_POST['contenu']);
    $auteur = $_SESSION['utilisateur'] ?? 'Anonyme';

    if (strlen($titre) > 150) {
        $messageErreur = "Le titre est trop long. (maximum 150 caractères)";
    } elseif (empty($titre) || empty($contenu)) {
        $messageErreur = "Veuillez remplir tous les champs.";
    } else {
        $stmt = $bdd->prepare("INSERT INTO messages (titre, contenu, message_public, date_msg, catégories, parent_id, auteur)
                               VALUES (?, ?, 'oui', NOW(), ?, NULL, ?)");
        $stmt->execute([$titre, $contenu, $categorie_nom, $auteur]);

        header("Location: categorie.php?nom=" . urlencode($categorie_nom));
        exit;
    }
}
?>

<body>
    <?php include("../include/header.php"); ?>

    <div class="container my-5">
        <h2 class="mb-4">Créer un nouveau sujet dans : <?= htmlspecialchars($categorie_nom) ?></h2>

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
    </div>

    <?php include("../include/footer.php"); ?>
    <script>
        function updateCounter() {
            const input = document.getElementById('titre');
            const counter = document.getElementById('counter');
            counter.textContent = `${input.value.length} / 150`;
        }

        // Mettre à jour dès le chargement (utile en cas de retour avec texte déjà rempli)
        document.addEventListener("DOMContentLoaded", updateCounter);
    </script>
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