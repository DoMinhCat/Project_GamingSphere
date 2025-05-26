<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/check_timeout.php');
require('../../include/database.php');
require_once __DIR__ . '/../../path.php';
try {
    $stmt = $bdd->prepare("SELECT * from easter;");
    $stmt->execute();
    $currentReward = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $bdd->prepare("SELECT * from utilisateurs WHERE easter_found=1 ORDER BY date_easter DESC;");
    $stmt->execute();
    $finders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location:' . index_back . '?error=bdd');
    exit();
}

?>
<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Easter egg';
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
            $noti = 'Récompense modifiée avec succès !';
        elseif (isset($_GET['error']) && $_GET['error'] === 'bdd') {
            $noti_Err = 'Erreur lors de la connection à la base de données : ' . $_SESSION['error'];
            unset($_SESSION['error']);
        } elseif (isset($_GET['error']) && $_GET['error'] === 'invalid')
            $noti_Err = 'Requête invalide';

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

        <h1 class="text-center my-5">Gestion de Easter egg</h1>

        <form method="post" action="update_reward.php">
            <div class="mb-2">
                <label for="reward" class="form-label">Credit de récompense</label>
                <input type="number" value="<?= $currentReward['reward'] ?>" min="0" class="form-control" id="reward" name="reward" required>
            </div>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </form>

        <h2 class="mt-5 mb-4 text-center">Liste des Easter egg finders</h2>
        <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
            <table class="table table-bordered table-striped">
                <thead class='table-dark' style="position: sticky; top: 0; z-index: 1;">
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Pseudo</th>
                        <th>Date trouvé</th>
                    </tr>
                </thead>
                <tbody id="results">
                    <?php if (count($finders) > 0): ?>
                        <?php foreach ($finders as $finder): ?>
                            <tr>
                                <td class="align-middle"><?= htmlspecialchars($finder['id_utilisateurs']) ?></td>
                                <td class="align-middle"><?= htmlspecialchars($finder['email']) ?></td>
                                <td class="align-middle"><?= htmlspecialchars($finder['pseudo']) ?></td>
                                <td class="align-middle"><?= date('d/m/Y', strtotime($finder['date_easter'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center">Personne a trouvé l'Easter egg</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>

</html>