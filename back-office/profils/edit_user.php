<?php
require('../../include/database.php');
require('../../include/check_timeout.php');

$user_id = $_GET['id'];

$stmt = $bdd->prepare("SELECT id_utilisateurs, pseudo, nom, prenom, email, photo_profil, ville, rue, code_postal FROM utilisateurs WHERE id_utilisateurs = ?");
$stmt->bindValue(1, $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pseudo = $_POST['pseudo'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $ville = $_POST['ville'];
    $rue = $_POST['rue'];
    $code_postal = $_POST['code_postal'];
    $photo = "profil/uploads/profiles_pictures/" . basename($_FILES["photo"]["name"]);
    if (!empty($_FILES['photo']['tmp_name'])) {
        $target_dir = "../profil/uploads/profiles_pictures";
        $target_file = $target_dir . basename($_FILES["photo"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                $photo = $target_file;
            }
        }
    }

    $update_stmt = $bdd->prepare("UPDATE utilisateurs SET pseudo = ?, nom = ?, prenom = ?, email = ?, photo_profil = ?, ville = ?, rue = ?, code_postal = ? WHERE id_utilisateurs = ?");
    $update_stmt->bindValue(1, $pseudo, PDO::PARAM_STR);
    $update_stmt->bindValue(2, $nom, PDO::PARAM_STR);
    $update_stmt->bindValue(3, $prenom, PDO::PARAM_STR);
    $update_stmt->bindValue(4, $email, PDO::PARAM_STR);
    $update_stmt->bindValue(5, $photo, PDO::PARAM_STR);
    $update_stmt->bindValue(6, $ville, PDO::PARAM_STR);
    $update_stmt->bindValue(7, $rue, PDO::PARAM_STR);
    $update_stmt->bindValue(8, $code_postal, PDO::PARAM_STR);
    $update_stmt->bindValue(9, $user_id, PDO::PARAM_INT);
    $update_stmt->execute();

    header("Location: profils.php?message=success");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Modifier Utilisateur</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <?php
    if (isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
        echo '<script src="../../include/check_timeout.js"></script>';
    }
    ?>
</head>

<body>
    <?php
    include('../navbar.php');
    ?>
    <div class="container">
        <h2 class="mt-5">Modifier Utilisateur</h2>
        <form method="post" enctype="multipart/form-data" class="mt-3">
            <div class="form-group">
                <label for="pseudo">Pseudo:</label>
                <input type="text" class="form-control" id="pseudo" name="pseudo" value="<?php echo htmlspecialchars($user['pseudo']); ?>" required>
            </div>
            <div class="form-group">
                <label for="nom">Nom:</label>
                <input type="text" class="form-control" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
            </div>
            <div class="form-group">
                <label for="prenom">Prénom:</label>
                <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="photo">Photo de profil:</label>
                <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                <?php if (!empty($user['photo_profil'])): ?>
                    <img src="/PA/<?php echo htmlspecialchars($user['photo_profil']); ?>" class="mt-2" width="100">
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="ville">Ville:</label>
                <input type="text" class="form-control" id="ville" name="ville" value="<?php echo htmlspecialchars($user['ville']); ?>" required>
            </div>
            <div class="form-group">
                <label for="rue">Rue:</label>
                <input type="text" class="form-control" id="rue" name="rue" value="<?php echo htmlspecialchars($user['rue']); ?>" required>
            </div>
            <div class="form-group">
                <label for="code_postal">Code Postal:</label>
                <input type="text" class="form-control" id="code_postal" name="code_postal" value="<?php echo htmlspecialchars($user['code_postal']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>