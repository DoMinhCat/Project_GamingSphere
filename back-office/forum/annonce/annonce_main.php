<?php
session_start();
require_once('../../../include/database.php');
$login_page = '../../../connexion/login.php';
require('../../check_session.php');
require('../../../include/check_timeout.php');
require_once __DIR__ . '/../../../path.php';


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titre = trim($_POST['titre']);
    $contenu = trim($_POST['contenu']);
    $auteur = $_SESSION['utilisateur'] ?? 'Anonyme';

    if (strlen($titre) > 150 || strlen($contenu) > 1000) {
        header('Location:' . forum_annonce_back . '?error=length');
        exit;
    } elseif (empty($titre) || empty($contenu)) {
        header('Location:' . forum_annonce_back . '?error=missing_fields');
        exit;
    } else {
        try {
            $stmt = $bdd->prepare("INSERT INTO forum_sujets (titre, contenu, date_msg, categories, parent_id, auteur) VALUES (?, ?, NOW(), 'Annonces', NULL, ?);");
            $stmt->execute([$titre, $contenu, $auteur]);

            header('Location:' . forum_annonce_back . '?message=added');
            exit;
        } catch (PDOException $e) {
            $_SESSION['error'] = htmlspecialchars($e->getMessage());
            header('Location:' . forum_annonce_back . '?error=bdd');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<?php
$title = 'Gestion des annonces';
require('../../head.php');
?>

<body class="pb-4">
    <?php
    $page = forum_back;
    include('../../navbar.php');
    ?>
    <main class="container my-5">
        <?php
        $noti = '';
        $noti_Err = '';
        if (isset($_GET['error']) && $_GET['error'] === 'missing_id')
            $noti_Err = 'Aucun ID spécifié !';
        elseif (isset($_GET['message']) && $_GET['message'] === 'added')
            $noti = 'Sujet ajouté !';
        elseif (isset($_GET['error']) && $_GET['error'] === 'missing_fields')
            $noti_Err = 'Veuillez remplir tous les champs !';
        elseif (isset($_GET['error']) && $_GET['error'] === 'length')
            $noti_Err = 'Veuillez respecter la longeur du titre et du contenu !';
        elseif (isset($_GET['error']) && $_GET['error'] === 'bdd') {
            $noti_Err = 'Erreur lors de la connection à la base de données : ' . $_SESSION['error'];
            unset($_SESSION['error']);
        }
        ?>

        <?php if (!empty($noti_Err)) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $noti_Err ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif;
        if (!empty($noti)) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $noti ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif ?>

        <h1 class="my-5 text-center">Gestion du forum Annonce</h1>
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