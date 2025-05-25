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




<header class="sticky-top">

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
              <span class="position-relative top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $panierCount ?></span>
            <?php else: ?>
              <span class="position-relative top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display:none;"></span>
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
          <a class="nav-link ms-4 lato16 px-2 py-1 <?= ($this_page == 'magasin_main.php' || $this_page == 'game_info.php' || $this_page == 'magasin_category.php') ? 'active' : '' ?>" href="<?= magasin_main ?>" style="color: #F5F0E1 !important;">
            Magasin
          </a>
          <a class="nav-link lato16 ms-4 px-2 py-1 <?= ($this_page == 'tournois_main.php' || $this_page == 'create_team.php' || $this_page == 'tournois_category.php' || $this_page == 'tournois_details.php' || $this_page == 'tournois_jeux.php' || $this_page == 'team_details.php' || $this_page == 'team_list.php') ? 'active' : '' ?>" href="<?= tournois_main ?>" style="color: #F5F0E1 !important;">
            Tournois
          </a>
          <?php if (!empty($_SESSION['user_id'])) { ?>
            <a class="nav-link lato16 ms-4 px-2 py-1 <?= ($this_page == 'paris_main.php' || $this_page == 'mes_paris.php') ? 'active' : '' ?>" href="<?= paris_main ?>" style="color: #F5F0E1 !important;">
              Paris
            </a>
          <?php } ?>
          <a class="nav-link lato16 ms-4 px-2 py-1 <?= ($this_page == 'actualite_main.php' || $this_page == 'actualite_article.php' || $this_page == 'actualite_categorie.php') ? 'active' : '' ?>" href="<?= actualite_main ?>" style="color: #F5F0E1 !important;">
            Actualités
          </a>
          <a class="nav-link lato16 ms-4 px-2 py-1 <?= ($this_page == 'forum_main.php' || $this_page == 'categorie.php' || $this_page == 'nouveau_sujet.php' || $this_page == 'sujet.php') ? 'active' : '' ?>" href="<?= forum_main ?>" style="color: #F5F0E1 !important;">
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
                        <a href="<?= profil . '?user=' . urlencode($request['pseudo']) ?>" class="dropdown-item">
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
                  <li class="dropdown-item text-center">Aucune notification</li>
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
                <li><a id="btn-deconnexion" href="/connexion/deconnexion.php" class="dropdown-item btn btn-sm py-3">
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
            <img src="/include/LOGO ENTIER 40px.png" alt="Logo" class="img-fluid" style="height: 30px; width: auto;">
          </a>
        </div>

        <div class="col-9 d-flex justify-content-end align-items-center px-2 gap-2">
          <?php if (isset($_SESSION['user_email'])): ?>
            <a href="<?= messagerie ?>" class="btn btn-outline-dark d-flex align-items-center justify-content-center p-2">
              <i class="bi bi-chat-dots-fill"></i>
            </a>
          <?php endif; ?>

          <?php if (isset($_SESSION['user_email'])) : ?>
            <div class="dropdown">
              <button class="btn btn-outline-dark position-relative d-flex align-items-center justify-content-center p-2"
                id="notification-btn"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="bi bi-bell-fill"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                  id="notification-count"
                  style="display: <?= ($notificationCount + $notificationCountTeams) > 0 ? 'inline' : 'none'; ?>;">
                  <?= $notificationCount + $notificationCountTeams ?>
                </span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0"
                id="notification-menu"
                style="padding: 0; max-height: 300px; overflow-y: auto; min-width: 280px;">

                <?php if ($notificationCount > 0): ?>
                  <?php foreach ($friendRequests as $request): ?>
                    <li class="dropdown-item-text p-3 border-bottom">
                      <div class="d-flex flex-column">
                        <div class="mb-2">
                          <a href="<?= profil . '?user=' . urlencode($request['pseudo']) ?>"
                            class="text-decoration-none text-dark fw-bold">
                            Demande d'ami
                          </a>
                          <div class="small text-muted">
                            de <?= htmlspecialchars($request['pseudo']) ?> - <?= date('d/m/Y', strtotime($request['date_début'])) ?>
                          </div>
                        </div>
                        <div class="d-flex gap-2">
                          <form action="/profil/accept_friend_request.php" method="POST" class="flex-fill">
                            <input type="hidden" name="friend_pseudo" value="<?= htmlspecialchars($request['pseudo']) ?>">
                            <button type="submit" class="btn btn-success btn-sm w-100" title="Accepter">
                              <i class="bi bi-check-circle-fill me-1"></i>Accepter
                            </button>
                          </form>
                          <form action="/profil/reject_friend_request.php" method="POST" class="flex-fill">
                            <input type="hidden" name="friend_pseudo" value="<?= htmlspecialchars($request['pseudo']) ?>">
                            <button type="submit" class="btn btn-danger btn-sm w-100" title="Refuser">
                              <i class="bi bi-x-circle-fill me-1"></i>Refuser
                            </button>
                          </form>
                        </div>
                      </div>
                    </li>
                  <?php endforeach; ?>
                <?php endif; ?>

                <?php if ($notificationCountTeams > 0): ?>
                  <?php foreach ($teamRequests as $request): ?>
                    <li class="dropdown-item-text p-3 border-bottom">
                      <div class="d-flex flex-column">
                        <div class="mb-2">
                          <div class="fw-bold">Demande de rejoindre</div>
                          <div class="small text-muted">
                            l'équipe <?= htmlspecialchars($request['equipe']) ?> par <?= htmlspecialchars($request['demandeur']) ?> - <?= date('d/m/Y', strtotime($request['date_invitation'])) ?>
                          </div>
                        </div>
                        <div class="d-flex gap-2">
                          <form action="/team/accept_team.php" method="POST" class="flex-fill">
                            <input type="hidden" name="invitation_id" value="<?= htmlspecialchars($request['id_invitation']) ?>">
                            <button type="submit" class="btn btn-success btn-sm w-100" title="Accepter">
                              <i class="bi bi-check-circle-fill me-1"></i>Accepter
                            </button>
                          </form>
                          <form action="team/reject_team.php" method="POST" class="flex-fill">
                            <input type="hidden" name="invitation_id" value="<?= htmlspecialchars($request['id_invitation']) ?>">
                            <button type="submit" class="btn btn-danger btn-sm w-100" title="Refuser">
                              <i class="bi bi-x-circle-fill me-1"></i>Refuser
                            </button>
                          </form>
                        </div>
                      </div>
                    </li>
                  <?php endforeach; ?>
                <?php endif; ?>

                <?php if ($notificationCount === 0 && $notificationCountTeams === 0): ?>
                  <li class="dropdown-item-text text-center p-4 text-muted">
                    <i class="bi bi-bell-slash d-block mb-2 fs-4"></i>
                    Aucune notification
                  </li>
                <?php endif; ?>
              </ul>
            </div>
          <?php endif; ?>

          <!-- mobile menu -->
          <button class="btn btn-outline-dark d-flex align-items-center justify-content-center p-2"
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#offcanvasWithBothOptions"
            aria-controls="offcanvasWithBothOptions">
            <i class="bi bi-list fs-5"></i>
          </button>

          <div class="offcanvas offcanvas-end"
            data-bs-scroll="true"
            tabindex="-1"
            id="offcanvasWithBothOptions"
            aria-labelledby="offcanvasWithBothOptionsLabel">

            <div class="offcanvas-header py-3 border-bottom">
              <h5 class="offcanvas-title fw-bold" id="offcanvasWithBothOptionsLabel">Menu</h5>
              <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>

            <div class="offcanvas-body py-0">
              <div class="d-flex flex-column h-100">

                <!-- profil -->
                <div class="border-bottom py-3">
                  <div class="dropdown">
                    <button class="btn btn-outline-dark dropdown-toggle w-100 d-flex align-items-center justify-content-between"
                      type="button"
                      data-bs-toggle="dropdown"
                      aria-expanded="false">
                      <span class="d-flex align-items-center">
                        <i class="bi bi-person-circle me-2"></i>
                        <?php if (isset($_SESSION['user_email'])) {
                          echo htmlspecialchars($_SESSION['user_pseudo']);
                        } else {
                          echo 'Compte';
                        } ?>
                      </span>
                    </button>
                    <ul class="dropdown-menu w-100 shadow-sm border-0">
                      <?php if (isset($_SESSION['user_email'])) {
                        $href = my_account;
                        $display_name = 'Mon compte';
                        $link = credits_main;
                      } else {
                        $href = login;
                        $display_name = 'Se connecter';
                      }
                      ?>
                      <li>
                        <a href="<?= $href ?>" class="dropdown-item py-3">
                          <i class="bi bi-person-gear me-2"></i><?php echo htmlspecialchars($display_name) ?>
                        </a>
                      </li>
                      <?php if (isset($_SESSION['user_email'])): ?>
                        <li>
                          <a href="<?= $link ?>" class="dropdown-item py-3">
                            <i class="bi bi-wallet2 me-2"></i>
                            Crédits : <?= htmlspecialchars($credits) ?>
                          </a>
                        </li>
                      <?php endif ?>
                      <li>
                        <button id="theme-btn-mobile" class="dropdown-item py-3 w-100 text-start border-0 bg-transparent">
                          <i class="bi bi-moon-stars me-2"></i>Mode nuit
                        </button>
                      </li>
                      <?php if (isset($_SESSION['user_email'])): ?>
                        <li>
                          <a href="/connexion/deconnexion.php" class="dropdown-item py-3 text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                          </a>
                        </li>
                      <?php endif ?>
                    </ul>
                  </div>
                </div>

                <!-- back office -->
                <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === true): ?>
                  <div class="py-3 border-bottom">
                    <a href="<?= index_back ?>" class="btn btn-warning w-100 d-flex align-items-center justify-content-center"
                      style="background-color: #ffc107; color: #212529; border-radius: 10px; font-weight: bold; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                      <i class="bi bi-gear-fill me-2"></i>Back office
                    </a>
                  </div>
                <?php endif; ?>

                <!-- Search -->
                <div class="py-3 border-bottom">
                  <form id="globalSearchForm_mobile" method="POST" action="<?= search ?>">
                    <div class="input-group">
                      <input type="text"
                        id="query"
                        name="query"
                        class="form-control"
                        placeholder="Rechercher..."
                        required>
                      <select name="category"
                        id="category_mobile"
                        class="form-select"
                        style="max-width: 110px;">
                        <option value="">Tous</option>
                        <option value="users">Utilisateurs</option>
                        <option value="articles">Articles</option>
                        <option value="games">Jeux</option>
                      </select>
                    </div>
                  </form>
                </div>

                <!-- Navigation -->
                <div class="flex-fill py-3">
                  <nav class="nav flex-column">
                    <a class="nav-link d-flex align-items-center py-3 <?= ($this_page == 'index.php') ? 'active fw-bold' : 'text-dark' ?>"
                      href="<?= index_front ?>">
                      <i class="bi bi-house-door me-3"></i>Accueil
                    </a>
                    <a class="nav-link d-flex align-items-center py-3 <?= ($this_page == 'magasin_main.php' || $this_page == 'magasin_category.php' || $this_page == 'game_info.php') ? 'active fw-bold' : 'text-dark' ?>"
                      href="<?= magasin_main ?>">
                      <i class="bi bi-shop me-3"></i>Magasin
                    </a>
                    <a class="nav-link d-flex align-items-center py-3 <?= ($this_page == 'tournois_main.php' || $this_page == 'create_team.php' || $this_page == 'tournois_details.php' || $this_page == 'tournois_jeux.php' || $this_page == 'tournois_category.php' || $this_page == 'team_details.php' || $this_page == 'team_list.php') ? 'active fw-bold' : 'text-dark' ?>"
                      href="<?= tournois_main ?>">
                      <i class="bi bi-trophy me-3"></i>Tournois
                    </a>
                    <?php if (!empty($_SESSION['user_id'])) { ?>
                      <a class="nav-link d-flex align-items-center py-3 <?= ($this_page == 'paris_main.php' || $this_page == 'mes_paris.php') ? 'active fw-bold' : 'text-dark' ?>"
                        href="<?= paris_main ?>">
                        <i class="bi bi-people me-3"></i>Communauté
                      </a>
                    <?php } ?>
                    <a class="nav-link d-flex align-items-center py-3 <?= ($this_page == 'actualite_main.php' || $this_page == 'actualite_article.php' || $this_page == 'actualite_categorie.php') ? 'active fw-bold' : 'text-dark' ?>"
                      href="<?= actualite_main ?>">
                      <i class="bi bi-newspaper me-3"></i>Actualités
                    </a>
                    <a class="nav-link d-flex align-items-center py-3 <?= ($this_page == 'forum_main.php' || $this_page == 'categorie.php' || $this_page == 'nouveau_sujet.php' || $this_page == 'sujet.php') ? 'active fw-bold' : 'text-dark' ?>"
                      href="<?= forum_main ?>">
                      <i class="bi bi-chat-square-text me-3"></i>Forum
                    </a>
                  </nav>
                </div>

                <!-- Cart -->
                <div class="py-3 border-top mt-auto">
                  <a href="<?= panier ?>" class="btn btn-outline-dark w-100 d-flex align-items-center justify-content-center">
                    <i class="bi bi-cart-fill me-2"></i>Panier
                  </a>
                </div>

              </div>
            </div>
          </div>

        </div>

      </div>
    </div>
  </div>
</header>