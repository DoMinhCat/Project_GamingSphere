<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/database.php');
require_once __DIR__ . '/../../path.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gameId'])) {
    $gameId = $_POST['gameId'];
    $category = $_POST['category'];
    $releaseDate = $_POST['releaseDate'];
    $gameName = $_POST['gameName'];
    $gameRating = $_POST['gameRating'];
    $platform = $_POST['platform'];
    $gamePrice = $_POST['gamePrice'];
    $gameType = $_POST['gameType'];
    $gamePublisher = $_POST['gamePublisher'];
    $gameDescription = $_POST['gameDescription'];

    $imagePath = null;

    if (isset($_FILES['gameImage']) && $_FILES['gameImage']['error'] === UPLOAD_ERR_OK) {
        $type_accept = ['image/png', 'image/gif', 'image/jpeg'];
        if (!in_array($_FILES['gameImage']['type'], $type_accept)) {
            $_SESSION['message'] = "Le fichier doit être du type jpeg , png, ou gif !";
            header('location:' . jeux_edit_back);
            exit();
        }
        $size_accept = 4 * 1024 * 1024; //4MB
        if ($_FILES['gameImage']['size'] > $size_accept) {
            $_SESSION['message'] = "La taille de l'image ne doit pas dépasser 4Mo !";
            header('location:' . jeux_edit_back);
            exit();
        }
        $uploadDir = '../uploads/';
        $filename = basename($_FILES['gameImage']['name']);
        $imagePath = $uploadDir . $filename;

        if (file_exists($uploadDir . $filename)) {
            $filename = uniqid() . "_" . $filename;
            $imagePath = $uploadDir . $filename;
        }

        if (move_uploaded_file($_FILES['gameImage']['tmp_name'], $imagePath)) {
            try {
                $stmt = $bdd->prepare("SELECT image FROM jeu WHERE id_jeu = ?");
                $stmt->execute([$gameId]);
                $oldImage = $stmt->fetchColumn();

                if (!empty($oldImage) && file_exists($uploadDir . $oldImage)) {
                    unlink($uploadDir . $oldImage);
                }

                $imagePath = $filename;
            } catch (PDOException $e) {
                $_SESSION['error'] = htmlspecialchars($e->getMessage());
                header('Location:' . jeux_back . '?error=bdd');
                exit();
            }
        } else {
            header('location:' . jeux_edit_back . '?message=' . urlencode('Erreur lors de l\'upload de l\'image !'));
            exit();
        }
    } else {
        $stmt = $bdd->prepare("SELECT image FROM jeu WHERE id_jeu = ?");
        $stmt->execute([$gameId]);
        $imagePath = $stmt->fetchColumn();
    }

    $query = "UPDATE jeu SET 
    nom = ?, catégorie = ?, date_sortie = ?, note_jeu = ?, plateforme = ?, prix = ?, type = ?, éditeur = ?, description = ?";

    if ($imagePath) {
        $query .= ", image = ?";
    }

    $query .= " WHERE id_jeu = ?";

    $params = [
        $gameName,
        $category,
        $releaseDate,
        $gameRating,
        $platform,
        $gamePrice,
        $gameType,
        $gamePublisher,
        $gameDescription,
    ];

    if ($imagePath) {
        $params[] = $imagePath;
    }

    $params[] = $gameId;

    try {
        $stmt = $bdd->prepare($query);
        $stmt->execute($params);
        header('Location:' . jeux_back . '?message=updated');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = htmlspecialchars($e->getMessage());
        header('Location:' . jeux_back . '?error=bdd');
        exit();
    }
} else {
    header('Location: ' . jeux_back . '?error=id_invalid');
    exit();
}
