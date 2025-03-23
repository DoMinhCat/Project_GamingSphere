<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$this_page = basename($_SERVER['PHP_SELF']);
?>
<header>
  <nav class="navbar nav-underline navbar-expand-xl bg-body-tertiary my-navbar" style="padding-top: 0.2rem; padding-bottom:0.4rem;">
    <div class=" container container-fluid" style="padding: 0.5rem">
      <a href=<?= ($this_page == 'index.php') ? 'connexion/login.php' : '../connexion/login.php' ?>>
        <img src="<?= ($this_page == 'index.php') ? 'include/LOGO ENTIER 40px.png' : '../include/LOGO ENTIER 40px.png' ?>" alt="Logo" class="col-2 image-fluid" style="height: 30px; width: auto;">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
          <a class="nav-link lato16 <?= ($this_page == 'index.php') ? 'active' : '' ?>"
            href=<?= ($this_page == 'index.php') ? 'index.php' : '../index.php' ?>
            style="color: white; margin-right:1.5rem; margin-left:1.5rem;">
            Accueil
            <a class="nav-link lato16 <?= ($this_page == 'magasin_main.php') ? 'active' : '' ?>"
              href=<?= ($this_page == 'index.php') ? 'magasin/magasin_main.php' : '../magasin/magasin_main.php' ?>
              style="color: white;margin-right:1.5rem;">
              Magasin
            </a>
            <a class="nav-link lato16 <?= ($this_page == 'tournois_main.php') ? 'active' : '' ?>"
              href=<?= ($this_page == 'index.php') ? 'tournois/tournois_main.php' : '../tournois/tournois_main.php' ?>
              style="color: white;margin-right:1.5rem;">
              Tournois
            </a>
            <a class="nav-link lato16 <?= ($this_page == 'communaute_main.php') ? 'active' : '' ?>"
              href=<?= ($this_page == 'index.php') ? 'communaute/communaute_main.php' : '../communaute/communaute_main.php' ?>
              style="color: white;margin-right:1.5rem;">
              Communauté
            </a>
            <a class="nav-link lato16 <?= ($this_page == 'actualite_main.php') ? 'active' : '' ?>"
              href=<?= ($this_page == 'index.php') ? 'actualite/actualite_main.php' : '../actualite/actualite_main.php' ?>
              style="color: white;margin-right:1.5rem;">
              Actualités
            </a>
            <a class="nav-link lato16 <?= ($this_page == 'forum_main.php') ? 'active' : '' ?>"
              href=<?= ($this_page == 'index.php') ? 'forum/forum_main.php' : '../forum/forum_main.php' ?>
              style="color: white;">
              Forum
            </a>
        </div>

        <div class="d-flex ms-auto align-items-center">
        <form id="globalSearchForm" method="POST" action="/PA/include/search.php" class="d-flex align-items-center">
          <div class="input-group">
            <input type="text" id="query" name="query" class="form-control" placeholder="Rechercher..." required>
            <select name="category" id="category" class="form-select">
              <option value="">Tous</option>
              <option value="users">Utilisateurs</option>
              <option value="articles">Articles</option>
              <option value="games">Jeux</option>
            </select>
            <button type="submit" class="btn btn-primary">Rechercher</button>
          </div>
        </form>
                <div style="padding-right: 15px; padding-left: 15px;">
            <a href="message.php" class="btn btn-outline-dark">
              <i class="bi bi-chat-dots-fill"></i>
            </a>
          </div>
          <div style="padding-right: 15px;">
            <a href="panier.php" class="btn btn-outline-dark">
              <i class="bi bi-cart-fill"></i>
            </a>
          </div>
          <div class="dropdown">
            <button class="btn btn-outline-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-person-circle"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" style="padding:0;">
            <li>
                <?php
                  if (isset($_SESSION['user_email'])) {
                      $href = ($this_page == 'index.php') ? 'profil/my_account.php' : '../profil/my_account.php';  
                      $display_name = $_SESSION['user_pseudo']; 
                  } else {
                      $href = ($this_page == 'index.php') ? 'connexion/login.php' : '../connexion/login.php'; 
                      $display_name = 'Mon compte';
                  }
                ?>
                <a href="<?= $href ?>" class="dropdown-item btn btn-sm py-3"><?= htmlspecialchars($display_name) ?></a>
            </li>
              <li><button id="theme-btn" class="dropdown-item btn btn-sm py-3">Activer/Désactiver le mode nuit</button></li>
              <li><button class="dropdown-item btn btn-sm py-3" type="button">abcdef</button></li>
              <li><button class="dropdown-item btn btn-sm py-3" type="button">Deconnexion</button></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </nav>
</header>