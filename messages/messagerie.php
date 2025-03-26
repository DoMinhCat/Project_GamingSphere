<?php
session_start();
include('../include/database.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

try {
    $stmt = $bdd->prepare("
   SELECT u.id_utilisateurs, u.pseudo, u.photo_profil, 
       (SELECT MAX(m2.date_envoi) 
        FROM messages m2 
        WHERE (m2.expediteur_id = u.id_utilisateurs AND m2.destinataire_id = ?) 
           OR (m2.destinataire_id = u.id_utilisateurs AND m2.expediteur_id = ?)) 
       AS last_message
FROM utilisateurs u
WHERE EXISTS (
    SELECT 1 FROM messages m 
    WHERE (m.expediteur_id = u.id_utilisateurs AND m.destinataire_id = ?) 
       OR (m.destinataire_id = u.id_utilisateurs AND m.expediteur_id = ?)
)
AND EXISTS (
    SELECT 1 FROM relations r
    WHERE ((r.user_id1 = u.id_utilisateurs AND r.user_id2 = ?) 
        OR (r.user_id1 = ? AND r.user_id2 = u.id_utilisateurs))
    AND r.ami = 1
)
ORDER BY last_message DESC;
    ");
    $stmt->execute([$userId, $userId, $userId, $userId, $userId, $userId]);
    $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Messagerie';
include('../include/head.php');
?>
<body>
<?php include('../include/header.php'); ?>
<div class="container my-4">
    <h2 class="mb-4 text-center">Messagerie</h2>

    <?php if (!empty($conversations)): ?>
        <div class="list-group">
            <?php foreach ($conversations as $conversation): ?>
                <a href="conversation.php?user=<?= $conversation['id_utilisateurs'] ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                    <img src="/PA/profil/<?= htmlspecialchars($conversation['photo_profil'] ?: 'default-profile.jpg') ?>" alt="Profil" class="rounded-circle me-3" width="50" height="50">
                    <span><?= htmlspecialchars($conversation['pseudo']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">Aucune conversation pour le moment.</div>
    <?php endif; ?>

   
    <div class="text-center mt-4">
        <a href="nouvelle_conversation.php" class="btn btn-primary">Créer une conversation</a>
    </div>

</div>
<?php include('../include/footer.php'); ?> 
</body>
</html>
