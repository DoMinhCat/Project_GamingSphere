<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/database.php');
require('../../include/check_timeout.php');
require_once __DIR__ . '/../../path.php';
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Durée visite';
require('../head.php');
?>

<body>
    <?php
    $page = stats_main;
    include('../navbar.php');
    $stmt = $bdd->query("SELECT
    email,
    SUM(CASE WHEN utt.category = 'actualite' THEN utt.duration ELSE 0 END) AS actualite,
    SUM(CASE WHEN utt.category = 'communaute' THEN utt.duration ELSE 0 END) AS communaute,
    SUM(CASE WHEN utt.category = 'credits' THEN utt.duration ELSE 0 END) AS credits,
    SUM(CASE WHEN utt.category = 'error' THEN utt.duration ELSE 0 END) AS error,
    SUM(CASE WHEN utt.category = 'forum' THEN utt.duration ELSE 0 END) AS forum,
    SUM(CASE WHEN utt.category = 'magasin' THEN utt.duration ELSE 0 END) AS magasin,
    SUM(CASE WHEN utt.category = 'message' THEN utt.duration ELSE 0 END) AS message,
    SUM(CASE WHEN utt.category = 'panier' THEN utt.duration ELSE 0 END) AS panier,
    SUM(CASE WHEN utt.category = 'profil' THEN utt.duration ELSE 0 END) AS profil,
    SUM(CASE WHEN utt.category = 'equipe' THEN utt.duration ELSE 0 END) AS equipe,
    SUM(CASE WHEN utt.category = 'tournois' THEN utt.duration ELSE 0 END) AS tournois,
    SUM(CASE WHEN utt.category = 'accueil' THEN utt.duration ELSE 0 END) AS accueil,
    SUM(utt.duration) AS total_time
FROM
    visit_duration AS utt
JOIN
    utilisateurs AS u ON utt.id_utilisateur = u.id_utilisateurs
GROUP BY
    u.email
ORDER BY
    total_time DESC;");
    $stmt->execute();
    $lines = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <main class="container my-5">
        <h1 class="my-5 text-center">Durée de la visite sur le site</h1>
        <div class="form-group mb-2 pt-3 pb-2">
            <div class="d-flex gap-2">
                <input type="text" id="search_duree" class="form-control searchBoxBack" placeholder="Rechercher par email">
                <div class="d-flex ms-2" style="gap: 0.5rem;">
                    <select id="categoryFilter" class="form-select searchBoxBack">
                        <option value="">Tout</option>
                        <option value="actualite">Actualité</option>
                        <option value="communaute">Communauté</option>
                        <option value="credits">Crédits</option>
                        <option value="error">Erreur</option>
                        <option value="forum">Forum</option>
                        <option value="magasin">Magasin</option>
                        <option value="message">Message</option>
                        <option value="panier">Panier</option>
                        <option value="profil">Profil</option>
                        <option value="equipe">Equipe</option>
                        <option value="tournois">Tournoi</option>
                        <option value="accueil">Accueil</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
            <table class="table table-striped table-bordered">
                <thead class="table-dark" style="position: sticky; top: 0; z-index: 1;">
                    <tr>
                        <th>Utilisateur</th>
                        <th>Total</th>
                        <th>Accueil</th>
                        <th>Profil</th>
                        <th>Actualité</th>
                        <th>Communauté</th>
                        <th>Forum</th>
                        <th>Tournoi</th>
                        <th>Equipe</th>
                        <th>Message</th>
                        <th>Magasin</th>
                        <th>Crédits</th>
                        <th>Panier</th>
                        <th>Erreur</th>
                    </tr>
                </thead>
                <tbody id="list">
                    <?php foreach ($lines as $line) {
                        $email = $line['email'];
                        $total = $line['total_time'];
                        $accueil = $line['accueil'];
                        $profil = $line['profil'];
                        $actualite = $line['actualite'];
                        $commnunaute = $line['commnunaute'];
                        $forum = $line['forum'];
                        $tournois = $line['tournois'];
                        $equipe = $line['equipe'];
                        $message = $line['message'];
                        $magasin = $line['magasin'];
                        $credits = $line['credits'];
                        $panier = $line['panier'];
                        $error = $line['error'];
                    ?>
                        <tr>
                            <td class="align-middle"><?= $email ?></td>
                            <td class="align-middle"><?= $total ?></td>
                            <td class="align-middle"><?= $accueil ?></td>
                            <td class="align-middle"><?= $profil ?></td>
                            <td class="align-middle"><?= $actualite ?></td>
                            <td class="align-middle"><?= $commnunaute ?></td>
                            <td class="align-middle"><?= $forum ?></td>
                            <td class="align-middle"><?= $tournois ?></td>
                            <td class="align-middle"><?= $equipe ?></td>
                            <td class="align-middle"><?= $message ?></td>
                            <td class="align-middle"><?= $magasin ?></td>
                            <td class="align-middle"><?= $credits ?></td>
                            <td class="align-middle"><?= $panier ?></td>
                            <td class="align-middle"><?= $error ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <!-- total time sur chaque category (time all user) -->
        <!-- table time sur chaque category de chauqe user -->

    </main>
</body>

</html>