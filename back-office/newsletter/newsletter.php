<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/check_timeout.php');
require('../../include/database.php');
require_once __DIR__ . '/../../path.php';
try {
    $stmt = $bdd->prepare("SELECT * from utilisateurs WHERE newsletter_sub=1;");
    $stmt->execute();
    $users_sub = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $bdd->prepare("SELECT * from newsletter_interval LIMIT 1;");
    $stmt->execute();
    $interval = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['reward'])) {
    }
} catch (PDOException $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location:' . index_back . '?error=bdd');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Newsletter';
require('../head.php');
?>

<body class="pb-4">
    <?php
    $page = index_back;
    include('../navbar.php');
    ?>
    <main class="container mb-5">
        <?php
        $noti = '';
        $noti_Err = '';
        if (isset($_GET['message']) && $_GET['message'] === 'edit_ok') //here
            $noti = 'Interval de reenvoie de l\'email modifié !';
        elseif (isset($_GET['error']) && $_GET['error'] === 'bdd') {
            $noti_Err = 'Erreur lors de la connection à la base de données : ' . $_SESSION['error'];
            unset($_SESSION['error']);
        } elseif (isset($_GET['error']) && $_GET['error'] === 'invalid')
            $noti_Err = 'Requête invalide !';
        elseif (isset($_GET['error']) && $_GET['error'] === 'no_sub')
            $noti_Err = 'Aucune abonné !';
        elseif (isset($_GET['message']) && $_GET['message'] === 'sent')
            $noti = 'Newsletter envoyé !';
        elseif (isset($_GET['error']) && $_GET['error'] === 'send_fail') {
            $noti_Err = 'Erreur de \'envoie : ' . $_SESSION['error'];
            unset($_SESSION['error']);
        }

        ?>
        <?php if (!empty($noti_Err)) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $noti_Err ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif ?>

        <?php if (!empty($noti)) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $noti ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif ?>

        <h1 class="text-center mt-5 mb-4">Gestion des newsletters</h1>

        <h2 class="mb-3">Envoyer un newsletter personnalisé</h2>
        <form method="POST" action="send_customize.php">
            <textarea class="form-control" name="message" placeholder="Votre message" required></textarea>
            <button type="submit" class="btn btn-primary mt-2">Envoyer</button>
        </form>

        <form action="update_interval.php" method="post" class="mt-3">
            <label class="form-label" for="day">Envoyer un email de re-engagement chaque (jours)</label>
            <input class="form-control" required type="number" step="1" id="day" name="interval" value="<?= $interval['gap'] ?>">
            <button type="submit" class="btn btn-primary mt-2">Enregistrer</button>
        </form>


        <h2 class="mt-5 mb-4 text-center">Liste des abonnés</h2>
        <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
            <table class="table table-bordered table-striped">
                <thead class='table-dark' style="position: sticky; top: 0; z-index: 1;">
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Pseudo</th>
                        <th>Date d'abonnenment</th>
                    </tr>
                </thead>
                <tbody id="results">
                    <?php if (count($users_sub) > 0): ?>
                        <?php foreach ($users_sub as $user): ?>
                            <tr>
                                <td class="align-middle"><?= htmlspecialchars($user['id_utilisateurs']) ?></td>
                                <td class="align-middle"><?= htmlspecialchars($user['email']) ?></td>
                                <td class="align-middle"><?= htmlspecialchars($user['pseudo']) ?></td>
                                <td class="align-middle"><?= date('d/m/Y', strtotime($user['newsletter_date'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center">Aucun utilisateur abonné aux newsletter :/</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </main>

</body>

</html>