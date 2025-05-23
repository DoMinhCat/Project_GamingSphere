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
    <main class="container my-5">
        <?php
        include('../include/header.php');
        ?>
        <div class="d-flex justify-content-center">
            <h1 class="mb-5">Fécilitations! Vous avez trouvé un easter egg !!!</h1>
            <p>Bienvenue, hacker curieux ! Voici quelques stats du projet :</p>
            <ul>
                <li>Note attendu pour le projet : 19/20</li>
                <li>Bugs rencontrés : trop pour les compter</li>
                <li>Tasses de café consommées : 87</li>
                <li>Moment le plus drôle : <em>aucun, pourquoi pensez-vous qu'il y a eu un moment drôle ?</em></li>
            </ul>

            <h2 class="mt-5">Notre équipe incroyable</h2>
            <div class="d-flex gap-4">
                <img src="members/Paul.jpg" alt="Paul Sainctavit" class="rounded" width="150">
                <img src="members/Cat.jpg" alt="Minh Cat Do" class="rounded" width="150">
                <img src="members/Maxime.jpg" alt="Maxime Oliveira" class="rounded" width="150">
            </div>
            <a href="<?= index_front ?>" class="btn btn-primary">Retour à l'acueil</a>
        </div>




        <?php
        include('../include/footer.php');
        ?>
    </main>
</body>

</html>