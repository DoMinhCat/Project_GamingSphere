<?php
session_start();
require_once('../../../include/database.php');
$login_page = '../../../connexion/login.php';
require('../../check_session.php');
require('../../../include/check_timeout.php');
require_once __DIR__ . '/../../../path.php';
?>

<!DOCTYPE html>
<html lang="fr">

<?php
$title = 'Gestion des discussions';
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
        <h1 class="my-5 text-center">Gestion du forum Discussion</h1>

    </main>
</body>

</html>