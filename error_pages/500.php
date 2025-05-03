<?php
require_once __DIR__ . '/../path.php';
?>
<!DOCTYPE html>
<html lang="fr">
<?php
$title = '500 INTERNAL SERVER ERROR';
include('../include/head.php');
?>

<body>
    <?php
    include('../include/header.php');
    ?>
    <main class="container my-5">
        <h1>500</h1>
        <a href="<?= index_front ?>" class="btn btn-primary">Retour à l'acueil</a>
    </main>
    <?php
    include('../include/footer.php');
    ?>
</body>

</html>