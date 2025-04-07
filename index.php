<?php
session_start();
require('include/check_timeout.php');
$success = $_GET['success'] ?? "";
$user_pseudo = htmlspecialchars($_GET['user_pseudo'] ?? "");
$pseudo = htmlspecialchars($_GET['pseudo'] ?? "");
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Acceuil';
include('include/database.php');
include('include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="include/check_timeout.js"></script>';
}
?>

<body>
    <?php include('include/header.php');
    if ($success == '1' || $success == 'connected') : ?>
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
        <div id="bande_sepe"></div>
        <a href="tournois/tournois_main.php" style="text-decoration: none;">
            <h3 class="montserrat-titre40 tournament_title mt-3">TOURNOIS EN COURS</h3>
        </a>
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
        <a href="magasin/magasin_main.php" style="text-decoration: none;">
            <h3 class="montserrat-titre40 tournament_title">MAGASIN</h3>
        </a>
        <h2 class="montserrat-titre32 title_selling_item_index">Meilleurs Ventes</h2>
        <div class="d-flex flex-row flex-wrap justify-content-start g-2 sell_card_index">
            <?php
            $max_cards = 6;
            $count = 0;
            foreach ($games as $game):
                if ($count >= $max_cards) break;
                $count++;
            ?>
                <div class="card" style="width: 18rem;">
                    <img src="back-office/uploads/<?php echo htmlspecialchars($game['image']); ?>" class="card-img-top img_sell_index" alt="Image du jeu">
                    <div class="card-body d-flex flex-column align-items-center">
                        <h5 class="card-title text-center"><?php echo htmlspecialchars($game['nom']); ?></h5>
                        <p class="card-text text-center"><?php echo htmlspecialchars($game['prix']); ?> €</p>
                        <p>Ajouter au panier</p>
                        <button type="button" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bag-plus" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M8 7.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V12a.5.5 0 0 1-1 0v-1.5H6a.5.5 0 0 1 0-1h1.5V8a.5.5 0 0 1 .5-.5"></path>
                                <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
    <?php include('include/footer.php'); ?>
</body>

</html>