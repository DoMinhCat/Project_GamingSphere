<?php
session_start();
$login_page = '../../../connexion/login.php';
require('../../check_session.php');
require('../../../include/database.php');
require('../../../include/check_timeout.php');
require_once('../../../path.php');
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Log des inscriptions';
require('../../head.php');
$lines = file('../../../log/log_inscription.txt');
?>

<body>
    <?php
    $page = stats_main;
    include('../../navbar.php'); ?>

    <main class="container my-5">
        <h1 class="text-center my-5">Inscriptions | Vérification | Initialisation de mot de passe</h1>
        <div class="form-group mb-2 pt-3 pb-2">
            <div class="d-flex gap-2">
                <input type="text" id="search_inscription" class="form-control searchBoxBack" placeholder="Rechercher par email ou action,">
                <div class="d-flex ms-2" style="gap: 0.5rem;">
                    <select id="statusFilter" class="form-select searchBoxBack">
                        <option value="">Statut</option>
                        <option value="Réussie">Réussi</option>
                        <option value="échouée">Echoué</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
            <table class="table table-striped table-bordered">
                <thead class="table-dark" style="position: sticky; top: 0; z-index: 1;">
                    <tr>

                        <th>Date/heure</th>
                        <th>Action</th>
                        <th>Email</th>
                        <th>Statut</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody id="log_inscription">
                    <?php foreach ($lines as $line) {
                        preg_match(
                            '/^(\d{4}\/\d{2}\/\d{2}) - (\d{2}:\d{2}:\d{2}) - (.+?) (réussie|échouée) de (.+?)(?: - (?:en raison de : )?(.+))?$/',
                            trim($line),
                            $match
                        );
                        $dateTime = $match[1] . ' - ' . $match[2];
                        $action = $match[3];
                        $status = ucfirst($match[4]);
                        $email = strtolower($match[5]);
                        if ($match[6])
                            $note = ucfirst($match[6]);
                    ?>
                        <tr>
                            <td class="align-middle"><?= $dateTime ?></td>
                            <td class="align-middle"><?= $action ?></td>
                            <td class="align-middle"><?= $email ?></td>
                            <td class="align-middle"><?= $status ?></td>
                            <td class="align-middle"><?= $note ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        function fetchLogConnexion() {
            const query = document.getElementById('search_inscription').value;
            const status = document.getElementById('statusFilter').value;

            fetch(`/back-office/stats/log_display/search_inscription.php?search=${encodeURIComponent(query)}&status=${encodeURIComponent(status)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.text())
                .then(data => {
                    document.getElementById('log_inscription').innerHTML = data;
                });
        }

        document.getElementById('search_inscription').addEventListener('input', fetchLogConnexion);
        document.getElementById('statusFilter').addEventListener('change', fetchLogConnexion);
    </script>
</body>

</html>