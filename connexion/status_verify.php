<?php
session_start();
require_once __DIR__ . '/../path.php';
if (!empty($_SESSION['user_email'])) {
    header('Location: ../' . index_front);
    exit();
}
if (!isset($_GET['result'])) {
    header('Location: ../' . index_front);
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Statut de vérification de l\'e-mail';
$pageCategory = 'connexion';
echo "<script>const pageCategory = '$pageCategory';</script>";
include('../include/head.php')
?>

<body>
    <?php
    include("../include/header.php");
    ?>
    <div class="d-flex justify-content-center my-5">
        <div class="col-10 col-sm-10 col-md-10 col-lg-8 col-xl-6 justify-content-center text-center p-5 my-5 connexion_box">
            <div class="pb-3 mb-2">
                <h1>
                    <?php
                    if (isset($_GET['result']) && $_GET['result'] == 'success') {
                        echo 'Email vérifié avec succcès !';
                    } elseif (isset($_GET['result']) && ($_GET['result'] == 'token_invalid') || $_GET['result'] == 'token_expire') {
                        echo 'Vérification de l\'email échoué ! ';
                    }
                    ?>
                </h1>
            </div>

            <?php
            if (isset($_GET['result']) && $_GET['result'] == 'success') {
                echo '<span class="lato24 mb-2">Vous pouvez maintenant vous connecter.</span>';
            } elseif (isset($_GET['result']) && $_GET['result'] == 'token_invalid') {
                echo '<span class="lato24 mb-2">Le lien de vérification est invalide.</span>';
            } elseif (isset($_GET['result']) && $_GET['result'] == 'token_expire') {
                echo '<span class="lato24 mb-2">Le lien de vérification a expiré.<br> Veuillez envoyez une autre demande de vérification.</span>';
            }
            ?>
            <div class="line-with-letters montserrat-titre32 my-3">
                <span class="line"></span>
            </div>

            <div class="d-flex flex-column pt-1">
                <?php
                if (isset($_GET['result']) && ($_GET['result'] == 'token_invalid') || $_GET['result'] == 'token_expire') {
                    echo '<a href="' . resend_verify_inscrire . '" class="btn btn-primary mt-2">
                    Renvoyer un email de vérification
                    </a>';
                }
                ?>
                <a href="<?= login ?>" class="btn btn-success mt-2">
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