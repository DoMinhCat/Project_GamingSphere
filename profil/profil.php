<?php
session_start();
$login_page = '../connexion/login.php';
require('../include/check_session.php');
require('../include/database.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';

$pseudo = $_GET['user'] ?? '';

if (empty($pseudo)) {
    header('location:' . index_front . '?error=' . urlencode('Utilisateur introuvable !'));
    exit;
}


if (isset($_SESSION['user_pseudo']) && $_SESSION['user_pseudo'] === $pseudo) {
    header('Location:' . my_account);
    exit;
}

try {
    $stmt = $bdd->prepare("SELECT pseudo, date_inscription, photo_profil, bio FROM utilisateurs WHERE pseudo = ?");
    $stmt->execute([$pseudo]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header('location:' . index_front . '?error=' . urlencode('Utilisateur introuvable !'));
        exit;
    }
} catch (PDOException $e) {
    header('location:' . index_front . '?message=bdd');
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

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?php
                        switch ($_GET['error']) {
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
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <?php
                        switch ($_GET['success']) {
                            case 'friend_request_sent':
                                echo "La demande d'ami a été envoyée avec succès.";
                                break;
                            default:
                                $friendPseudo = isset($_GET['user']) ? htmlspecialchars($_GET['user']) : 'votre ami';
                                echo "Vous êtes ami avec \"" . $friendPseudo . "\"";
                                break;
                        }
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <?php if (!empty($user['photo_profil'])): ?>
                                <img src="<?php echo htmlspecialchars($user['photo_profil']); ?>"
                                    alt="Photo de profil de <?php echo htmlspecialchars($user['pseudo']); ?>"
                                    class="rounded-circle img-fluid border border-3 border-light shadow-sm"
                                    style="width: 150px; height: 150px; object-fit: cover;">
                            <?php else: ?>
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto"
                                    style="width: 150px; height: 150px;">
                                    <i class="bi bi-person-fill text-white" style="font-size: 4rem;"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <h1 class="h2 fw-bold text-primary mb-3"><?php echo htmlspecialchars($user['pseudo']); ?></h1>

                        <div class="mb-4">
                            <span class="badge bg-light text-dark border px-3 py-2">
                                <i class="bi bi-calendar-event me-2"></i>
                                Membre depuis <?php echo date('F Y', strtotime($user['date_inscription'])); ?>
                            </span>
                        </div>

                        <?php if (!empty($user['bio'])): ?>
                            <div class="card bg-light border-0 mb-4">
                                <div class="card-body">
                                    <h5 class="card-title text-start">
                                        <i class="bi bi-chat-quote me-2"></i>À propos
                                    </h5>
                                    <p class="card-text text-start text-muted mb-0">
                                        <?= nl2br(htmlspecialchars($user['bio'])) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_pseudo'] !== $pseudo): ?>
                            <form method="POST" action="add_friend.php" class="mt-4">
                                <input type="hidden" name="friend_pseudo" value="<?php echo htmlspecialchars($user['pseudo']); ?>">
                                <button type="submit" class="btn btn-primary btn-lg px-4 py-2">
                                    <i class="bi bi-person-plus-fill me-2"></i>
                                    Ajouter en ami
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="<?= index_front ?>" class="btn btn-primary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Retour à l'accueil
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php include('../include/footer.php'); ?>
</body>

</html>