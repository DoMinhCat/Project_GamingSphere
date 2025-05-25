<?php
session_start();
require('../include/database.php');
require('../include/check_session.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';

$rencontres = $bdd->query("SELECT * FROM tournoi WHERE status_ENUM = 'en cours' AND pari_ouvert = 1")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Paris';
$pageCategory = 'paris';
echo "<script>const pageCategory = '$pageCategory';</script>";
require('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}
?>
<style>
    .fade-out {
        opacity: 1;
        transition: opacity 1s;
    }
    .fade-out.hide {
        opacity: 0;
    }
</style>

<body>
    <?php include('../include/header.php'); ?>
    <div class="container mt-4">
        <h1 class="mb-4">Matchs e-sport en cours</h1>

        <div class="mb-4">
            <a href="mes_paris.php" class="btn btn-outline-primary">
                🎯 Voir mes paris
            </a>
        </div>

        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-info"><?= htmlspecialchars($_GET['message']) ?></div>
        <?php endif; ?>

        <?php if (empty($rencontres)): ?>
            <p>Aucun match en cours.</p>
        <?php endif; ?>

        <?php foreach ($rencontres as $tournoi): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">
                        <?= htmlspecialchars($tournoi['nom_tournoi']) ?> (<?= htmlspecialchars($tournoi['type']) ?>)
                    </h5>
                    <form method="POST" action="parier.php" class="row g-2 align-items-center form-pari" data-id="<?= $tournoi['id_tournoi'] ?>">
                        <input type="hidden" name="id_tournoi" value="<?= $tournoi['id_tournoi'] ?>">
                        <input type="hidden" name="type_pari" value="<?= ($tournoi['type'] === 'equipe') ? 'equipe' : 'solo' ?>">
                        <input type="hidden" name="cote" value="">
                        <?php
                        if ($tournoi['type'] === 'equipe') {
                            $stmt = $bdd->prepare("
                                SELECT e.id_equipe, e.nom, cp.cote
                                FROM equipe_tournois et
                                JOIN equipe e ON et.id_equipe = e.id_equipe
                                LEFT JOIN cote_participant cp ON cp.id_tournoi = et.id_tournoi AND cp.id_team = et.id_equipe
                                WHERE et.id_tournoi = ?
                            ");
                            $stmt->execute([$tournoi['id_tournoi']]);
                            $equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($equipes as $equipe):
                        ?>
                            <div class="col-auto">
                                <label>
                                    <input type="radio" name="id_equipe" value="<?= $equipe['id_equipe'] ?>" data-cote="<?= htmlspecialchars($equipe['cote'] ?? 1) ?>" required>
                                    <?= htmlspecialchars($equipe['nom']) ?>
                                    <span class="badge bg-info text-dark ms-1">
                                        Cote : <?= htmlspecialchars($equipe['cote'] ?? 1) ?>
                                    </span>
                                </label>
                            </div>
                        <?php endforeach;
                        } else {
                            $stmt = $bdd->prepare("
                                SELECT u.id_utilisateurs, u.pseudo, cp.cote
                                FROM inscription_tournoi it
                                JOIN utilisateurs u ON it.user_id = u.id_utilisateurs
                                LEFT JOIN cote_participant cp ON cp.id_tournoi = it.id_tournoi AND cp.id_team = it.user_id
                                WHERE it.id_tournoi = ?
                            ");
                            $stmt->execute([$tournoi['id_tournoi']]);
                            $joueurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($joueurs as $joueur):
                        ?>
                            <div class="col-auto">
                                <label>
                                    <input type="radio" name="id_joueur" value="<?= $joueur['id_utilisateurs'] ?>" data-cote="<?= htmlspecialchars($joueur['cote'] ?? 1) ?>" required>
                                    <?= htmlspecialchars($joueur['pseudo']) ?>
                                    <span class="badge bg-info text-dark ms-1">
                                        Cote : <?= htmlspecialchars($joueur['cote'] ?? 1) ?>
                                    </span>
                                </label>
                            </div>
                        <?php endforeach;
                        }
                        ?>

                        <div class="col-auto">
                            <input type="number" name="montant" min="1" class="form-control" placeholder="Coins" required>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">Parier</button>
                        </div>
                        <div class="col-12 mt-2">
                            <div class="alert d-none" role="alert"></div>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>

    </div>

    <script>
        document.querySelectorAll('.form-pari').forEach(form => {
            const radios = form.querySelectorAll('input[type="radio"][name="id_equipe"], input[type="radio"][name="id_joueur"]');
            const hiddenCote = form.querySelector('input[name="cote"]');

            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    hiddenCote.value = this.getAttribute('data-cote') || 1;
                });
            });

            // Préremplir si déjà coché au chargement
            const checked = form.querySelector('input[type="radio"][name="id_equipe"]:checked, input[type="radio"][name="id_joueur"]:checked');
            if (checked) hiddenCote.value = checked.getAttribute('data-cote') || 1;
        });

        document.querySelectorAll('.form-pari').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const alertBox = form.querySelector('.alert');
                alertBox.classList.add('d-none');
                alertBox.classList.remove('alert-success', 'alert-danger', 'fade-out', 'hide');
                const btn = form.querySelector('button[type="submit"]');
                btn.disabled = true;

                const formData = new FormData(form);
                fetch('parier.php', { // lien vers le fichier PHP de traitement
                    method: 'POST',
                    body: formData
                })
                .then(resp => resp.text())
                .then(html => {
                    let msg = '';
                    try {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const redirect = doc.querySelector('meta[http-equiv="refresh"]');
                        if (redirect) {
                            const content = redirect.getAttribute('content');
                            const url = content.split('url=')[1];
                            const params = new URLSearchParams(url.split('?')[1]);
                            msg = params.get('message');
                        }
                    } catch (e) {}

                    if (!msg) msg = "Pari enregistré avec succès !";

                    alertBox.textContent = decodeURIComponent(msg.replace(/\+/g, ' '));
                    alertBox.classList.remove('d-none', 'alert-danger');
                    alertBox.classList.add('alert-success', 'fade-out');

                    setTimeout(() => alertBox.classList.add('hide'), 2000);
                })
                .catch(() => {
                    alertBox.textContent = "Erreur lors de l'enregistrement du pari.";
                    alertBox.classList.remove('d-none', 'alert-success');
                    alertBox.classList.add('alert-danger', 'fade-out');
                    setTimeout(() => alertBox.classList.add('hide'), 2000);
                })
                .finally(() => {
                    setTimeout(() => {
                        alertBox.classList.add('d-none');
                        alertBox.classList.remove('fade-out', 'hide');
                        btn.disabled = false;
                        form.reset();
                    }, 2500);
                });
            });
        });
    </script>
</body>
</html>
