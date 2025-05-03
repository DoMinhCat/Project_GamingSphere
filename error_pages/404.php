<?php
require_once __DIR__ . '/../path.php';
?>
<!DOCTYPE html>
<html lang="fr">
<?php
$title = '404 NOT FOUND';
include('../include/head.php');
?>

<body>
    <main class="container">
        <h1>404</h1>
        <a href="'<?= index_front ?>'" class="btn btn-primary">Retour à l'acueil</a>
    </main>
</body>

</html>