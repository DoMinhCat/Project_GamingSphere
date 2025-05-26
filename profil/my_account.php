<?php
session_start();
$login_page = '../connexion/login.php';
require('../include/check_session.php');
require('../include/database.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';

$userId = $_SESSION['user_id'];

try {
    $stmt = $bdd->prepare("SELECT pseudo, email, date_inscription, nom, prenom, ville, rue, code_postal, photo_profil, bio FROM utilisateurs WHERE id_utilisateurs = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header('location:' . index_front . '?error=' . urlencode('Utilisateur introuvable !'));
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
        $stmt = $bdd->prepare("DELETE FROM utilisateurs WHERE id_utilisateurs = ?");
        $stmt->execute([$userId]);
        $stream = fopen('../log/log_inscription.txt', 'a+');

        $line = date('Y/m/d - H:i:s') . ' - Suppression du compte réussie de ' . $email . "\n";
        fputs($stream, $line);
        fclose($stream);
        session_destroy();
        header('Location:' . index_front . '?acc_del=' . urlencode('Votre compte a été supprimé. Nous espérons vous revoir bientôt !'));
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
        if ($_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
            error_log("Erreur d'upload : " . $_FILES['profile_picture']['error']);
            header('location:' . my_account . '?error=' . urlencode('Erreur lors du téléchargement de l\'image !'));
            exit;
        }

        $uploadDir = __DIR__ . '/uploads/profiles_pictures/';
        $filename = uniqid() . '_' . str_replace(' ', '_', $_FILES['profile_picture']['name']);
        $uploadFile = $uploadDir . basename($filename);
        $relativePath = 'uploads/profiles_pictures/' . basename($filename);
        $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (!file_exists($_FILES['profile_picture']['tmp_name'])) {
            header('location:' . my_account . '?error=' . urlencode('Erreur lors du téléchargement de l\'image !'));
            exit;
        }

        $check = getimagesize($_FILES['profile_picture']['tmp_name']);
        if ($check === false) {
            header('location:' . my_account . '?error=' . urlencode('Le fichier téléchargé doit être une image !'));
            exit;
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowedExtensions)) {
            header('location:' . my_account . '?error=' . urlencode('Le fichier téléchargé doit être une image !'));
            exit;
        }

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
            $stmt = $bdd->prepare("UPDATE utilisateurs SET photo_profil = ? WHERE id_utilisateurs = ?");
            $stmt->execute([$relativePath, $userId]);
            $user['photo_profil'] = $relativePath;
        } else {
            error_log("Erreur lors du déplacement du fichier : " . $_FILES['profile_picture']['tmp_name'] . " vers " . $uploadFile);
            header('location:' . my_account . '?error=' . urlencode('Erreur lors du téléchargement de l\'image !'));
            exit;
        }
    }
} catch (PDOException $e) {
    header('location:' . index_front . '?message=bdd');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<?php $title = "Mon compte";
$pageCategory = 'profil';
echo "<script>const pageCategory = '$pageCategory';</script>";
require('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}
?>

<body>
    <?php
    include('../include/header.php');
    include('navbar.php');
    if (!empty($_GET['message'])) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($_GET['message']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }
    if (!empty($_GET['error'])) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($_GET['error']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }
    ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">Mon Compte</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Photo de Profil</h4>
                    </div>
                    <div class="card-body text-center">
                        <?php if (!empty($user['photo_profil'])): ?>
                            <img src="/profil/<?= htmlspecialchars($user['photo_profil']) ?>"
                                alt="Photo de profil"
                                class="rounded-circle mb-3"
                                width="150"
                                height="150">
                        <?php else: ?>
                            <div class="bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                style="width: 150px; height: 150px;">
                                <span class="text-muted">Aucune photo</span>
                            </div>
                        <?php endif; ?>

                        <button type="button"
                            class="btn btn-primary btn-sm"
                            onclick="document.getElementById('profile_picture_form').style.display = 'block'; this.style.display = 'none';">
                            Modifier la photo
                        </button>

                        <form id="profile_picture_form" method="POST" enctype="multipart/form-data" style="display: none;" class="mt-3">
                            <div class="mb-3">
                                <input type="file" class="form-control form-control-sm" name="profile_picture" required>
                            </div>
                            <button type="submit" class="btn btn-success btn-sm">Télécharger</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Informations Personnelles</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-sm-4"><strong>Pseudo:</strong></div>
                            <div class="col-sm-8"><?= htmlspecialchars($user['pseudo']); ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4"><strong>Email:</strong></div>
                            <div class="col-sm-8"><?= htmlspecialchars($user['email']); ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4"><strong>Nom:</strong></div>
                            <div class="col-sm-8"><?= htmlspecialchars($user['nom']); ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4"><strong>Prénom:</strong></div>
                            <div class="col-sm-8"><?= htmlspecialchars($user['prenom']); ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4"><strong>Date d'inscription:</strong></div>
                            <div class="col-sm-8"><?= htmlspecialchars($user['date_inscription']); ?></div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Adresse</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-sm-4"><strong>Ville:</strong></div>
                            <div class="col-sm-8"><?= htmlspecialchars($user['ville']); ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4"><strong>Code Postal:</strong></div>
                            <div class="col-sm-8"><?= htmlspecialchars($user['code_postal']); ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4"><strong>Rue:</strong></div>
                            <div class="col-sm-8"><?= htmlspecialchars($user['rue']); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Biographie</h4>
                    </div>
                    <div class="card-body">
                        <p><?= nl2br(htmlspecialchars($user['bio'])); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-danger">
                        <h4>Actions</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="<?= edit_account ?>" class="btn btn-primary">Modifier mes informations</a>
                            <a href="export.php" class="btn btn-primary">Exporter mes informations</a>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                                Supprimer mon compte
                            </button>
                        </div>
                    </div>
                </div>
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