<?php
session_start();
include('../include/database.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Tournois - jeux';
$pageCategory = 'tournois';
echo "<script>const pageCategory = '$pageCategory';</script>";
include('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}
?>

<body>

</body>

</html>