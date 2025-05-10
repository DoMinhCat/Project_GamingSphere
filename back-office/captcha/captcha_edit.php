<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/database.php');
require_once __DIR__ . '/../../path.php';
require('../../include/check_timeout.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $stmt = $bdd->prepare("SELECT id_captcha, question,answer,status FROM captcha WHERE id_captcha = ?");
        $stmt->execute([$id]);
        $captcha = $stmt->fetch();

        if (!$captcha) {
            header('Location: ' . captcha_back . '?error=id_invalid');
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = htmlspecialchars($e->getMessage());
        header('Location:' . captcha_back . '?error=bdd');
        exit();
    }
} else {
    header('Location: ' . captcha_back . '?error=id_invalid');
    exit();
}
?>


<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Modification des Captchas';
require('../head.php');
?>

<body>
    <?php
    $page = captcha_back;
    include("../navbar.php"); ?>
    <main class="container my-5">
        <h1 class="text-center mb-4">Modifier la question captcha</h1>
        <?php if (isset($captcha)): ?>
            <form action="update_captcha.php" method="POST" class="p-4 border rounded shadow-sm bg-light">
                <input type="hidden" name="id_captcha" value="<?= htmlspecialchars($captcha['id_captcha'] ?? ''); ?>">

                <div class="mb-3">
                    <label for="question" class="form-label">Question</label>
                    <input type="text" class="form-control" id="question" name="question" value="<?= htmlspecialchars($captcha['question'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="answer" class="form-label">Réponse</label>
                    <input type="text" class="form-control" id="answer" name="answer" value="<?= htmlspecialchars($captcha['answer'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" value="1" name="statut" id="statut_actif" <?php if ($captcha['statut'] == 1) echo 'checked' ?>>
                        <label class="form-check-label" for="statut_actif">
                            Actif
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" value="0" name="statut" id="statut_inactif" <?php if ($captcha['statut'] == 0) echo 'checked' ?>>
                        <label class="form-check-label" for="statut_inactif">
                            Inactif
                        </label>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Modifier la question</button>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-danger text-center">Aucune donnée pour cette question.</div>
        <?php endif; ?>
    </main>

</body>

</html>