<?php
session_start();
require('../include/check_timeout.php');
?>
<!DOCTYPE html>
<html lang="fr">

<?php
$title = 'Actualités - article';
require('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}
?>

<body>
    <?php
    include("../include/header.php");
    ?>

    <?php
    include("../include/footer.php");
    ?>
</body>

</html>