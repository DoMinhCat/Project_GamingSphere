<?php
session_start();
require('../include/database.php');
require('../include/check_timeout.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: profil.php?error=not_logged_in');
    exit;
}

$userId = $_SESSION['user_id'];
try {
    $stmt = $bdd->prepare("
        SELECT u.pseudo, u.photo_profil
        FROM relations r
        JOIN utilisateurs u ON (u.id_utilisateurs = r.user_id1 OR u.id_utilisateurs = r.user_id2)
        WHERE (r.user_id1 = ? OR r.user_id2 = ?) AND r.ami = 1 AND u.id_utilisateurs != ?");
    $stmt->execute([$userId, $userId, $userId]);
    $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
    exit;
}
try {
    $stmt = $bdd->prepare("
        SELECT u.pseudo, u.photo_profil, r.user_id1, r.user_id2
        FROM relations r
        JOIN utilisateurs u ON u.id_utilisateurs = r.user_id1
        WHERE r.user_id2 = ? AND r.status = 'pending'");
    $stmt->execute([$userId]);
    $pendingRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<?php $title = "Mes amis"; 
require('../include/head.php'); 
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}

?>
<body>
  <?php include('../include/header.php'); ?>
  <div class="container my-4">
      <h2 class="mb-4 text-center">Mes amis</h2>
      <?php if (count($friends) > 0): ?>
          <div class="row row-cols-1 row-cols-md-3 g-4">
              <?php foreach ($friends as $friend): ?>
                  <div class="col">
                      <div class="card h-100 shadow-sm">
                          <img src="<?= htmlspecialchars($friend['photo_profil'] ?: 'default-profile.jpg') ?>" alt="Photo de profil" class="card-img-top">
                          <div class="card-body text-center">
                              <h5 class="card-title"><?= htmlspecialchars($friend['pseudo']) ?></h5>
                              <a href="profil.php?user=<?= urlencode($friend['pseudo']) ?>" class="btn btn-outline-primary">Voir le profil</a>
                          </div>
                      </div>
                  </div>
              <?php endforeach; ?>
          </div>
      <?php else: ?>
          <div class="alert alert-info text-center">Aucun ami ajouté pour le moment.</div>
      <?php endif; ?>

      <h2 class="section-title text-center">Demandes d'amis en attente</h2>
      <?php if (count($pendingRequests) > 0): ?>
          <div class="row row-cols-1 row-cols-md-3 g-4">
              <?php foreach ($pendingRequests as $request): ?>
                  <div class="col">
                      <div class="card h-100 shadow-sm">
                          <img src="<?= htmlspecialchars($request['photo_profil'] ?: 'default-profile.jpg') ?>" alt="Photo de profil" class="card-img-top">
                          <div class="card-body text-center">
                              <h5 class="card-title"><?= htmlspecialchars($request['pseudo']) ?></h5>
                              <a href="profil.php?user=<?= urlencode($request['pseudo']) ?>" class="btn btn-outline-primary mb-2">Voir le profil</a>
                              <div class="d-flex justify-content-around">
                                  <form method="POST" action="accept_friend.php">
                                      <input type="hidden" name="friend_pseudo" value="<?= htmlspecialchars($request['pseudo']) ?>">
                                      <button type="submit" class="btn btn-success btn-sm">Accepter</button>
                                  </form>
                                  <form method="POST" action="reject_friend.php">
                                      <input type="hidden" name="friend_pseudo" value="<?= htmlspecialchars($request['pseudo']) ?>">
                                      <button type="submit" class="btn btn-danger btn-sm">Refuser</button>
                                  </form>
                              </div>
                          </div>
                      </div>
                  </div>
              <?php endforeach; ?>
          </div>
      <?php else: ?>
          <div class="alert alert-info text-center">Aucune demande en attente.</div>
      <?php endif; ?>
  </div>
  <?php include('../include/footer.php'); ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>