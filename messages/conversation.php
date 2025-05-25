<?php
session_start();
$login_page = '../connexion/login.php';
require('../include/check_session.php');
require('../include/database.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';

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
    $messageContent = trim($_POST['message']);
    if (!empty($messageContent)) {
        $messageContent = htmlspecialchars($messageContent, ENT_QUOTES, 'UTF-8'); // 
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
            header('Location:' . conversation . '?user=' . $otherUserId);
            exit;
        } catch (PDOException $e) {
            echo "Erreur : " . htmlspecialchars($e->getMessage());
            exit;
        }
    }
}

$stmt = $bdd->prepare("SELECT last_active FROM utilisateurs WHERE id_utilisateurs=?");
$stmt->execute([$otherUserId]);
$otherUserStatus = $stmt->fetch(PDO::FETCH_ASSOC);

function isOnline($lastActive)
{
    $timeout = 60;
    $lastActiveTime = strtotime($lastActive);
    return (time() - $lastActiveTime) <= $timeout;
}
$isUserOnline = isOnline($otherUserStatus['last_active']);
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Conversation avec ' . htmlspecialchars($otherUser['pseudo']);
$pageCategory = 'message';
echo "<script>const pageCategory = '$pageCategory';</script>";
include('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}
?>


<body>
    <?php include('../include/header.php'); ?>
    <div class="container">
        <div class="conversation-container message_box my-5">
            <div class="header-chat">
                <a href="<?= messagerie ?>" class="btn btn-primary btn-sm me-3">
                    <i class="bi bi-chevron-left"></i>
                    <span>Retour</span>
                </a>
                <a href="<?= profil ?>?user=<?php echo urlencode($otherUser['pseudo']); ?>" id="linkProfileFromConv" class="d-flex align-items-center">
                    <div class="avatar-container position-relative">
                        <img src="/profil/<?= ($conversation['photo_profil'] ? htmlspecialchars($conversation['photo_profil']) : 'uploads/profiles_pictures/default_profile_img.jpg') ?>" alt="avt" class="rounded-circle m-0" width="40" height="40">

                        <?php if ($isUserOnline): ?>
                            <span class="position-absolute bottom-0 start-100 bg-success border border-light translate-middle rounded-circle p-1" style="width: 10px; height: 10px; font-size: 0.75rem; margin-bottom: -5px; margin-right: -5px;"></span>
                        <?php else: ?>
                            <span class="position-absolute bottom-0 start-100 bg-secondary border border-light rounded-circle translate-middle p-1" style="width: 10px; height: 10px; font-size: 0.75rem; margin-bottom: -5px; margin-right: -5px;"></span>
                        <?php endif; ?>
                    </div>
                    <h5 class="ms-2 mb-0"><?= htmlspecialchars($otherUser['pseudo']) ?></h5>
                </a>
            </div>

            <div class="message-list p-3">
                <?php foreach ($messages as $message): ?>
                    <div class="message mb-3 <?= $message['expediteur'] == $otherUser['pseudo'] ? 'received' : 'sent' ?>">
                        <div class="d-flex flex-column">
                            <div class="message-bubble p-2">
                                <p><?= nl2br(htmlspecialchars($message['contenu'], ENT_QUOTES, 'UTF-8')) ?></p>
                                <!-- Reaction display and button -->
                                <div class="reaction-section mt-1">
                                    <span class="reactions" data-message-id="<?= $message['id_messages'] ?>">
                                        <?php
                                        $stmtReaction = $bdd->prepare("SELECT emoji FROM reactions 
                                                                            WHERE id_message = ? ORDER BY date_reaction ASC
                                                                            ");
                                        $stmtReaction->execute([$message['id_messages']]);
                                        $reactions = $stmtReaction->fetchAll(PDO::FETCH_COLUMN);
                                        foreach ($reactions as $reaction) {
                                            echo "<span class='reaction-emoji me-1'>$reaction</span>";
                                        }
                                        ?>
                                        <button class="btn btn-sm btn-light react-btn" data-message-id="<?= $message['id_messages'] ?>">+</button>
                                    </span>
                                </div>

                            </div>
                            <div class="message-time"><?= date('H:i', strtotime($message['date_envoi'])) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <form action="<?= conversation . '?user=' . $otherUserId ?>" method="POST" class="message-input-container d-flex align-items-center">
                <textarea id="messageTextarea" name="message" rows="1" placeholder="Écrivez un message..." class="p-2 me-2 flex-grow-1"></textarea>
                <button type="button" id="emoji-button" class="btn btn-sm btn-light me-2">😊</button>
                <button type="submit" class="send-button btn btn-primary">Envoyer</button>
            </form>
        </div>
    </div>

    <script src="refresh.js"></script>

    <script>
        const button = document.querySelector('#emoji-button');
        const textarea = document.querySelector('#messageTextarea');
        const picker = new EmojiButton();

        picker.on('emoji', emoji => {
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            textarea.setRangeText(emoji, start, end, 'end');
        });

        button.addEventListener('click', () => {
            picker.togglePicker(button);
        });
    </script>
    <script>
        const picker2 = new EmojiButton();

        document.querySelectorAll('.react-btn').forEach(button => {
            button.addEventListener('click', () => {
                const messageId = button.dataset.messageId;
                picker2.togglePicker(button);

                picker2.on('emoji', emoji => {
                    fetch('react.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `message_id=${messageId}&emoji=${encodeURIComponent(emoji)}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            }
                        });
                });
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@4.6.2/dist/index.min.js"></script>

</body>

</html>