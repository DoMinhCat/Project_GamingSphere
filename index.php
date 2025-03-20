<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Acceuil';
include('include/database.php');
include('include/head.php')
?>
<script src="include/carousel.js"></script>

<body>
    <?php include('include/header.php') ?>
    <?php
    session_start();
    $success = isset($_GET['success']) ? $_GET['success'] : "";
    $user_pseudo = isset($_GET['user_pseudo']) ? htmlspecialchars($_GET['user_pseudo']) : "";
    $pseudo = isset($_GET['pseudo']) ? htmlspecialchars($_GET['pseudo']) : "";
    ?>


    <?php if ($success == '1' || $success == 'connected') : ?>
        <div class="feedback text-center mb-4 mt-4" style="background-color: #f5f0e1; color:#1E3D59;">
            <?php
            if (!empty($pseudo)) {
                echo "Bienvenue " . $pseudo;
            } elseif (!empty($user_pseudo)) {
                echo "Connecté en tant que " . $user_pseudo;
            }
            ?>
        </div>
    <?php endif; ?>

    <main>
        <div class="carousel-container">
            <div id="carouselExampleIndicators" class="carousel slide carousel-fade" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
                </div>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="include/img/fortnite-tournaments.webp" class="d-block img-fluid c-img" alt="Fortnite" onclick="window.location.href='tournois/tournois_main.php'">
                        <div class="carousel-caption d-none d-md-block lato24">
                            <h5>Fortnite Tournaments</h5>
                            <p>Join the latest Fortnite tournaments and compete with the best.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="include/img/Wasted-League-of-Legends-Temps-perdus-sur-LOL-1024x538.webp" class="d-block c-img img-fluid" alt="LOL" onclick="window.location.href='actualité.php'">
                        <div class="carousel-caption d-none d-md-block lato24">
                            <h5>League of Legends News</h5>
                            <p>Stay updated with the latest news in the League of Legends community.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="include/img/Valorant-Skin-Hub.avif" class="d-block c-img img-fluid" alt="Skin Valorant" onclick="window.location.href='magasin.php'">
                        <div class="carousel-caption d-none d-md-block lato24">
                            <h5>Valorant Skins</h5>
                            <p>Explore the newest skins available in Valorant.</p>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>

        <?php if (!empty($search_results)): ?>
            <ul>
                <?php foreach ($search_results as $result): ?>
                    <li><?php echo htmlspecialchars($result['pseudo']); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div id="bande_sepe"></div>
        <h3 class="montserrat-titre40 tournament_title mt-3">TOURNOIS EN COURS</h3>
        <div class="b_l">
            <div class="row justify-content-center">
                <div class="col-md-5 mb-5">
                    <div class="card my_card d-flex flex-colum">
                        <div class="d-flex flex-row align-items-center">
                            <img src="include/img/Tournament/128px-FortniteLogo.svg.png" class="card-img-left img_trn" alt="fortnite">
                            <div class="card-body">
                                <h5 class="card-title lato24">Nom du tournoi :</h5>
                                <p class="card-text lato16">Date du tournoi :</p>
                            </div>
                        </div>
                        <ul class="list-group list-group-flush lato16 d-i">
                            <li class="list-group-item">Equipe :</li>
                            <li class="list-group-item">Prize pool :</li>
                            <li class="list-group-item">Prix d'inscription :</li>
                        </ul>
                        <div class="card-body text-center">
                            <a href="#" class="card-link montserrat-titre32">Plus d'infos ...</a>
                            <button class="btn-primary" onclick="window.location.href='tournois.php'">S'inscrire</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 mb-5">
                    <div class="card my_card d-flex flex-column">
                        <div class="d-flex flex-row align-items-center">
                            <img src="include/img/Tournament/League_of_Legends_2019_vector.svg.png" class="card-img-left img_trn" alt="lol">
                            <div class="card-body">
                                <h5 class="card-title lato24">Nom du tournoi :</h5>
                                <p class="card-text lato16">Date du tournoi :</p>
                            </div>
                        </div>
                        <ul class="list-group list-group-flush lato16">
                            <li class="list-group-item">Equipe :</li>
                            <li class="list-group-item">Prize pool :</li>
                            <li class="list-group-item">Prix d'inscription :</li>
                        </ul>
                        <div class="card-body text-center">
                            <a href="#" class="card-link montserrat-titre32">Plus d'infos ...</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-5 mb-5">
                    <div class="card my_card d-flex flex-column">
                        <div class="d-flex flex-row align-items-center">
                            <img src="include/img/Tournament/Rocket_League_logo.svg.png" class="card-img-left img_trn" alt="fortnite">
                            <div class="card-body">
                                <h5 class="card-title lato24">Nom du tournoi :</h5>
                                <p class="card-text lato16">Date du tournoi :</p>
                            </div>
                        </div>
                        <ul class="list-group list-group-flush lato16">
                            <li class="list-group-item">Equipe :</li>
                            <li class="list-group-item">Prize pool :</li>
                            <li class="list-group-item">Prix d'inscription :</li>
                        </ul>
                        <div class="card-body text-center">
                            <a href="#" class="card-link montserrat-titre32">Plus d'infos ...</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 mb-5">
                    <div class="card my_card d-flex flex-column">
                        <div class="d-flex flex-row align-items-center">
                            <img src="include/img/Tournament/Valorant_logo_-_pink_color_version.svg.png" class="card-img-left img_trn" alt="valorant">
                            <div class="card-body">
                                <h5 class="card-title lato24">Nom du tournoi :</h5>
                                <p class="card-text lato16">Date du tournoi :</p>
                            </div>
                        </div>
                        <ul class="list-group list-group-flush lato16">
                            <li class="list-group-item">Equipe :</li>
                            <li class="list-group-item">Prize pool :</li>
                            <li class="list-group-item">Prix d'inscription :</li>
                        </ul>
                        <div class="card-body text-center">
                            <a href="#" class="card-link montserrat-titre32">Plus d'infos ...</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>


