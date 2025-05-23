<?php
require_once __DIR__ . '/../path.php';
?>
<!DOCTYPE html>
<html lang="fr">
<?php
$title = '500 INTERNAL SERVER ERROR';
$pageCategory = 'error';
echo "<script>const pageCategory = '$pageCategory';</script>";
include('../include/head.php');
?>

<body>
    <?php
    include('../include/header.php');
    ?>
    <main class="container my-5">
        <div class="d-flex justify-content-center my-5">
            <div class="col-10 col-sm-10 col-md-10 col-lg-8 col-xl-6 justify-content-center text-center p-5 my-5 connexion_box">
                <h2>Erreur 500</h2>
                <h5>Uh oh, il y a un problème de notre côté. Veuillez revenir plus tard.</h5>
                <a href="<?= index_front ?>" class="btn btn-primary">Retour à l'acueil</a>
            </div>
        </div>
    </main>
    <?php
    include('../include/footer.php');
    ?>
</body>

</html>