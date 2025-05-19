<?php
session_start();
require('include/database.php');
require('include/check_timeout.php');
require_once __DIR__ . '/path.php';

$success = $_GET['success'] ?? "";
$user_pseudo = htmlspecialchars($_GET['user_pseudo'] ?? "");
$pseudo = htmlspecialchars($_GET['pseudo'] ?? "");
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Acceuil';
$pageCategory = 'accueil';
echo "<script>const pageCategory = '$pageCategory';</script>";
require('include/head.php');
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
    <?php endif;

    if (isset($_GET['message']) && $_GET['message'] == 'email_verifie') { ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            Vous avez déjà vérifié votre email !
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php }
    if (isset($_GET['message']) && $_GET['message'] == 'bdd') { ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            Erreur de la base de données, veuillez reéssayer plus tard ! !
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php } ?>

    <main class="mb-5">
        <div class="carousel-container">
            <div id="carouselExampleIndicators" class="carousel slide carousel-fade" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
                </div>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="include/img/fortnite-tournaments.webp" class="d-block img-fluid c-img" alt="Fortnite" onclick="window.location.href='<?= tournois_main ?>'">
                        <div class="carousel-caption d-none d-md-block lato24">
                            <h5>Fortnite Tournaments</h5>
                            <p>Join the latest Fortnite tournaments and compete with the best.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="include/img/Wasted-League-of-Legends-Temps-perdus-sur-LOL-1024x538.webp" class="d-block c-img img-fluid" alt="LOL" onclick="window.location.href='<?= actualite_main ?>'">
                        <div class="carousel-caption d-none d-md-block lato24">
                            <h5>League of Legends News</h5>
                            <p>Stay updated with the latest news in the League of Legends community.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="include/img/Valorant-Skin-Hub.avif" class="d-block c-img img-fluid" alt="Skin Valorant" onclick="window.location.href='<?= magasin_main ?>'">
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
        <a href="<?= tournois_main ?>" style="text-decoration: none;">
            <h3 class="montserrat-titre40 tournament_title my-3">TOURNOIS EN COURS</h3>
        </a>
        <div class="b_l mx-5">
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
                            <button class="btn-primary" onclick="window.location.href='<?= tournois_main ?>'">S'inscrire</button>
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
        <a href="<?= magasin_main ?>" style="text-decoration: none;">
            <h3 class="montserrat-titre40 tournament_title mb-3">MAGASIN</h3>
        </a>

        <div class="d-flex flex-row">
            <h3 class="title_selling_item_index">Meilleurs Ventes</h3>
            <div class="d-flex flex-row flex-wrap justify-content-start g-2 sell_card_index mx-5">
                <?php
                $max_cards = 6;
                $count = 0;
                foreach ($games as $game):
                    if ($count >= $max_cards) break;
                    $count++;
                ?>
                    <div class="col-md-4 mb-4 d-flex align-items-stretch">
                        <div class="card shadow-sm w-100 d-flex flex-column">
                            <?php if (!empty($game['image'])): ?>
                                <img src="../back-office/uploads/<?= htmlspecialchars($game['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($game['nom']) ?>">
                            <?php else: ?>
                                <img src="../../assets/img/no_image.png" class="card-img-top" alt="Aucune image">
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($game['nom']) ?></h5>
                                <p class="card-text"><strong>Prix :</strong> <?= htmlspecialchars($game['prix']) ?> €</p>
                                <div class="mt-auto d-flex justify-content-between gap-2 align-items-center">
                                    <a href="<?= magasin_game ?>?id=<?= $game['id_jeu'] ?>" class="btn btn-magasin btn-outline-primary w-50 mt-3 h-50">Voir détails</a>
                                    <button class="btn btn-magasin btn-success mt-3 btn-add-to-cart h-50" data-id="<?= $game['id_jeu'] ?>">Ajouter au panier</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
    <?php include('include/footer.php'); ?>
</body>

</html>