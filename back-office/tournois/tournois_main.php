<?php


session_start();
include('../../include/database.php'); 
$title = 'Liste des Tournois';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('../navbar.php'); ?>

    <div class="container my-5">
    <?php if (isset($_GET['message']) && $_GET['message'] === 'tournoi_deleted'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        Le tournoi a été supprimé avec succès.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
        <h1 class="mb-4 text-center">Liste des Tournois Disponibles</h1>
        <div class="mb-4 text-end">
        <a href="add_tournoi.php" class="btn btn-primary">Ajouter un tournoi</a>
    </div>
        <?php
        try {
            $stmt = $bdd->query("SELECT id_tournoi, nom_tournoi, date_debut, date_fin, jeu, status_ENUM FROM tournoi ORDER BY date_debut DESC");
            $tournois = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($tournois) > 0): ?>
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Nom</th>
                            <th>Date de Début</th>
                            <th>Date de Fin</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tournois as $tournoi): ?>
                            <tr>
                                <td><?= htmlspecialchars($tournoi['id_tournoi']) ?></td>
                                <td><?= htmlspecialchars($tournoi['nom_tournoi']) ?></td>
                                <td><?= htmlspecialchars($tournoi['date_debut']) ?></td>
                                <td><?= htmlspecialchars($tournoi['date_fin']) ?></td>
                                <td><?= htmlspecialchars($tournoi['status_ENUM']) ?></td>
                                <td>
                                    <a href="modify_tournoi.php?id_tournoi=<?= $tournoi['id_tournoi'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                                    <a href="delete_tournoi.php?id_tournoi=<?= $tournoi['id_tournoi'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce tournoi ?');">Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info text-center">Aucun tournoi disponible pour le moment.</div>
            <?php endif;
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>Erreur lors de la récupération des tournois : " . htmlspecialchars($e->getMessage()) . "</div>";
        }
        ?>
    </div>  
</body>
</html>