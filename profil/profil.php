<?php
session_start();
include('../include/database.php');

$pseudo = $_GET['user'] ?? '';

if (empty($pseudo)) {
    echo "Aucun utilisateur spécifié.";
    exit;
}

if (isset($_SESSION['user_pseudo']) && $_SESSION['user_pseudo'] === $pseudo) {
    header('Location: my_account.php');
    exit;
}

try {
    $stmt = $bdd->prepare("SELECT pseudo, date_inscription, photo_profil FROM utilisateurs WHERE pseudo = ?");
    $stmt->execute([$pseudo]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "Utilisateur introuvable.";
        exit;
    }
} catch (PDOException $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<?php $title = "Profil de " . htmlspecialchars($pseudo);
include('../include/head.php');
include('../include/header.php'); 
?>
<body>
    <div class="container mt-4">
        <?php if (!empty($user['photo_profil'])): ?>
            <div class="text-center mb-3">
                <img src="<?php echo htmlspecialchars($user['photo_profil']); ?>" alt="Photo de profil de <?php echo htmlspecialchars($user['pseudo']); ?>" class="img-fluid" style="max-width: 150px; border-radius: 30%;">
            </div>
        <?php endif; ?>
        <h1>Profil de <?php echo htmlspecialchars($user['pseudo']); ?></h1>
        <p>Date d'inscription : <?php echo htmlspecialchars($user['date_inscription']); ?></p>
    </div>
</body>
<?php include('../include/footer.php');?>
</html>