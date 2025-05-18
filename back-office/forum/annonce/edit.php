<?php
session_start();
$login_page = '../../../connexion/login.php';
require('../../check_session.php');
require('../../../include/check_timeout.php');
require('../../../include/database.php');
require_once __DIR__ . '/../../../path.php';



if (isset($_GET['id'])) {
    $annonceId = $_GET['id'];
    try {
        $stmt = $bdd->prepare("SELECT id_sujet, titre, categories, contenu FROM forum_sujets WHERE id_sujet = ?");
        $stmt->execute([$annonceId]);
        $annonce = $stmt->fetch();

        if (!$annonce) {
            header('Location: ' . forum_annonce_back . '?error=id_invalid');
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
$title = 'Modifier sujet Annonce';
require('../../head.php');
?>

<body>
    <?php
    $page = forum_annonce_back;
    include('../../navbar.php');
    ?>
    <main class="container mb-5">
        <?php if (!empty($_GET['message'])) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif ?>
        <?php if (!empty($_SESSION['message'])) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php unset($_SESSION['message']);
        endif ?>

        <h1 class="text-center mt-5 mb-4">Modifier le sujet - Annonce</h1>
        <?php if (isset($annonce)): ?>
            <form action="update.php" method="POST" class="p-4 border rounded shadow-sm bg-light">
                <input type="hidden" name="annonceId" value="<?php echo htmlspecialchars($annonce['id_sujet'] ?? ''); ?>">

                <div class="mb-3">
                    <label for="annonceName" class="form-label">Titre du sujet</label>
                    <input type="text" maxlength="150" oninput="updateCounter()" class="form-control" id="annonceName" name="titre" value="<?php echo htmlspecialchars($annonce['titre'] ?? ''); ?>" required>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Maximum 150 caractères</small>
                        <small id="counter" class="text-muted">0 / 150</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="category" class="form-label">Catégorie</label>
                    <select class="form-select" id="category" name="category" required>
                        <option value="Annonces" selected>Annonces</option>
                        <option value="Support">Support</option>
                        <option value="Discussions">Discussions</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="contenu" class="form-label">Contenu</label>
                    <textarea class="form-control" maxlength="1000" id="contenu" name="contenu" oninput="updateContentCounter()" rows="4" required><?= htmlspecialchars($annonce['titre']) ?></textarea>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Maximum 1000 caractères</small>
                        <small id="contentCounter" class="text-muted">0 / 1000</small>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="<?= forum_annonce_back ?>" class="btn btn-secondary">Retour</a>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-danger text-center">Aucune donnée pour ce sujet.</div>
        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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

        document.addEventListener("DOMContentLoaded", () => {
            updateCounter();
            updateContentCounter();
        });
    </script>
</body>

</html>