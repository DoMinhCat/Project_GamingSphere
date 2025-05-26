<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/check_timeout.php');
require('../../include/database.php');
require_once __DIR__ . '/../../path.php';

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
        if (isset($_GET['message']) && $_GET['message'] === '') //here
            $noti = '';
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
        <?php endif ?>

        <?php if (!empty($noti)) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $noti ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif ?>

        <h1 class="text-center my-5">Gestion des newsletters</h1>


    </main>

</body>

</html>