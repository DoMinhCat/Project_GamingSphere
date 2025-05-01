<?php
session_start();
require('../include/database.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Communauté';
include('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}
?>

<body>
    <?php
    include("../include/header.php");
    ?>
    <main class="container my-5">


    </main>
    <?php
    include("../include/footer.php");
    ?>
</body>

</html>