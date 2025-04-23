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
$title = 'Gestions des utilisateurs';
require('../head.php');
?>

<body class="pb-4">
    <?php
    $page = 'index.php';
    include('../navbar.php');
    ?>
    <main class="container my-5">
        <?php
        if (isset($bdd)) {
            try {
                $stmt = $bdd->query("SELECT id_utilisateurs, pseudo, nom, prenom, email FROM utilisateurs");
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
                <input type="text" id="search" class="form-control" placeholder="Rechercher par pseudo ou email">
                </div>';

                if (count($users) > 0) {
                    echo '<div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">';
                    echo "<table class='table table-striped'>";
                    echo "<thead class='thead-dark'><tr><th>ID</th><th>Nom</th><th>Email</th><th>Pseudo</th><th>Prénom</th><th>Actions</th></tr></thead>";
                    echo "<tbody id='user_results'>";

                    foreach ($users as $user) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($user['id_utilisateurs']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['nom']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['pseudo']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['prenom']) . "</td>";
                        echo "<td>";
                        echo "<a href='edit_user.php?id=" . htmlspecialchars($user['id_utilisateurs']) . "' class='btn btn-primary btn-sm'>Modifier</a> ";
                        echo "<a href='export_pdf.php?id=" . htmlspecialchars($user['id_utilisateurs']) . "' class='btn btn-primary btn-sm'>Exporter PDF</a> ";
                        echo "<a href='delete_user.php?id=" . htmlspecialchars($user['id_utilisateurs']) . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer cet utilisateur?\");'>Supprimer</a>";
                        echo "</td>";
                        echo "</tr>";
                    }

                    echo "</tbody>";
                    echo "</table>";
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
        document.getElementById('search').addEventListener('input', function() {
            const query = this.value;

            fetch('search_profils.php?search=' + encodeURIComponent(query), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(data => {
                    document.getElementById('user_results').innerHTML = data;
                });
        });
    </script>
</body>

</html>