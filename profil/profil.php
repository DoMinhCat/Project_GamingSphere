<?php
session_start();
$login_page = '../connexion/login.php';
require('../include/check_session.php');
require('../include/database.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';

$pseudo = $_GET['user'] ?? '';

if (empty($pseudo)) {
    header('Location: ' . profil . '?error=no_user_specified');
    exit();
}


if (isset($_SESSION['user_pseudo']) && $_SESSION['user_pseudo'] === $pseudo) {
    header('Location:' . my_account);
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
<?php
$title = "Profil de " . htmlspecialchars($pseudo);
$pageCategory = 'profil';
echo "<script>const pageCategory = '$pageCategory';</script>";
require('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}
?>

<body>
    <?php include('../include/header.php'); ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger" role="alert">
            <?php
            switch ($_GET['error']) {
                case 'no_user_specified':
                    echo "Aucun utilisateur spécifié.";
                    break;
                case 'user_not_found':
                    echo "Utilisateur introuvable.";
                    break;
                case 'already_friends':
                    echo "Vous êtes déjà amis avec cet utilisateur.";
                    break;
                case 'request_pending':
                    echo "Une demande d'ami est déjà en attente.";
                    break;
                case 'relation_exists':
                    echo "Une relation existe déjà avec cet utilisateur.";
                    break;
                case 'database_error':
                    echo "Une erreur est survenue lors de la connexion à la base de données.";
                    break;
                default:
                    echo "Une erreur inconnue est survenue.";
                    break;
            }
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success" role="alert">
            <?php
            switch ($_GET['success']) {
                case 'friend_request_sent':
                    echo "La demande d'ami a été envoyée avec succès.";
                    break;
                default:
                    $friendPseudo = isset($_GET['user']) ? htmlspecialchars($_GET['user']) : 'votre ami';
                    echo "Vous êtes amis avec \"" . $friendPseudo . "\"";
                    break;
            }
            ?>
        </div>
    <?php endif; ?>
    <main class="container my-5">
        <?php if (!empty($user['photo_profil'])): ?>
            <div class="text-center mb-3">
                <img src="<?php echo htmlspecialchars($user['photo_profil']); ?>" alt="Photo de profil de <?php echo htmlspecialchars($user['pseudo']); ?>" class="img-fluid" style="max-width: 150px; border-radius: 30%;">
            </div>
        <?php endif; ?>
        <h1>Profil de <?php echo htmlspecialchars($user['pseudo']); ?></h1>
        <p>Date d'inscription : <?php echo htmlspecialchars($user['date_inscription']); ?></p>
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_pseudo'] !== $pseudo): ?>
            <form method="POST" action="add_friend.php">
                <input type="hidden" name="friend_pseudo" value="<?php echo htmlspecialchars($user['pseudo']); ?>">
                <button type="submit" class="btn btn-primary">Ajouter en ami</button>
            </form>
        <?php endif; ?>
    </main>
    <?php include('../include/header.php'); ?>
</body>