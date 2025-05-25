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
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1> Matchs e-sport en cours</h1>
            <a href="mes_paris.php" class="btn btn-outline-primary">
                Voir mes paris
            </a>
        </div>

        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (empty($rencontres)): ?>
            <div class="alert alert-warning">
                Aucun match en cours actuellement.
            </div>
        <?php endif; ?>

        <?php foreach ($rencontres as $tournoi): ?>
            <div class="card shadow rounded-4 mb-4">
                <div class="card-header text-white rounded-top-4" style="background-color: #ff6e40 !important;">
                    <h5 class="mb-0"><?= htmlspecialchars($tournoi['nom_tournoi']) ?>
                        <span class="badge bg-secondary ms-2"><?= htmlspecialchars($tournoi['type']) ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="parier.php" class="row gy-3 gx-2 align-items-center form-pari" data-id="<?= $tournoi['id_tournoi'] ?>">
                        <input type="hidden" name="id_tournoi" value="<?= $tournoi['id_tournoi'] ?>">
                        <input type="hidden" name="type_pari" value="<?= ($tournoi['type'] === 'equipe') ? 'equipe' : 'solo' ?>">
                        <input type="hidden" name="cote" value="">

                        <?php
                        if ($tournoi['type'] === 'equipe') {
                            $stmt = $bdd->prepare("SELECT e.id_equipe, e.nom, cp.cote FROM inscription_tournoi it JOIN equipe e ON it.id_team = e.id_equipe LEFT JOIN cote_participant cp ON cp.id_tournoi = it.id_tournoi AND cp.id_team = it.id_team WHERE it.id_tournoi = ? GROUP BY e.id_equipe, e.nom, cp.cote");
                            $stmt->execute([$tournoi['id_tournoi']]);
                            $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($participants as $equipe): ?>
                                <div class="col-md-3 col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="id_equipe" value="<?= $equipe['id_equipe'] ?>" data-cote="<?= htmlspecialchars($equipe['cote'] ?? 1) ?>" required>
                                        <label class="form-check-label fw-medium">
                                            <?= htmlspecialchars($equipe['nom']) ?>
                                            <span class="badge bg-info text-dark">Cote : <?= htmlspecialchars($equipe['cote'] ?? 1) ?></span>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach;
                        } else {
                            $stmt = $bdd->prepare("SELECT u.id_utilisateurs, u.pseudo, cp.cote FROM inscription_tournoi it JOIN utilisateurs u ON it.user_id = u.id_utilisateurs LEFT JOIN cote_participant cp ON cp.id_tournoi = it.id_tournoi AND cp.id_team = it.user_id WHERE it.id_tournoi = ?");
                            $stmt->execute([$tournoi['id_tournoi']]);
                            $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($participants as $joueur): ?>
                                <div class="col-md-3 col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="id_joueur" value="<?= $joueur['id_utilisateurs'] ?>" data-cote="<?= htmlspecialchars($joueur['cote'] ?? 1) ?>" required>
                                        <label class="form-check-label fw-medium">
                                            <?= htmlspecialchars($joueur['pseudo']) ?>
                                            <span class="badge bg-info text-dark">Cote : <?= htmlspecialchars($joueur['cote'] ?? 1) ?></span>
                                        </label>
                                    </div>
                                </div>
                        <?php endforeach;
                        } ?>

                        <div class="col-md-3 col-6">
                            <input type="number" name="montant" min="1" class="form-control" placeholder=" Montant" required>
                        </div>
                        <div class="col-md-3 col-6">
                            <button type="submit" class="btn btn-success w-100">Parier</button>
                        </div>
                        <div class="col-12">
                            <div class="alert d-none mt-2" role="alert"></div>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php include('../include/footer.php'); ?>

    <script>
        document.querySelectorAll('.form-pari').forEach(form => {
            const radios = form.querySelectorAll('input[type="radio"]');
            const hiddenCote = form.querySelector('input[name="cote"]');

            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    hiddenCote.value = this.dataset.cote || 1;
                });
            });

            const checked = form.querySelector('input[type="radio"]:checked');
            if (checked) hiddenCote.value = checked.dataset.cote || 1;

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const alertBox = form.querySelector('.alert');
                const btn = form.querySelector('button[type="submit"]');
                btn.disabled = true;

                const formData = new FormData(form);
                fetch('<?= parier ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(resp => resp.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const redirect = doc.querySelector('meta[http-equiv="refresh"]');
                        let msg = 'Pari enregistré avec succès !';

                        if (redirect) {
                            const content = redirect.getAttribute('content');
                            const url = content.split('url=')[1];
                            const params = new URLSearchParams(url.split('?')[1]);
                            msg = decodeURIComponent(params.get('message').replace(/\+/g, ' '));
                        }

                        alertBox.textContent = msg;
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