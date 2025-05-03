<?php
require_once __DIR__ . '/../path.php';
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
          JOIN equipe e ON i.id_equipe = e.id_equipe
          JOIN membres_equipe me ON e.id_equipe = me.id_equipe
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

$panierCount = isset($_SESSION['panier']) ? count($_SESSION['panier']) : 0;

?>




<header>

  <!-- uppper -->
  <div id="upper_header">
    <div class="container">
      <div class=" row d-none d-xl-flex justify-content-end align-items-center py-2 container-fluid px-0 mx-0">
        <div class="col-12 col-md-6 d-flex justify-content-end align-items-center p-0 mx-0">
          <form id="globalSearchForm" method="POST" action="<?= search ?>" class="d-flex align-items-center">
            <div class="input-group justify-content-end">
              <input type="text" id="query_mobile" name="query" class="form-control-sm col-6 w-50" style="border: 0.3rem;" placeholder="Rechercher" required>
              <select name="category" id="category" class="form-select-sm" style="border: 0.3rem;">
                <option value="">Tous</option>
                <option value="users">Utilisateurs</option>
                <option value="articles">Articles</option>
                <option value="games">Jeux</option>
              </select>
            </div>
          </form>
          <a href="<?= panier ?>" class="btn btn-outline-dark d-flex align-items-center ms-3">
            <i class="bi bi-cart-fill"></i>
            <?php if ($panierCount > 0): ?>
              <span class="badge bg-danger position-absolute top-0 start-100 translate-middle p-2 rounded-circle panier-badge"><?= $panierCount ?></span>
            <?php else: ?>
              <span class="badge bg-danger position-absolute top-0 start-100 translate-middle p-2 rounded-circle panier-badge" style="display:none;"></span>
            <?php endif; ?>
          </a>
          <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === true): ?>
            <a href="<?= index_back ?>" class="btn btn-warning ms-2 px-2" style="background-color: #ffc107; color: #212529; border-radius: 10px; font-weight: bold; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
              <i class="bi bi-gear-fill"></i>Back office
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <!-- lower -->
  <div id="lower_header">
    <div class="container">
      <div class="row d-none d-xl-flex justify-content-between align-items-center py-3 px-0 mx-0 container-fluid">

        <div class="col-3 d-flex justify-content-start">
          <a href="<?= index_front ?>">
            <img src="/include/LOGO ENTIER 40px.png" alt="Logo" class="col-2 image-fluid" style="height: 30px; width: auto;">
          </a>
        </div>

        <div class="col-6 d-flex justify-content-center nav-underline">
          <a class="nav-link lato16 px-2 py-1 <?= ($this_page == 'index.php') ? 'active' : '' ?>" href="<?= index_front ?>" style="color: #F5F0E1 !important;">
            Accueil
          </a>
          <a class="nav-link ms-4 lato16 px-2 py-1 <?= ($this_page == 'magasin_main.php') ? 'active' : '' ?>" href="<?= magasin_main ?>" style="color: #F5F0E1 !important;">
            Magasin
          </a>
          <a class="nav-link lato16 ms-4 px-2 py-1 <?= ($this_page == 'tournois_main.php') ? 'active' : '' ?>" href="<?= tournois_main ?>" style="color: #F5F0E1 !important;">
            Tournois
          </a>
          <a class="nav-link lato16 ms-4 px-2 py-1 <?= ($this_page == 'communaute_main.php') ? 'active' : '' ?>" href="<?= communaute_main ?>" style="color: #F5F0E1 !important;">
            Communauté
          </a>
          <a class="nav-link lato16 ms-4 px-2 py-1 <?= ($this_page == 'actualite_main.php') ? 'active' : '' ?>" href="<?= actualite_main ?>" style="color: #F5F0E1 !important;">
            Actualités
          </a>
          <a class="nav-link lato16 ms-4 px-2 py-1 <?= ($this_page == 'forum.php') ? 'active' : '' ?>" href="<?= forum_main ?>" style="color: #F5F0E1 !important;">
            Forum
          </a>
        </div>

        <div class="col-3 d-flex justify-content-end px-0">

          <?php if (isset($_SESSION['user_email'])) : ?>
            <a href=<?= messagerie ?> class="btn btn-outline-dark d-flex align-items-center ms-2 position-relative">
              <i class="bi bi-chat-dots-fill"></i>
              <?php if (isset($_SESSION['user_email']) && ($notificationCountMessages > 0)): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.75rem;">
                  <?= $notificationCountMessages ?>
                </span>
              <?php endif; ?>
            </a>

            <div class="dropdown ms-2">
              <button class="btn btn-outline-dark position-relative d-flex align-items-center me-2" id="notification-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-bell-fill"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notification-count" style="display: <?= ($notificationCount + $notificationCountTeams) > 0 ? 'inline' : 'none'; ?>;">
                  <?= $notificationCount + $notificationCountTeams ?>
                </span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end" id="notification-menu" style="padding: 0; max-height: 300px; overflow-y: auto;">

                <?php if ($notificationCount > 0): ?>
                  <?php foreach ($friendRequests as $request): ?>
                    <li class="d-flex justify-content-between align-items-center">
                      <div>
                        <a href="<?= profil . '?user=' . urlencode($request['pseudo']) ?>" class="dropdown-item text-dark">
                          <strong>Demande d'ami</strong> de <?= htmlspecialchars($request['pseudo']) ?> - <?= date('d/m/Y', strtotime($request['date_début'])) ?> </a>
                      </div>
                      <div class="btn-group ms-1">
                        <form action="/profil/accept_friend_request.php" method="POST">
                          <input type="hidden" name="friend_pseudo" value="<?= htmlspecialchars($request['pseudo']) ?>">
                          <button type="submit" class="btn btn-success btn-sm" title="Accepter">
                            <i class="bi bi-check-circle-fill"></i>
                          </button>
                        </form>
                        <form action="/profil/reject_friend_request.php" method="POST">
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
                        <form action="/team/accept_team.php" method="POST" style="display: inline;">
                          <input type="hidden" name="invitation_id" value="<?= htmlspecialchars($request['id_invitation']) ?>">
                          <button type="submit" class="btn btn-success btn-sm" title="Accepter">
                            <i class="bi bi-check-circle-fill"></i>
                          </button>
                        </form>
                        <form action="team/reject_team.php" method="POST" style="display: inline;">
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

          <div class="dropdown">
            <button class="btn btn-outline-dark dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-person-circle"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" style="padding: 0;">
              <?php if (isset($_SESSION['user_email'])) {
                $href = my_account;
                $display_name = 'Mon compte - ' . $_SESSION['user_pseudo'];
                $link = credits_main;
              } else {
                $href = login;
                $display_name = 'Se connecter';
              }
              ?>
              <li><a href="<?= $href ?>" class="dropdown-item btn btn-sm py-3"><?php echo htmlspecialchars($display_name) ?></a></li>
              <?php if (isset($_SESSION['user_email'])): ?>
                <li><a href="<?= $link ?>" class="dropdown-item btn btn-sm py-3">
                    <span class="d-flex align-items-center">
                      <i class="bi bi-wallet2 me-2" style="font-size: 1rem;"></i>
                      Crédits : <?= htmlspecialchars($credits) ?>
                    </span>
                  </a></li>
              <?php endif ?>
              <li><button id="theme-btn" class="dropdown-item btn btn-sm py-3">Activer/Désactiver le mode nuit</button></li>
              <?php
              if (isset($_SESSION['user_email'])): ?>
                <li><a href="/connexion/deconnexion.php" class="dropdown-item btn btn-sm py-3">
                    Déconnexion
                  </a></li>
              <?php endif ?>
            </ul>
          </div>



        </div>

      </div>
    </div>
  </div>
  <!-- single -->
  <div id="single_header">
    <div class="mx-3 justify-content-between">
      <div class="row d-flex d-xl-none justify-content-between align-items-center p-2">
        <div class="col-3 d-flex justify-content-start px-2">
          <a href="<?= index_front ?>">
            <img src="/include/LOGO ENTIER 40px.png" alt="Logo" class="col-2 image-fluid" style="height: 30px; width: auto;">
          </a>
        </div>

        <div class="col-9 d-flex justify-content-end px-2">
          <?php if (isset($_SESSION['user_email'])): ?>
            <a href="<?= messagerie ?>" class="btn btn-outline-dark d-flex align-items-center me-2">
              <i class=" bi bi-chat-dots-fill"></i>
            </a>
          <?php endif; ?>
          <?php if (isset($_SESSION['user_email'])) : ?>
            <div class="dropdown me-2">
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
                        <a href="<?= profil . '?user=' . urlencode($request['pseudo']) ?>" class="text-dark">
                          <strong>Demande d'ami</strong> de <?= htmlspecialchars($request['pseudo']) ?> - <?= date('d/m/Y', strtotime($request['date_début'])) ?>
                        </a>
                      </div>
                      <div class="btn-group">
                        <form action="/profil/accept_friend_request.php" method="POST">
                          <input type="hidden" name="friend_pseudo" value="<?= htmlspecialchars($request['pseudo']) ?>">
                          <button type="submit" class="btn btn-success btn-sm" title="Accepter">
                            <i class="bi bi-check-circle-fill"></i>
                          </button>
                        </form>
                        <form action="/profil/reject_friend_request.php" method="POST">
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
                        <form action="/team/accept_team.php" method="POST" style="display: inline;">
                          <input type="hidden" name="invitation_id" value="<?= htmlspecialchars($request['id_invitation']) ?>">
                          <button type="submit" class="btn btn-success btn-sm" title="Accepter">
                            <i class="bi bi-check-circle-fill"></i>
                          </button>
                        </form>
                        <form action="team/reject_team.php" method="POST" style="display: inline;">
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

          <!-- mobile -->
          <button class="btn btn-outline-dark d-flex align-items-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions">
            <i class="bi bi-list"></i>
          </button>

          <div class="offcanvas offcanvas-end"
            data-bs-scroll="true"
            tabindex="-1"
            id="offcanvasWithBothOptions"
            aria-labelledby="offcanvasWithBothOptionsLabel">

            <div class="offcanvas-header py-3">
              <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>

            <div class="offcanvas-body py-2">
              <div class="d-flex flex-column">
                <div class="dropdown">
                  <button class="btn btn-outline-dark dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end" style="padding: 0;">
                    <?php if (isset($_SESSION['user_email'])) {
                      $href = my_account;
                      $display_name = 'Mon compte - ' . $_SESSION['user_pseudo'];
                      $link = credits_main;
                    } else {
                      $href = login;
                      $display_name = 'Se connecter';
                    }
                    ?>
                    <li><a href="<?= $href ?>" class="dropdown-item btn btn-sm py-3"><?php echo htmlspecialchars($display_name) ?></a></li>
                    <?php if (isset($_SESSION['user_email'])): ?>
                      <li><a href="<?= $link ?>" class="dropdown-item btn btn-sm py-3">
                          <span class="d-flex align-items-center">
                            <i class="bi bi-wallet2 me-2" style="font-size: 1rem;"></i>
                            Crédits : <?= htmlspecialchars($credits) ?>
                          </span>
                        </a></li>
                    <?php endif ?>
                    <li><button id="theme-btn-mobile" class="dropdown-item btn btn-sm py-3">Activer/Désactiver le mode nuit</button></li>
                    <?php
                    if (isset($_SESSION['user_email'])): ?>
                      <li><a href="/connexion/deconnexion.php" class="dropdown-item btn btn-sm py-3">
                          Déconnexion
                        </a></li>
                    <?php endif ?>
                  </ul>
                </div>
                <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === true): ?>
                  <a href="<?= index_back ?>" class="btn btn-warning px-2 m-3" style="background-color: #ffc107; color: #212529; border-radius: 10px; font-weight: bold; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <i class="bi bi-gear-fill"></i>Back office
                  </a>
                <?php endif; ?>

                <form id="globalSearchForm_mobile" method="POST" action="<?= search ?>" class="d-flex align-items-center">
                  <div class="input-group justify-content-end">
                    <input type="text" id="query" name="query" class="form-control-sm col-3 w-50" style="border: 0.3rem;" placeholder="Rechercher" required>
                    <select name="category" id="category_mobile" class="form-select form-select-sm" style="border: 0.1rem; font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                      <option value="">Tous</option>
                      <option value="users">Utilisateurs</option>
                      <option value="articles">Articles</option>
                      <option value="games">Jeux</option>
                    </select>
                  </div>
                </form>

                <div class="col-6 d-flex flex-column">
                  <a class="nav-link lato16 px-2 py-1 <?= ($this_page == 'index.php') ? 'active' : '' ?>" href="<?= index_front ?>" style="color: #F5F0E1 !important;">
                    Accueil
                  </a>
                  <a class="nav-link ms-4 lato16 px-2 py-1 <?= ($this_page == 'magasin_main.php') ? 'active' : '' ?>" href="<?= magasin_main ?>" style="color: #F5F0E1 !important;">
                    Magasin
                  </a>
                  <a class="nav-link lato16 ms-4 px-2 py-1 <?= ($this_page == 'tournois_main.php') ? 'active' : '' ?>" href="<?= tournois_main ?>" style="color: #F5F0E1 !important;">
                    Tournois
                  </a>
                  <a class="nav-link lato16 ms-4 px-2 py-1 <?= ($this_page == 'communaute_main.php') ? 'active' : '' ?>" href="<?= communaute_main ?>" style="color: #F5F0E1 !important;">
                    Communauté
                  </a>
                  <a class="nav-link lato16 ms-4 px-2 py-1 <?= ($this_page == 'actualite_main.php') ? 'active' : '' ?>" href="<?= actualite_main ?>" style="color: #F5F0E1 !important;">
                    Actualités
                  </a>
                  <a class="nav-link lato16 ms-4 px-2 py-1 <?= ($this_page == 'forum.php') ? 'active' : '' ?>"
                    href="<?= forum_main ?>"
                    style="color: #F5F0E1 !important;">
                    Forum
                  </a>
                </div>

                <a href="<?= panier ?>" class="btn btn-outline-dark d-flex align-items-center my-2">
                  <i class="bi bi-cart-fill"></i>
                </a>
              </div>
            </div>
          </div>

        </div>

      </div>
    </div>
  </div>
</header>