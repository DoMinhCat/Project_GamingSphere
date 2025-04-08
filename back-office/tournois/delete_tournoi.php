<?php
session_start();
include('../../include/database.php');

// Vérifiez si l'utilisateur est un administrateur
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: ../index.php");
    exit();
}

// Vérifiez si un ID de tournoi est passé dans l'URL
if (!isset($_GET['id_tournoi']) || empty($_GET['id_tournoi'])) {
    header("Location: tournois_main.php?message=missing_id");
    exit();
}

$id_tournoi = intval($_GET['id_tournoi']); // Sécurisez l'ID

try {
    // Supprimez le tournoi de la base de données
    $stmt = $bdd->prepare("DELETE FROM tournoi WHERE id_tournoi = ?");
    $stmt->execute([$id_tournoi]);

    // Redirigez avec un message de succès
    header("Location: tournois_main.php?message=tournoi_deleted");
    exit();
} catch (PDOException $e) {
    // En cas d'erreur, affichez un message
    echo "<div class='alert alert-danger'>Erreur lors de la suppression du tournoi : " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>