<?php
        $stmt = $bdd->query("SELECT nom, prix, image FROM jeu");
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
        <div id="bande_sepe"></div>
<h3 class="montserrat-titre40 tournament_title mt-3">MAGASIN</h3>
<div class="row row-cols-1 row-cols-md-6 g-2 p-2 text-center">
    <?php foreach ($games as $game): ?>
        <div class="col">
            <div class="card">
                <img src="back-office/uploads/<?php echo htmlspecialchars($game['image']); ?>" class="card-img-top" alt="Image du jeu">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($game['nom']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($game['prix']); ?> €</p>
                    <i class="bi bi-bag-plus"></i>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

            <div class="col">
            <div class="card">
                <img src="" class="card-img-top" alt="">
                <div class="card-body">
                <h5 class="card-title">Nom du jeu</h5>
                <p class="card-text">PRIX</p>
                <i class="bi bi-bag-plus"></i>
                </div>
            </div>
            </div>
            <div class="col">
            <div class="card">
                <img src="" class="card-img-top" alt="">
                <div class="card-body">
                <h5 class="card-title">Nom du jeu</h5>
                <p class="card-text">PRIX</p>
                <i class="bi bi-bag-plus"></i>
                </div>
            </div>
            </div>
            <div class="col">
            <div class="card">
                <img src="" class="card-img-top" alt="">
                <div class="card-body">
                <h5 class="card-title">Nom du jeu</h5>
                <p class="card-text">PRIX</p>
                <i class="bi bi-bag-plus"></i>
                </div>
            </div>
            </div>
            <div class="col">
            <div class="card">
                <img src="include/img/fortnite-tournaments.webp" class="card-img-top" alt="">
                <div class="card-body">
                <h5 class="card-title">Nom du jeu</h5>
                <p class="card-text">PRIX</p>
                <i class="bi bi-bag-plus"></i>
                </div>
            </div>
            </div>
            <div class="col">
            <div class="card">
                <img src="include/background_login.png" class="card-img-top" alt="">
                <div class="card-body">
                <h5 class="card-title">Nom du jeu</h5>
                <p class="card-text">PRIX</p>
                <i class="bi bi-bag-plus"></i>
                </div>
            </div>
            </div>
        </div>

    </main>
    <?php include('include/footer.php'); ?>
</body>


</html>