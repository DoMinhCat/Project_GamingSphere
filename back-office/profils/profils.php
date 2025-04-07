<?php
session_start();
include('../../include/database.php');
require('../../include/check_timeout.php');
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gestions des utilisateurs</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <?php
    if (isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
        echo '<script src="../../includes/check_timeout.js"></script>';
    }
    ?>
</head>

<body>
    <?php
    include('../navbar.php');
    ?>
    <main class="container mt-5">
        <?php
        if (isset($_SESSION['admin']) && $_SESSION['admin'] == true && isset($bdd)) {
            try {
                $stmt = $bdd->query("SELECT id_utilisateurs, pseudo, nom, prenom, email FROM utilisateurs");
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (isset($_GET['message'])) {
                    if ($_GET['message'] == 'success') {
                        echo "<div class='alert alert-success'>Modifications effectuées avec succès.</div>";
                    } elseif ($_GET['message'] == 'delete') {
                        echo "<div class='alert alert-success'>Utilisateur supprimé avec succès.</div>";
                    }
                }

                if (count($users) > 0) {
                    echo "<table class='table table-striped'>";
                    echo "<thead class='thead-dark'><tr><th>ID</th><th>Nom</th><th>Email</th><th>Pseudo</th><th>Prénom</th><th>Actions</th></tr></thead>";
                    echo "<tbody>";

                    foreach ($users as $user) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($user['id_utilisateurs']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['nom']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['pseudo']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['prenom']) . "</td>";
                        echo "<td>";
                        echo "<a href='edit_user.php?id=" . htmlspecialchars($user['id_utilisateurs']) . "' class='btn btn-primary btn-sm'>Modifier</a> ";
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
            echo "<div class='alert alert-danger'>Accès refusé. Vous n'êtes pas administrateur.</div>";
        }
        ?>

    </main>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>