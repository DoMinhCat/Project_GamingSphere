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
                        <span class="badge bg-warning text-dark ms-2">Cote : <?= htmlspecialchars($tournoi['cote']) ?></span>
                    </h5>
                    <form class="row g-2 align-items-center form-pari" data-id="<?= $tournoi['id_tournoi'] ?>">
                        <input type="hidden" name="id_tournoi" value="<?= $tournoi['id_tournoi'] ?>">
                        <input type="hidden" name="cote" value="<?= htmlspecialchars($tournoi['cote']) ?>">
                        <?php
                        if ($tournoi['type'] === 'equipe') {
                            $stmt = $bdd->prepare("
                    SELECT e.id_equipe, e.nom 
                    FROM inscription_tournoi it
                    JOIN equipe e ON it.id_team = e.id_equipe
                    WHERE it.id_tournoi = ?
                ");
                            $stmt->execute([$tournoi['id_tournoi']]);
                            $equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($equipes as $equipe) {
                        ?>
                                <div class="col-auto">
                                    <label>
                                        <input type="radio" name="choix" value="<?= $equipe['id_equipe'] ?>" required>
                                        <?= htmlspecialchars($equipe['nom']) ?>
                                    </label>
                                </div>
                            <?php
                            }
                        } else {
                            $stmt = $bdd->prepare("
                    SELECT u.id_utilisateurs, u.pseudo 
                    FROM inscription_tournoi it
                    JOIN utilisateurs u ON it.user_id = u.id_utilisateurs
                    WHERE it.id_tournoi = ?
                ");
                            $stmt->execute([$tournoi['id_tournoi']]);
                            $joueurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($joueurs as $joueur) {
                            ?>
                                <div class="col-auto">
                                    <label>
                                        <input type="radio" name="choix" value="<?= $joueur['id_utilisateurs'] ?>" required>
                                        <?= htmlspecialchars($joueur['pseudo']) ?>
                                    </label>
                                </div>
                        <?php
                            }
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
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const alertBox = form.querySelector('.alert');
                alertBox.classList.add('d-none');
                alertBox.classList.remove('alert-success', 'alert-danger', 'fade-out', 'hide');
                const btn = form.querySelector('button[type="submit"]');
                btn.disabled = true;

                const formData = new FormData(form);
                fetch('<?= parier ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(resp => resp.text())
                    .then(html => {
                        // On attend une redirection, donc on va parser le message dans l'URL
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