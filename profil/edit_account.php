<?php
session_start();
$login_page='../connexion/login.php';
require('../include/check_session.php');
require('../include/database.php');
require('../include/check_session.php');

$userId = $_SESSION['user_id'];

try {
    $stmt = $bdd->prepare("SELECT nom, prenom, email, ville, code_postal, rue FROM utilisateurs WHERE id_utilisateurs = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "Utilisateur introuvable.";
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = $_POST['nom'] ?? '';
        $prenom = $_POST['prenom'] ?? '';
        $email = $_POST['email'] ?? '';
        $ville = $_POST['ville'] ?? '';
        $code_postal = $_POST['code_postal'] ?? '';
        $rue = $_POST['rue'] ?? '';

        if (empty($nom) || empty($prenom) || empty($email) || empty($ville) || empty($code_postal) || empty($rue)) {
            $error = "Tous les champs sont obligatoires.";
        } else {
            $stmt = $bdd->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, ville = ?, code_postal = ?, rue = ? WHERE id_utilisateurs = ?");
            $stmt->execute([$nom, $prenom, $email, $ville, $code_postal, $rue, $userId]);

            header('Location: my_account.php');
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
<?php $title = "Modifier mes informations";
include('../include/head.php');
include('../include/header.php'); 
?>
<body>
<div class="container mt-4">
    <h1 class="montserrat-titre40 text-center mb-4">Modifier mes informations</h1>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    <form method="POST" class="p-4 shadow-sm rounded connexion_box">
        <div class="row g-3">
            <div class="col-md-6">
                <label for="nom" class="form-label montserrat-titre32">Nom</label>
                <input type="text" class="form-control form-control-sm" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
            </div>
            <div class="col-md-6">
                <label for="prenom" class="form-label montserrat-titre32">Prénom</label>
                <input type="text" class="form-control form-control-sm" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
            </div>
        </div>
        <div class="row g-3 mt-3">
            <div class="col-md-12">
                <label for="email" class="form-label montserrat-titre32">Email</label>
                <input type="email" class="form-control form-control-sm" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
        </div>
        <div class="row g-3 mt-3">
            <div class="col-md-6">
                <label for="ville" class="form-label montserrat-titre32">Ville</label>
                <input type="text" class="form-control form-control-sm" id="ville" name="ville" value="<?php echo htmlspecialchars($user['ville']); ?>" required>
            </div>
            <div class="col-md-3">
                <label for="code_postal" class="form-label montserrat-titre32">Code Postal</label>
                <input type="text" class="form-control form-control-sm" id="code_postal" name="code_postal" value="<?php echo htmlspecialchars($user['code_postal']); ?>" required>
            </div>
            <div class="col-md-3">
                <label for="rue" class="form-label montserrat-titre32">Rue</label>
                <input type="text" class="form-control form-control-sm" id="rue" name="rue" value="<?php echo htmlspecialchars($user['rue']); ?>" required>
            </div>
        </div>
        <div class="d-flex justify-content-between mt-4">
            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
            <a href="my_account.php" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
</body>
<?php include('../include/footer.php'); ?>
</html>
