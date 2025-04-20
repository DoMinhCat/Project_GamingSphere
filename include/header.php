<?php
include('database.php');
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if (isset($_SESSION['user_id'])) {
  try {
    $stmt = $bdd->prepare("SELECT u.pseudo, r.date_début
                             FROM relations r
                             JOIN utilisateurs u ON u.id_utilisateurs = r.user_id1
                             WHERE r.user_id2 = ? AND r.status = 'pending'");
    $stmt->execute([$_SESSION['user_id']]);
    $friendRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $notificationCount = count($friendRequests);
  } catch (PDOException $e) {
    $notificationCount = 0;
  }
} else {
  $notificationCount = 0;
}
$messagesNonLus = [];

if (isset($_SESSION['user_id'])) {
  try {
    $stmtMessages = $bdd->prepare("SELECT m.id, m.expediteur_id, m.date_envoi
                                      FROM messages m
                                      WHERE m.destinataire_id = ? AND m.lu = 0
                                      ORDER BY m.date_envoi DESC");
    $stmtMessages->execute([$_SESSION['user_id']]);
    $messagesNonLus = $stmtMessages->fetchAll(PDO::FETCH_ASSOC);

    $notificationCountMessages = count($messagesNonLus);
  } catch (PDOException $e) {
    $notificationCountMessages = 0;
  }
} else {
  $notificationCountMessages = 0;
}

if (isset($_SESSION['user_id'])) {
  try {
    $stmtTeamRequests = $bdd->prepare("
          SELECT i.id_invitation, u.pseudo AS demandeur, e.nom AS equipe, i.date_invitation
          FROM invitations i
          JOIN utilisateurs u ON i.id_utilisateur = u.id_utilisateurs
          JOIN equipe e ON i.id_equipe = e.id_équipe
          JOIN membres_equipe me ON e.id_équipe = me.id_equipe
          WHERE me.id_utilisateur = ? AND me.role = 'capitaine' AND i.statut = 'en attente'
      ");
    $stmtTeamRequests->execute([$_SESSION['user_id']]);
    $teamRequests = $stmtTeamRequests->fetchAll(PDO::FETCH_ASSOC);
    $notificationCountTeams = count($teamRequests);
  } catch (PDOException $e) {
    $notificationCountTeams = 0;
  }
} else {
  $notificationCountTeams = 0;
}

$credits = 0;
if (isset($_SESSION['user_id'])) {
  try {
    $stmtCredits = $bdd->prepare("SELECT credits FROM credits WHERE user_id = ?");
    $stmtCredits->execute([$_SESSION['user_id']]);
    $result = $stmtCredits->fetch(PDO::FETCH_ASSOC);
    if ($result) {
      $credits = $result['credits'];
    }
  } catch (PDOException $e) {
    $credits = 0;
  }
}
?>




<header style="background-color: #ff6e40 !important;">
  <div class="container-fluid">
    <!-- uppper -->
    <div class="row d-none d-xl-flex justify-content-end align-items-center py-2">
      <div class="col-12 col-md-6 d-flex justify-content-end align-items-center">
        <form id="globalSearchForm" method="POST" action="<?= ($this_page == 'index.php') ? 'include/search.php' : '../include/search.php' ?>" class="d-flex align-items-center me-3">
          <div class="input-group">
            <input type="text" id="query" name="query" class="form-control-sm" placeholder="Rechercher" required>
            <select name="category" id="category" class="form-select-sm">
              <option value="">Tous</option>
              <option value="users">Utilisateurs</option>
              <option value="articles">Articles</option>
              <option value="games">Jeux</option>
            </select>
          </div>
        </form>

        <?php if (isset($_SESSION['user_email'])) : ?>
          <a href=<?= ($this_page == 'index.php') ? 'messages/messagerie.php' : '../messages/messagerie.php' ?> class="btn btn-outline-dark d-flex align-items-center me-3 position-relative">
            <i class="bi bi-chat-dots-fill"></i>
            <?php if (isset($_SESSION['user_email']) && ($notificationCountMessages > 0)): ?>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.75rem;">
                <?= $notificationCountMessages ?>
              </span>
            <?php endif; ?>
          </a>
        <?php endif; ?>

        <?php if (isset($_SESSION['user_email'])) : ?>
          <div class="dropdown me-3">
            <button class="btn btn-outline-dark position-relative d-flex align-items-center" id="notification-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-bell-fill"></i>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notification-count" style="display: <?= ($notificationCount + $notificationCountTeams) > 0 ? 'inline' : 'none'; ?>;">
                <?= $notificationCount + $notificationCountTeams ?>
              </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" id="notification-menu" style="padding: 0; max-height: 300px; overflow-y: auto;">

              <?php if ($notificationCount > 0): ?>
                <?php foreach ($friendRequests as $request): ?>
                  <li class="dropdown-item d-flex justify-content-between align-items-center">
                    <div>
                      <a href="profil.php?user=<?= urlencode($request['pseudo']) ?>" class="text-dark">
                        <strong>Demande d'ami</strong> de <?= htmlspecialchars($request['pseudo']) ?> - <?= date('d/m/Y', strtotime($request['date_début'])) ?>
                    </div>
                    <div class="btn-group">
                      <form action="<?php echo $_SERVER['SERVER_NAME'] . '/profil/accept_friend_request.php' ?>">
                        <input type="hidden" name="friend_pseudo" value="<?= htmlspecialchars($request['pseudo']) ?>">
                        <button type="submit" class="btn btn-success btn-sm" title="Accepter">
                          <i class="bi bi-check-circle-fill"></i>
                        </button>
                      </form>
                      <form action="<?php echo $_SERVER['SERVER_NAME'] . '/profil/accept_friend_request.php' ?>">
                        <input type="hidden" name="friend_pseudo" value="<?= htmlspecialchars($request['pseudo']) ?>">
                        <button type="submit" class="btn btn-danger btn-sm" title="Refuser">
                          <i class="bi bi-x-circle-fill"></i>
                        </button>
                      </form>
                    </div>
                  </li>
                <?php endforeach; ?>
              <?php endif; ?>
              <?php if ($notificationCountTeams > 0): ?>
                <?php foreach ($teamRequests as $request): ?>
                  <li class="dropdown-item d-flex justify-content-between align-items-center">
                    <div>
                      <strong>Demande de rejoindre</strong> l'équipe <?= htmlspecialchars($request['equipe']) ?> par <?= htmlspecialchars($request['demandeur']) ?> - <?= date('d/m/Y', strtotime($request['date_invitation'])) ?>
                    </div>
                    <div class="btn-group">
                      <form action="<?php echo $_SERVER['SERVER_NAME'] . '/team/accept_team.php' ?>" method="POST" style="display: inline;">
                        <input type="hidden" name="invitation_id" value="<?= htmlspecialchars($request['id_invitation']) ?>">
                        <button type="submit" class="btn btn-success btn-sm" title="Accepter">
                          <i class="bi bi-check-circle-fill"></i>
                        </button>
                      </form>
                      <form action="<?= $_SERVER['SERVER_NAME'] . 'team/reject_team_request.php' ?>" method="POST" style="display: inline;">
                        <input type="hidden" name="invitation_id" value="<?= htmlspecialchars($request['id_invitation']) ?>">
                        <button type="submit" class="btn btn-danger btn-sm" title="Refuser">
                          <i class="bi bi-x-circle-fill"></i>
                        </button>
                      </form>
                    </div>
                  </li>
                <?php endforeach; ?>
              <?php endif; ?>

              <?php if ($notificationCount === 0 && $notificationCountTeams === 0): ?>
                <li class="dropdown-item text-center text-muted">Aucune notification</li>
              <?php endif; ?>
            </ul>
          </div>
        <?php endif; ?>
      </div>
    </div>
    <!-- lower -->
    <div class="row d-none d-xl-flex justify-content-between align-items-center py-2">
      <div class="container container-fluid" style="padding: 0.5rem">
        <div class="col-3 d-flex justify-content-start">
          <a href="<?= ($this_page == 'index.php') ? 'index.php' : '../index.php' ?>">
            <img src="<?= ($this_page == 'index.php') ? 'include/LOGO ENTIER 40px.png' : '../include/LOGO ENTIER 40px.png' ?>" alt="Logo" class="col-2 image-fluid" style="height: 30px; width: auto;">
          </a>
        </div>

        <div class="col-6 d-flex justify-content-center">
          <a class="nav-link lato16 ms-3 <?= ($this_page == 'index.php') ? 'active' : '' ?>" href=<?= ($this_page == 'index.php') ? 'index.php' : '../index.php' ?> style="color: #F5F0E1 !important;">
            Accueil
          </a>
          <a class="nav-link lato16 ms-3 <?= ($this_page == 'magasin_main.php') ? 'active' : '' ?>" href=<?= ($this_page == 'index.php') ? 'magasin/magasin_main.php' : '../magasin/magasin_main.php' ?> style="color: #F5F0E1 !important;">
            Magasin
          </a>
          <a class="nav-link lato16 ms-3 <?= ($this_page == 'tournois_main.php') ? 'active' : '' ?>" href=<?= ($this_page == 'index.php') ? 'tournois/tournois_main.php' : '../tournois/tournois_main.php' ?> style="color: #F5F0E1 !important;">
            Tournois
          </a>
          <a class="nav-link lato16 ms-3 <?= ($this_page == 'communaute_main.php') ? 'active' : '' ?>" href=<?= ($this_page == 'index.php') ? 'communaute/communaute_main.php' : '../communaute/communaute_main.php' ?> style="color: #F5F0E1 !important;">
            Communauté
          </a>
          <a class="nav-link lato16 ms-3 <?= ($this_page == 'actualite_main.php') ? 'active' : '' ?>" href=<?= ($this_page == 'index.php') ? 'actualite/actualite_main.php' : '../actualite/actualite_main.php' ?> style="color: #F5F0E1 !important;">
            Actualités
          </a>
          <a class="nav-link lato16 mx-3 <?= ($this_page == 'forum_main.php') ? 'active' : '' ?>" href=<?= ($this_page == 'index.php') ? 'forum/forum_main.php' : '../forum/forum_main.php' ?> style="color: #F5F0E1 !important;">
            Forum
          </a>
        </div>

        <div class="col-3 d-flex justify-content-end">
          <a href="panier.php" class="btn btn-outline-dark d-flex align-items-center me-3">
            <i class="bi bi-cart-fill"></i>
          </a>

          <?php if (isset($_SESSION['user_email'])): ?>
            <div class="dropdown me-3">
              <button class="btn btn-outline-dark dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end" style="padding: 0;">
                <li><a href="<?= $href ?>" class="dropdown-item btn btn-sm py-3"><?php echo htmlspecialchars($display_name) ?></a></li>
                <li><a href="<?= $link ?>" class="dropdown-item btn btn-sm py-3">
                    <span class="d-flex align-items-center">
                      <i class="bi bi-wallet2 me-2" style="font-size: 1rem;"></i>
                      Crédits : <?= htmlspecialchars($credits) ?>
                    </span>
                  </a></li>
                <li><button id="theme-btn" class="dropdown-item btn btn-sm py-3">Activer/Désactiver le mode nuit</button></li>
              </ul>
            </div>
          <?php endif; ?>

          <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === true): ?>
            <a href="<?= ($this_page == 'index.php') ? 'back-office/index.php' : '../back-office/index.php' ?>" class="btn btn-warning m-3" style="background-color: #ffc107; color: #212529; border-radius: 20px; font-weight: bold; padding: 0.3rem 0.3rem; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
              <i class="bi bi-gear-fill"></i> Back Office
            </a>
          <?php endif; ?>
        </div>
      </div>

    </div>
    <!-- single -->
    <div class="row d-flex d-xl-none justify-content-between align-items-center py-2">
      <div class="col-3 d-flex justify-content-start">
        <a href="<?= ($this_page == 'index.php') ? 'index.php' : '../index.php' ?>">
          <img src="<?= ($this_page == 'index.php') ? 'include/LOGO ENTIER 40px.png' : '../include/LOGO ENTIER 40px.png' ?>" alt="Logo" class="col-2 image-fluid" style="height: 30px; width: auto;">
        </a>
      </div>

      <div class="col-9 d-flex justify-content-end">
        <?php if (isset($_SESSION['user_email'])): ?>
          <a href="<?= ($this_page == 'index.php') ? 'messages/messagerie.php' : '../messages/messagerie.php' ?>" class="btn btn-outline-dark d-flex align-items-center me-3">
            <i class="bi bi-chat-dots-fill"></i>
          </a>
        <?php endif; ?>
        <?php if (isset($_SESSION['user_email'])) : ?>
          <div class="dropdown me-3">
            <button class="btn btn-outline-dark position-relative d-flex align-items-center" id="notification-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-bell-fill"></i>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notification-count" style="display: <?= ($notificationCount + $notificationCountTeams) > 0 ? 'inline' : 'none'; ?>;">
                <?= $notificationCount + $notificationCountTeams ?>
              </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" id="notification-menu" style="padding: 0; max-height: 300px; overflow-y: auto;">

              <?php if ($notificationCount > 0): ?>
                <?php foreach ($friendRequests as $request): ?>
                  <li class="dropdown-item d-flex justify-content-between align-items-center">
                    <div>
                      <a href="profil.php?user=<?= urlencode($request['pseudo']) ?>" class="text-dark">
                        <strong>Demande d'ami</strong> de <?= htmlspecialchars($request['pseudo']) ?> - <?= date('d/m/Y', strtotime($request['date_début'])) ?>
                    </div>
                    <div class="btn-group">
                      <form action="<?php echo $_SERVER['SERVER_NAME'] . '/profil/accept_friend_request.php' ?>">
                        <input type="hidden" name="friend_pseudo" value="<?= htmlspecialchars($request['pseudo']) ?>">
                        <button type="submit" class="btn btn-success btn-sm" title="Accepter">
                          <i class="bi bi-check-circle-fill"></i>
                        </button>
                      </form>
                      <form action="<?php echo $_SERVER['SERVER_NAME'] . '/profil/accept_friend_request.php' ?>">
                        <input type="hidden" name="friend_pseudo" value="<?= htmlspecialchars($request['pseudo']) ?>">
                        <button type="submit" class="btn btn-danger btn-sm" title="Refuser">
                          <i class="bi bi-x-circle-fill"></i>
                        </button>
                      </form>
                    </div>
                  </li>
                <?php endforeach; ?>
              <?php endif; ?>
              <?php if ($notificationCountTeams > 0): ?>
                <?php foreach ($teamRequests as $request): ?>
                  <li class="dropdown-item d-flex justify-content-between align-items-center">
                    <div>
                      <strong>Demande de rejoindre</strong> l'équipe <?= htmlspecialchars($request['equipe']) ?> par <?= htmlspecialchars($request['demandeur']) ?> - <?= date('d/m/Y', strtotime($request['date_invitation'])) ?>
                    </div>
                    <div class="btn-group">
                      <form action="<?php echo $_SERVER['SERVER_NAME'] . '/team/accept_team.php' ?>" method="POST" style="display: inline;">
                        <input type="hidden" name="invitation_id" value="<?= htmlspecialchars($request['id_invitation']) ?>">
                        <button type="submit" class="btn btn-success btn-sm" title="Accepter">
                          <i class="bi bi-check-circle-fill"></i>
                        </button>
                      </form>
                      <form action="<?= $_SERVER['SERVER_NAME'] . 'team/reject_team_request.php' ?>" method="POST" style="display: inline;">
                        <input type="hidden" name="invitation_id" value="<?= htmlspecialchars($request['id_invitation']) ?>">
                        <button type="submit" class="btn btn-danger btn-sm" title="Refuser">
                          <i class="bi bi-x-circle-fill"></i>
                        </button>
                      </form>
                    </div>
                  </li>
                <?php endforeach; ?>
              <?php endif; ?>

              <?php if ($notificationCount === 0 && $notificationCountTeams === 0): ?>
                <li class="dropdown-item text-center text-muted">Aucune notification</li>
              <?php endif; ?>
            </ul>
          </div>
        <?php endif; ?><button class="btn btn-outline-dark d-flex align-items-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#navbar-offcanvas" aria-controls="navbar-offcanvas">
          <i class="bi bi-list"></i>
        </button>

      </div>

    </div>
  </div>
</header>