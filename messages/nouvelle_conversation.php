<?php
session_start();
$login_page = '../connexion/login.php';
require('../include/database.php');
require('../include/check_session.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';

$userId = $_SESSION['user_id'];

try {
    $stmt = $bdd->prepare("
        SELECT u.id_utilisateurs, u.pseudo, u.photo_profil
        FROM relations r
        JOIN utilisateurs u ON (u.id_utilisateurs = r.user_id1 OR u.id_utilisateurs = r.user_id2)
        WHERE (r.user_id1 = ? OR r.user_id2 = ?)
          AND r.ami = 1
          AND u.id_utilisateurs != ?
    ");
    $stmt->execute([$userId, $userId, $userId]);
    $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $friendId = $_POST['friend_id'];

    try {
        $stmt = $bdd->prepare("INSERT INTO messages (expediteur_id, destinataire_id, contenu, date_envoi) VALUES (?, ?, ?, NOW())");
    } catch (PDOException $e) {
        echo "Erreur : " . htmlspecialchars($e->getMessage());
        exit;
    }
    header('Location:' . conversation . '?user=' . $friendId);
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<?php $title = 'Nouvelle Conversation';
require('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}
?>

<body>
    <?php include('../include/header.php'); ?>
    <div class="container my-4">
        <h2 class="mb-4 text-center">Choisir un ami pour démarrer une conversation</h2>
        <?php if (count($friends) > 0): ?>
            <form method="POST" action="<?= nouvelle_conversation ?>">
                <div class="list-group">
                    <?php foreach ($friends as $friend): ?>
                        <label class="list-group-item d-flex align-items-center">
                            <input type="radio" name="friend_id" value="<?= htmlspecialchars($friend['id_utilisateurs']) ?>" required class="form-check-input me-2">
                            <img src="../profil/<?= htmlspecialchars($friend['photo_profil'] ?: 'uploads/profiles_pictures/default_profile_img.jpg') ?>" alt="Profil" class="rounded-circle me-3" width="50" height="50">
                            <span><?= htmlspecialchars($friend['pseudo']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">Démarrer la conversation</button>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-info text-center">Vous n'avez aucun ami pour démarrer une conversation.</div>
        <?php endif; ?>
    </div>
    <?php include('../include/footer.php'); ?>
</body>

</html>