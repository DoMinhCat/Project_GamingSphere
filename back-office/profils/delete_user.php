<?php
include('../../include/database.php');

try {
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['id'])) {
        $userId = $_GET['id'];

        $stmt = $bdd->prepare("DELETE FROM utilisateurs WHERE id_utilisateurs = :id");
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: profils.php?message=delete");
            exit();
        } else {
            echo "Error deleting user.";
        }
    } else {
        echo "User ID not provided.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$bdd = null;
?>
