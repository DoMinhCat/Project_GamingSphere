<?php
session_start();
require('../include/database.php');
require('../include/check_timeout.php');
?>

<!DOCTYPE html>
<html lang="fr">
  
<?php
$title = 'Tournois';
require('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
  echo '<script src="../include/check_timeout.js"></script>';
}
?>

<body>
  <?php include('../include/header.php'); ?>

  <main>

    <div class="row main_t">
      <div class="col-md-6 p-3">
        <div class="card m_card" style="width: 100%; height: 50vh">
          <img src="../include/img/Tournament/Rocket_League_logo.svg.png" class="card-img-top" alt="...">
          <div class="card-body">
            <h5 class="card-title">Card title</h5>
            <p class="card-text"></p>
            <a href="#" class="btn btn-primary">Go somewhere</a>
          </div>
        </div>
      </div>

      <div class="col-md-6 p-3">
        <div class="card m_card" style="width: 50%; height:25vh ">
          <img src="../include/img/Tournament/Rocket_League_logo.svg.png" class="card-img-top" alt="...">
          <div class="card-body">
            <h5 class="card-title">Card title</h5>
            <p class="card-text">Wsh</p>
            <a href="#" class="btn btn-primary">Go somewhere</a>
          </div>
        </div>
      </div>
    </div>

  </main>

  <?php
  include("../include/footer.php");
  ?>
</body>

</html>
