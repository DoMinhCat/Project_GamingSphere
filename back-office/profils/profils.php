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
    <main class="container mb-5">
        <?php
        $noti = '';
        $noti_Err = '';
        if (isset($_GET['message']) && $_GET['message'] === 'deleted')
            $noti = 'L\'utilisateur a été supprimé avec succès !';
        elseif (isset($_GET['message']) && $_GET['message'] === 'success')
            $noti = 'Les informations de l\'utilisateur ont été modifiées';
        elseif (isset($_GET['error']) && $_GET['error'] === 'user_non_exist')
            $noti_Err = 'Utilisateur non trouvé';
        elseif (isset($_GET['error']) && $_GET['error'] === 'id_invalid')
            $noti_Err = 'ID utilisateur invalid';
        elseif (isset($_GET['error']) && $_GET['error'] === 'bdd') {
            $noti_Err = 'Erreur lors de la connection à la base de données : ' . $_SESSION['error'];
            unset($_SESSION['error']);
        }
        ?>
        <?php if (!empty($noti_Err)) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $noti_Err ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif ?>

        <?php if (!empty($noti)) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $noti ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif ?>

        <h1 class="text-center my-5">Liste des utilisateurs</h1>
        <?php
        if (isset($bdd)) {
            try {
                $stmt = $bdd->query("SELECT id_utilisateurs, pseudo, nom, prenom, email, last_active FROM utilisateurs ORDER BY nom ASC");
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo '<div class="form-group my-2 sticky-top pt-3 pb-2">
                <div class="d-flex gap-2">
                    <input type="text" id="search" class="form-control searchBoxBack" placeholder="Rechercher par pseudo, email, nom ou prénom">
                    <div class="d-flex ms-2" style="gap: 0.5rem;">
                    <select id="statusFilter" class="form-select searchBoxBack">
                        <option value="">Tous</option>
                        <option value="online">Online</option>
                        <option value="offline">Offline</option>
                    </select>
                    </div>
                </div>
                </div>';

                if (count($users) > 0) {
                    echo '<div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">';
                    echo "<table class='table table-striped table-bordered'>";
                    echo "<thead class='table-dark' style=\"position: sticky; top: 0; z-index: 1;\"><tr><th>ID</th><th>Nom</th><th>Email</th><th>Pseudo</th><th>Prénom</th><th>Statut</th><th>Actions</th></tr></thead>";
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
                        echo "<div class='d-flex flex-wrap align-items-start flex-xl-row align-items-start'>";
                        echo "<a href='" . profils_edit_back . "?id=" . htmlspecialchars($user['id_utilisateurs']) . "' class='btn btn-primary btn-sm mb-1 mb-xl-0 me-sm-1'>Modifier</a> ";
                        echo "<a href='export_pdf.php?id=" . htmlspecialchars($user['id_utilisateurs']) . "' class='btn btn-primary btn-sm mb-1 mb-xl-0 me-sm-1'>Exporter PDF</a> ";
                        echo '<button type="button" class="btn btn-sm btn-danger mb-1 mb-xl-0 me-sm-1" data-bs-toggle="modal" data-bs-target="#deleteModal' . $user['id_utilisateurs'] . '">Supprimer</button>';
                        echo '</div>';
                        echo '<div class="modal fade" id="deleteModal' . $user['id_utilisateurs'] . '" tabindex="-1" aria-labelledby="deleteModalLabel' . $user['id_utilisateurs'] . '" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="deleteModalLabel' . $user['id_utilisateurs'] . '">Confirmation</h1>
                                                </div>
                                                <div class="modal-body">
                                                    Êtes-vous sûr de vouloir supprimer cet utilisateur ?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <a type="button" class="btn btn-danger" href="delete_user.php?id=' . $user['id_utilisateurs'] . '">Supprimer</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';
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
        document.addEventListener('DOMContentLoaded', fetchFilteredUsers);

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
                console.error('Echec de récuperation du statut des utilisateurs :', err);
            }
        }

        fetchOnlineUsers();
        setInterval(fetchOnlineUsers, 30000); 
    </script>





</body>


</html>