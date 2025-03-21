<<<<<<< HEAD
<?php
include('../../include/database.php');
include ('../navbar.php'); 
$user_id = $_GET['id'];

$stmt = $bdd->prepare("SELECT id_utilisateurs, pseudo, nom, prenom, email FROM utilisateurs WHERE id_utilisateurs = ?");
$stmt->bindValue(1, $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pseudo = $_POST['pseudo'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];

    $update_stmt = $bdd->prepare("UPDATE utilisateurs SET pseudo = ?, nom = ?, prenom = ?, email = ? WHERE id_utilisateurs = ?");
    $update_stmt->bindValue(1, $pseudo, PDO::PARAM_STR);
    $update_stmt->bindValue(2, $nom, PDO::PARAM_STR);
    $update_stmt->bindValue(3, $prenom, PDO::PARAM_STR);
    $update_stmt->bindValue(4, $email, PDO::PARAM_STR);
    $update_stmt->bindValue(5, $user_id, PDO::PARAM_INT);
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
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Modifier Utilisateur</h2>
        <form method="post" class="mt-3">
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
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
=======
<?php
include('../../include/database.php');
$user_id = $_GET['id'];

$stmt = $bdd->prepare("SELECT id_utilisateurs, pseudo, nom, prenom, email FROM utilisateurs WHERE id_utilisateurs = ?");
$stmt->bindValue(1, $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pseudo = $_POST['pseudo'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];

    $update_stmt = $bdd->prepare("UPDATE utilisateurs SET pseudo = ?, nom = ?, prenom = ?, email = ? WHERE id_utilisateurs = ?");
    $update_stmt->bindValue(1, $pseudo, PDO::PARAM_STR);
    $update_stmt->bindValue(2, $nom, PDO::PARAM_STR);
    $update_stmt->bindValue(3, $prenom, PDO::PARAM_STR);
    $update_stmt->bindValue(4, $email, PDO::PARAM_STR);
    $update_stmt->bindValue(5, $user_id, PDO::PARAM_INT);
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
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Modifier Utilisateur</h2>
        <form method="post" class="mt-3">
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
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
>>>>>>> 084327abe52abe59871cc635c307ef2b601c5a28
</html>