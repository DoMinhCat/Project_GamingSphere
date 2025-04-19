<?php
session_start();

$timeout_duree = 60 * 30;
$this_page = basename($_SERVER['PHP_SELF']);

if ((isset($_SESSION['actif']) && time() - $_SESSION['actif'] < $timeout_duree)) {
    header("Location: ../index.php");
    exit();
}

session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Session expirée';
include('../include/head.php');
?>

<body>
    <div class="d-flex justify-content-center">
        <div class="col-10 col-sm-10 col-md-10 col-lg-8 col-xl-6 justify-content-center text-center p-5 my-5 connexion_box">
            <div class="pb-1 mb-3">
                <h1>Vous êtes toujours là ?</h1>
            </div>

            <span class="lato24 mb-2"><strong>Votre session a expirée.</strong></span>
            <span class="lato16">Vous avez été déconnecté en raison d'inactivité afin de proteger vos informations privées</span>

            <div class="line-with-letters montserrat-titre32 my-2">
                <span class="line"></span>
            </div>

            <div class="d-flex flex-column pt-1">
                <a href="login.php" class="btn btn-success">
                    Revenir à la page de connexion
                </a>
            </div>
        </div>
    </div>

    <?php
    include("../include/footer.php");
    ?>
</body>

</html>