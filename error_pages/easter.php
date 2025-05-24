<?php
require_once __DIR__ . '/../path.php';
?>
<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Easter egg';
include('../include/head.php');
?>

<body>
    <?php
    include('../include/header.php');
    ?>
    <main class="container my-5">
        <div class="d-flex flex-column text-center justify-content-center">
            <h1 class="mb-5">Fécilitations! Vous avez trouvé un easter egg !!!</h1>
            <h3 class="mb-3">Bienvenue, hacker curieux ! Voici quelques stats du projet :</h3>
            <p>Note attendu pour le projet annuel : 19/20 (Nous savons que c'est irréaliste :/ )</p>
            <p>Bugs rencontrés : trop pour les compter</p>
            <p>Tasses de café consommées : ~41</p>
            <p>Moment le plus drôle : <em>aucun, pourquoi pensez-vous qu'il y a eu un moment drôle ?</em></p>

            <h2 class="mt-5 mb-2">Notre équipe <em>incroyable</em></h2>
            <div class="d-flex gap-4 justify-content-center">
                <img src="/error_pages/members/Paul.jpg" alt="Paul Sainctavit" class="rounded" width="150">
                <img src="/error_pages/members/Cat.jpg" alt="Minh Cat Do" class="rounded" width="150">
                <img src="/error_pages/members/Maxime.jpg" alt="Maxime Oliveira" class="rounded" width="150">
            </div>
            <a href="<?= index_front ?>" class="btn btn-primary mt-4">Retour à l'acueil</a>
        </div>

    </main>
    <?php
    include('../include/footer.php');
    ?>

</body>

</html>