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
$title = 'Log des connexions';
require('../../head.php');
$lines = file('../../../log/log_login.txt');
?>

<body>
    <?php
    $page = stats_main;
    include('../../navbar.php'); ?>
    <main class="container my-5">
        <h1 class="text-center my-5">Connexions</h1>
        <div class="form-group mb-2 pt-3 pb-2">
            <div class="d-flex gap-2">
                <input type="text" id="search_login" class="form-control searchBoxBack" placeholder="Rechercher par email">
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
                <thead class="table-dark">
                    <tr>

                        <th>Date/heure</th>
                        <th>Action</th>
                        <th>Email</th>
                        <th>Statut</th>

                    </tr>
                </thead>
                <tbody id="log_login">
                    <?php foreach ($lines as $line) {
                        preg_match(
                            '/^(\d{4}\/\d{2}\/\d{2}) - (\d{2}:\d{2}:\d{2}) - (.+?) (réussie|échouée) de (.+?)$/',
                            trim($line),
                            $match
                        );
                        $dateTime = $match[1] . ' - ' . $match[2];
                        $action = $match[3];
                        $status = ucfirst($match[4]);
                        $email = strtolower($match[5]);
                    ?>
                        <tr>
                            <td class="align-middle"><?= $dateTime ?></td>
                            <td class="align-middle"><?= $action ?></td>
                            <td class="align-middle"><?= $email ?></td>
                            <td class="align-middle"><?= $status ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <h1 class="text-center my-5">Statistiques</h1>

    </main>

    <script>
        function fetchLogConnexion() {
            const query = document.getElementById('search_login').value;
            const status = document.getElementById('statusFilter').value;

            fetch(`/back-office/stats/log_display/search_login.php?search=${encodeURIComponent(query)}&status=${encodeURIComponent(status)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.text())
                .then(data => {
                    document.getElementById('log_login').innerHTML = data;
                });
        }

        document.getElementById('search_login').addEventListener('input', fetchLogConnexion);
        document.getElementById('statusFilter').addEventListener('change', fetchLogConnexion);
    </script>
</body>

</html>