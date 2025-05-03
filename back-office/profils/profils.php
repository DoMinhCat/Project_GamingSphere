<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/database.php');
require('../../include/check_timeout.php');
require_once __DIR__ . '/../../path.php';
function isOnline($lastActive)
{
    $timeout = 60;
    $lastActiveTime = strtotime($lastActive);
    return (time() - $lastActiveTime) <= $timeout;
}
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Gestions des utilisateurs';
require('../head.php');
?>

<body class="pb-4">
    <?php
    $page = index_back;
    include('../navbar.php');
    ?>
    <main class="container my-5">
        <?php
        if (isset($bdd)) {
            try {
                $stmt = $bdd->query("SELECT id_utilisateurs, pseudo, nom, prenom, email, last_active FROM utilisateurs ORDER BY nom ASC");
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (isset($_GET['message'])) {
                    if ($_GET['message'] == 'success') {
                        echo "<div class='alert alert-success'>Modifications effectuées avec succès.</div>";
                    } elseif ($_GET['message'] == 'delete') {
                        echo "<div class='alert alert-success'>Utilisateur supprimé avec succès.</div>";
                    } elseif ($_GET['message'] == 'user_non_exist') {
                        echo "<div class='alert alert-danger'>Utilisateur non trouvé.</div>";
                    } elseif ($_GET['message'] == 'id_invalid') {
                        echo "<div class='alert alert-danger'>ID utilisateur invalide</div>";
                    }
                }


                echo '<div class="form-group my-2 sticky-top pt-3 pb-2">
                <div class="input-group">
                    <input type="text" id="search" class="form-control" placeholder="Rechercher par pseudo ou email">
                    <div class="input-group-append">
                    <select id="statusFilter" class="form-select">
                        <option value="">Tous</option>
                        <option value="online">Online</option>
                        <option value="offline">Offline</option>
                    </select>
                    </div>
                </div>
                </div>';

                if (count($users) > 0) {
                    echo '<div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">';
                    echo "<table class='table table-striped'>";
                    echo "<thead class='thead-dark'><tr><th>ID</th><th>Nom</th><th>Email</th><th>Pseudo</th><th>Prénom</th><th>Statut</th><th>Actions</th></tr></thead>";
                    echo "<tbody id='user_results'>";

                    foreach ($users as $user) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($user['id_utilisateurs']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['nom']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['pseudo']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['prenom']) . "</td>";
                        echo "<td>
                        <span class=\"user-status\" data-user=\"" . htmlspecialchars($user['pseudo']) . "\">" . (isOnline($user['last_active']) ? '<span style="color: green;">Online</span>' : '<span style="color: gray;">Offline</span>') . "
                        </span>
                        </td>";

                        echo "<td>";
                        echo "<div class='d-flex flex-wrap align-items-start flex-md-row align-items-start'>";
                        echo "<a href='" . profils_edit_back . "?id=" . htmlspecialchars($user['id_utilisateurs']) . "' class='btn btn-primary btn-sm mb-1 mb-md-0 me-sm-1'>Modifier</a> ";
                        echo "<a href='export_pdf.php?id=" . htmlspecialchars($user['id_utilisateurs']) . "' class='btn btn-primary btn-sm mb-1 mb-md-0 me-sm-1'>Exporter PDF</a> ";
                        echo "<a href='delete_user.php?id=" . htmlspecialchars($user['id_utilisateurs']) . "' class='btn btn-danger btn-sm mb-1 mb-md-0 me-sm-1' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer cet utilisateur?\");'>Supprimer</a>";
                        echo '</div>';
                        echo "</td>";
                        echo "</tr>";
                    }

                    echo "</tbody>";
                    echo "</table>";
                    echo '</div>';
                } else {
                    echo "<div class='alert alert-warning'>Aucun utilisateur trouvé.</div>";
                }
            } catch (PDOException $e) {
                echo "<div class='alert alert-danger'>Erreur lors de la récupération des utilisateurs : " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Erreur lors de la connexion à la base de donnée.</div>";
        }
        ?>

    </main>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function fetchFilteredUsers() {
            const query = document.getElementById('search').value;
            const status = document.getElementById('statusFilter').value;

            fetch(`search_profils.php?search=${encodeURIComponent(query)}&status=${encodeURIComponent(status)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.text())
                .then(data => {
                    document.getElementById('user_results').innerHTML = data;
                    fetchOnlineUsers();
                });
        }

        document.getElementById('search').addEventListener('input', fetchFilteredUsers);
        document.getElementById('statusFilter').addEventListener('change', fetchFilteredUsers);
    </script>
    <script>
        async function fetchOnlineUsers() {
            try {
                const res = await fetch('../../status_user/reload_back-office.php');
                const onlineUsers = await res.json();

                document.querySelectorAll('.user-status').forEach(span => {
                    const username = span.dataset.user;
                    const isOnline = onlineUsers.includes(username);

                    span.innerHTML = isOnline ?
                        '<span style="color: green;">Online</span>' :
                        '<span style="color: gray;">Offline</span>';
                });
            } catch (err) {
                console.error('Failed to fetch user statuses:', err);
            }
        }

        fetchOnlineUsers();
        setInterval(fetchOnlineUsers, 30000); //30s
    </script>

</body>

</html>