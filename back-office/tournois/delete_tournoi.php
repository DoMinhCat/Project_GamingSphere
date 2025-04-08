<?php
session_start();
include('../../include/database.php');

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id_tournoi']) || empty($_GET['id_tournoi'])) {
    header("Location: tournois_main.php?message=missing_id");
    exit();
}

$id_tournoi = intval($_GET['id_tournoi']);

try {
    $stmt = $bdd->prepare("DELETE FROM tournoi WHERE id_tournoi = ?");
    $stmt->execute([$id_tournoi]);

    header("Location: tournois_main.php?message=tournoi_deleted");
    exit();
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Erreur lors de la suppression du tournoi : " . htmlspecialchars($e->getMessage()) . "</div>";
}
