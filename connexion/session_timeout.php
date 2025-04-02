<!-- working here -->
<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Session expirée';
include('../include/head.php');
?>

<body>
    <div class="d-flex justify-content-center">
        <div class="col-8 col-sm-7 col-md-6 col-lg-7 col-xl-6 justify-content-center text-center p-5 my-5 connexion_box">

            <div class="pb-1">
                <h1 class="mb-3">Vous êtes toujours là ?</h1>
            </div>

            <span class="lato24" style="color: #FF6E40;"><strong>Votre session a expirée.</strong></span><br>
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