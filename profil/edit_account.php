<?php
session_start();
$login_page = '../connexion/login.php';
require('../include/check_session.php');
require('../include/database.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';

$userId = $_SESSION['user_id'];

try {
    $stmt = $bdd->prepare("SELECT nom, prenom, email, ville, code_postal, rue, bio FROM utilisateurs WHERE id_utilisateurs = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header('location:' . index_front . '?error=' . urlencode('Utilisateur introuvable !'));
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = $_POST['nom'] ?? '';
        $prenom = $_POST['prenom'] ?? '';
        $email = $_POST['email'] ?? '';
        $ville = $_POST['ville'] ?? '';
        $code_postal = $_POST['code_postal'] ?? '';
        $rue = $_POST['rue'] ?? '';
        $bio = $_POST['bio'] ?? '';

        if (empty($nom) || empty($prenom) || empty($email) || empty($ville) || empty($code_postal) || empty($rue)) {
            $error = "Tous les champs sont obligatoires (sauf bio) !";
        } else {
            $stmt = $bdd->prepare("UPDATE utilisateurs SET bio=?, nom = ?, prenom = ?, email = ?, ville = ?, code_postal = ?, rue = ? WHERE id_utilisateurs = ?");
            $stmt->execute([$bio, $nom, $prenom, $email, $ville, $code_postal, $rue, $userId]);

            header('Location:' . my_account . '?message=' . urlencode('Modifications enregistrées !'));
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
<?php $title = "Modifier mes informations";
$pageCategory = 'profil';
echo "<script>const pageCategory = '$pageCategory';</script>";
include('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}
include('../include/header.php');
include('navbar.php');
?>

<body>
    <div class="container my-5">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">Modifier mes informations</h1>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <form method="POST">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4>Informations Personnelles</h4>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nom" class="form-label">Nom</label>
                                    <input type="text" class="form-control" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="prenom" class="form-label">Prénom</label>
                                    <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h4>Biographie</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="bio" class="form-label">Biographie</label>
                                <textarea rows="4" maxlength="200" class="form-control" id="bio" name="bio" placeholder="Parlez-nous de vous..."><?= htmlspecialchars($user['bio']); ?></textarea>
                                <div class="form-text">Maximum 200 caractères</div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h4>Adresse</h4>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label for="ville" class="form-label">Ville</label>
                                    <input type="text" class="form-control" id="ville" name="ville" value="<?php echo htmlspecialchars($user['ville']); ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="code_postal" class="form-label">Code Postal</label>
                                    <input type="text" class="form-control" id="code_postal" name="code_postal" value="<?php echo htmlspecialchars($user['code_postal']); ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="rue" class="form-label">Rue</label>
                                <input type="text" class="form-control" id="rue" name="rue" value="<?php echo htmlspecialchars($user['rue']); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                                <a href="<?= my_account ?>" class="btn btn-danger">Annuler</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include('../include/footer.php'); ?>
</body>

</html>