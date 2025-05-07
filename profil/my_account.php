<?php
session_start();
$login_page = '../connexion/login.php';
require('../include/check_session.php');
require('../include/database.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';

$userId = $_SESSION['user_id'];

try {

    $stmt = $bdd->prepare("SELECT pseudo, email, date_inscription, nom, prenom, ville, rue, code_postal, photo_profil FROM utilisateurs WHERE id_utilisateurs = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "Utilisateur introuvable.";
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
        $stmt = $bdd->prepare("DELETE FROM utilisateurs WHERE id_utilisateurs = ?");
        $stmt->execute([$userId]);
        session_destroy();
        header('Location: ../index.php');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
        if ($_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/profiles_pictures/';
            $filename = str_replace(' ', '_', $_FILES['profile_picture']['name']);
            $uploadFile = $uploadDir . basename($filename);
            $relativePath = '/uploads/profiles_pictures/' . basename($filename);
            $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES['profile_picture']['tmp_name']);
            if ($check === false) {
                echo "Le fichier n'est pas une image.";
                exit;
            }

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($imageFileType, $allowedExtensions)) {
                echo "Seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.";
                exit;
            }

            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
                $stmt = $bdd->prepare("UPDATE utilisateurs SET photo_profil = ? WHERE id_utilisateurs = ?");
                $stmt->execute([$relativePath, $userId]);
                $user['photo_profil'] = $relativePath;
            } else {
                error_log("Erreur lors du déplacement du fichier : " . $_FILES['profile_picture']['tmp_name'] . " vers " . $uploadFile);
                echo "Erreur lors du téléchargement de l'image.";
                exit;
            }
        } else {
            echo "Aucun fichier téléchargé ou erreur lors du téléchargement.";
            exit;
        }
    }
} catch (PDOException $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<?php $title = "Mon compte";
require('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}
?>

<body>
    <?php
    include('../include/header.php');
    include('navbar.php');
    ?>

    <div class="container mt-4">
        <div class="card shadow-sm p-4 connexion_box mt-4">
            <h3 class="card-title montserrat-titre40 text-center">Mon compte</h3>
            <hr>
            <div class="text-center mb-4">
                <h4>Photo de profil</h4>
                <?php if (!empty($user['photo_profil'])): ?>
                    <img src="<?= htmlspecialchars($user['photo_profil']) ?>" alt="Photo de profil" style="width: 150px; height: 150px; border-radius: 50%;">                    <form method="POST" enctype="multipart/form-data" class="mt-3">
                        <button type="button" class="btn btn-dark" onclick="document.getElementById('profile_picture_form').style.display = 'block'; this.style.display = 'none';">
                            Modifier la photo de profil
                        </button>
                    </form>
                    <form id="profile_picture_form" method="POST" enctype="multipart/form-data" style="display: none;" class="mt-3">
                        <div class="mb-3">
                            <label for="profile_picture" class="form-label">Changer votre photo de profil :</label>
                            <input type="file" class="form-control" id="profile_picture" name="profile_picture" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Télécharger</button>
                    </form>
                <?php else: ?>
                    <p>Aucune photo de profil.</p>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="profile_picture" class="form-label">Ajouter une photo de profil :</label>
                            <input type="file" class="form-control" id="profile_picture" name="profile_picture" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Télécharger</button>
                    </form>
                <?php endif; ?>
            </div>

            <div class="card shadow-sm p-3 mb-4 mon_compte_card">
                <h4 class="card-title montserrat-titre40">Informations personnelles</h4>
                <hr>
                <p class="montserrat-titre32"><strong>Pseudo :</strong> <?= htmlspecialchars($user['pseudo']); ?></p>
                <p class="montserrat-titre32"><strong>Email :</strong> <?= htmlspecialchars($user['email']); ?></p>
                <p class="montserrat-titre32"><strong>Nom :</strong> <?= htmlspecialchars($user['nom']); ?></p>
                <p class="montserrat-titre32"><strong>Prénom :</strong> <?= htmlspecialchars($user['prenom']); ?></p>
                <p class="montserrat-titre32"><strong>Date d'inscription :</strong> <?= htmlspecialchars($user['date_inscription']); ?></p>
            </div>

            <div class="card shadow-sm p-3 mon_compte_card">
                <h4 class="card-title montserrat-titre40">Adresse</h4>
                <hr>
                <p class="montserrat-titre32"><strong>Ville :</strong> <?= htmlspecialchars($user['ville']); ?></p>
                <p class="montserrat-titre32"><strong>Code Postal :</strong> <?= htmlspecialchars($user['code_postal']); ?></p>
                <p class="montserrat-titre32"><strong>Rue :</strong> <?= htmlspecialchars($user['rue']); ?></p>
            </div>

            <hr>
            <div class="d-flex justify-content-between mt-4">
                <a href="<?= edit_account ?>" class="btn btn-primary">Modifier mes informations</a>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                    Supprimer mon compte
                </button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAccountModalLabel">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form method="POST" class="d-inline">
                        <button type="submit" name="delete_account" class="btn btn-danger">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include('../include/footer.php'); ?>
</body>
</html>
