<?php
include('../../include/database.php');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = $bdd->prepare("SELECT image FROM jeu WHERE id_jeu = :id");
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($result && !empty($result['image'])) {
        $imagePath = __DIR__ . "/../uploads/" . $result['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
}
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = $bdd->prepare("DELETE FROM jeu WHERE id_jeu = :id");
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    if ($query->execute()) {
        header("Location: jeux.php?message=delete");
    } else {
        echo "Une erreur s'est produite lors de la suppression du jeu.";
    }
} else {
    echo "Aucun identifiant de jeu fourni.";
}
