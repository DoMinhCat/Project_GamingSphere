<?php
session_start();
include('../include/database.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$otherUserId = isset($_GET['user']) ? (int)$_GET['user'] : 0;

if ($otherUserId <= 0) {
    echo "Utilisateur non valide.";
    exit;
}

try {
    $stmt = $bdd->prepare("SELECT pseudo, photo_profil FROM utilisateurs WHERE id_utilisateurs = ?");
    $stmt->execute([$otherUserId]);
    $otherUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$otherUser) {
        echo "Utilisateur non trouvé.";
        exit;
    }
} catch (PDOException $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
    exit;
}

try {
    $stmt = $bdd->prepare("
        SELECT m.id_messages, m.contenu, m.date_envoi, u.pseudo AS expediteur
        FROM messages m
        JOIN utilisateurs u ON u.id_utilisateurs = m.expediteur_id
        WHERE (m.expediteur_id = ? AND m.destinataire_id = ?) 
           OR (m.destinataire_id = ? AND m.expediteur_id = ?)
        ORDER BY m.date_envoi ASC
    ");
    $stmt->execute([$userId, $otherUserId, $userId, $otherUserId]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $messageContent = htmlspecialchars($_POST['message']);
    if (!empty($messageContent)) {
        try {
            $stmt = $bdd->prepare("
                INSERT INTO messages (expediteur_id, destinataire_id, contenu, date_envoi)
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$userId, $otherUserId, $messageContent]);
            $messageTime = date('H:i', strtotime('NOW'));
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
                echo json_encode([
                    'success' => true,
                    'time' => $messageTime
                ]);
                exit;
            }
            header("Location: conversation.php?user=$otherUserId");
            exit;

        } catch (PDOException $e) {
            echo "Erreur : " . htmlspecialchars($e->getMessage());
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<?php $title = 'Conversation avec ' . htmlspecialchars($otherUser['pseudo']); include('../include/head.php'); ?>  
<body>
<?php include('../include/header.php'); ?>
<div class="container">
    <div class="conversation-container message_box">
        <div class="header-chat">
            <img src="/PA/profil/<?= htmlspecialchars($otherUser['photo_profil'] ?: 'default-profile.jpg') ?>" alt="Photo de profil">
            <h5><?= htmlspecialchars($otherUser['pseudo']) ?></h5>
        </div>

        <div class="message-list">
            <?php foreach ($messages as $message): ?>
                <div class="message <?= $message['expediteur'] == $otherUser['pseudo'] ? 'received' : 'sent' ?>">
                    <div class="message-bubble">
                        <p><?= nl2br(htmlspecialchars($message['contenu'])) ?></p>
                        <div class="message-time"><?= date('H:i', strtotime($message['date_envoi'])) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <form action="conversation.php?user=<?= $otherUserId ?>" method="POST" class="message-input-container">
            <textarea name="message" rows="2" placeholder="Écrivez un message..." required></textarea>
            <button type="submit" class="send-button">Envoyer</button>
        </form>
    </div>
</div>
<script src=""></script>
<?php include('../include/footer.php'); ?>
</body>
</html>
