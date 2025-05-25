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
        $messageContent = htmlspecialchars($messageContent, ENT_QUOTES, 'UTF-8');
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
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card shadow-sm my-4">
                    <div class="card-header bg-light border-bottom p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <a href="<?= messagerie ?>" class="btn btn-outline-primary btn-sm me-3">
                                    <i class="bi bi-chevron-left"></i>
                                    <span class="d-none d-sm-inline">Retour</span>
                                </a>
                                <a href="<?= profil ?>?user=<?php echo urlencode($otherUser['pseudo']); ?>"
                                    class="text-decoration-none d-flex align-items-center">
                                    <div class="position-relative">
                                        <img src="/profil/<?= ($otherUser['photo_profil'] ? htmlspecialchars($otherUser['photo_profil']) : 'uploads/profiles_pictures/default_profile_img.jpg') ?>"
                                            alt="Avatar"
                                            class="rounded-circle border border-2 border-light"
                                            width="45"
                                            height="45">
                                        <span class="position-absolute bottom-0 end-0 <?= $isUserOnline ? 'bg-success' : 'bg-secondary' ?> 
                                                     border border-2 border-white rounded-circle"
                                            style="width: 12px; height: 12px;"></span>
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="mb-0 fw-bold text-dark"><?= htmlspecialchars($otherUser['pseudo']) ?></h6>
                                        <small class="text-muted"><?= $isUserOnline ? 'En ligne' : 'Hors ligne' ?></small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Messages -->
                    <div class="card-body p-0" style="height: 500px; overflow-y: auto;">
                        <div class="p-3" id="message-container">
                            <?php if (empty($messages)): ?>
                                <div class="text-center text-muted py-5">
                                    <i class="bi bi-chat-dots display-4 mb-3"></i>
                                    <p>Aucun message encore. Commencez la conversation !</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($messages as $message): ?>
                                    <?php $isSent = $message['expediteur'] !== $otherUser['pseudo']; ?>
                                    <div class="d-flex mb-3 <?= $isSent ? 'justify-content-end' : 'justify-content-start' ?>">
                                        <div class="<?= $isSent ? 'order-2' : 'order-1' ?>" style="max-width: 75%;">
                                            <!-- Message Bubble -->
                                            <div class="<?= $isSent ? 'bg-primary text-white' : 'bg-light text-dark' ?> 
                                                        rounded-3 p-3 shadow-sm position-relative">
                                                <p class="mb-0"><?= nl2br(htmlspecialchars($message['contenu'], ENT_QUOTES, 'UTF-8')) ?></p>
                                            </div>

                                            <!-- Reactions -->
                                            <div class="d-flex align-items-center mt-1 <?= $isSent ? 'justify-content-end' : 'justify-content-start' ?>">
                                                <div class="reactions-container d-flex align-items-center">
                                                    <div class="reactions me-2" data-message-id="<?= $message['id_messages'] ?>">
                                                        <?php
                                                        $stmtReaction = $bdd->prepare("SELECT emoji, COUNT(*) as count 
                                                                                      FROM reactions 
                                                                                      WHERE id_message = ? 
                                                                                      GROUP BY emoji 
                                                                                      ORDER BY count DESC");
                                                        $stmtReaction->execute([$message['id_messages']]);
                                                        $reactions = $stmtReaction->fetchAll(PDO::FETCH_ASSOC);

                                                        foreach ($reactions as $reaction):
                                                        ?>
                                                            <span class="badge bg-light text-dark border me-1 reaction-badge"
                                                                style="font-size: 0.9rem;">
                                                                <?= $reaction['emoji'] ?>
                                                                <?php if ($reaction['count'] > 1): ?>
                                                                    <small class="ms-1"><?= $reaction['count'] ?></small>
                                                                <?php endif; ?>
                                                            </span>
                                                        <?php endforeach; ?>
                                                    </div>

                                                    <!-- React -->
                                                    <button class="btn btn-light btn-sm rounded-circle p-1 react-btn border-0 shadow-sm"
                                                        data-message-id="<?= $message['id_messages'] ?>"
                                                        style="width: 28px; height: 28px; font-size: 0.8rem;"
                                                        title="Ajouter une réaction">
                                                        <i class="bi bi-emoji-smile"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="<?= $isSent ? 'text-end' : 'text-start' ?> mt-1">
                                                <small class="text-muted"><?= date('H:i', strtotime($message['date_envoi'])) ?></small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Message Input -->
                    <div class="card-footer bg-light border-top p-3">
                        <form action="<?= conversation . '?user=' . $otherUserId ?>" method="POST" class="d-flex align-items-end gap-2">
                            <div class="flex-grow-1">
                                <div class="input-group">
                                    <textarea id="messageTextarea"
                                        name="message"
                                        rows="1"
                                        placeholder="Écrivez votre message..."
                                        class="form-control border-0 shadow-sm"
                                        style="resize: none; max-height: 100px;"></textarea>
                                    <button type="button"
                                        id="emoji-button"
                                        class="btn btn-outline-secondary"
                                        title="Ajouter un emoji">
                                        <i class="bi bi-emoji-smile"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="submit"
                                class="btn btn-primary d-flex align-items-center justify-content-center"
                                style="width: 45px; height: 45px;">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/@joeattardi/emoji-button@4.6.4/dist/index.js"></script>
    <script src="refresh.js"></script>

    <script>
        const textarea = document.getElementById('messageTextarea');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 100) + 'px';
        });

        const emojiButton = document.querySelector('#emoji-button');
        const messageTextarea = document.querySelector('#messageTextarea');
        let messagePicker;

        document.addEventListener('DOMContentLoaded', function() {
            if (typeof EmojiButton !== 'undefined') {
                messagePicker = new EmojiButton({
                    position: 'top-end',
                    autoHide: false,
                    showPreview: false,
                    style: 'apple'
                });

                messagePicker.on('emoji', emoji => {
                    const start = messageTextarea.selectionStart;
                    const end = messageTextarea.selectionEnd;
                    const text = messageTextarea.value;
                    messageTextarea.value = text.substring(0, start) + emoji + text.substring(end);
                    messageTextarea.focus();
                    messageTextarea.setSelectionRange(start + emoji.length, start + emoji.length);
                });

                emojiButton.addEventListener('click', (e) => {
                    e.preventDefault();
                    messagePicker.togglePicker(emojiButton);
                });
            }
        });


        let reactionPicker;
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof EmojiButton !== 'undefined') {
                reactionPicker = new EmojiButton({
                    position: 'top-start',
                    autoHide: true,
                    showPreview: false,
                    style: 'apple'
                });

                reactionPicker.on('emoji', emoji => {
                    if (!reactionPicker._targetMessageId) return;

                    fetch('react.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `message_id=${reactionPicker._targetMessageId}&emoji=${encodeURIComponent(emoji)}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                updateReactions(reactionPicker._targetMessageId, emoji);
                            } else {
                                console.error('Failed to add reaction');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });

                    reactionPicker._targetMessageId = null;
                });

                document.querySelectorAll('.react-btn').forEach(button => {
                    button.addEventListener('click', (e) => {
                        e.preventDefault();
                        reactionPicker._targetMessageId = button.dataset.messageId;
                        reactionPicker.togglePicker(button);
                    });
                });
            }
        });

        function updateReactions(messageId, newEmoji) {
            const reactionsContainer = document.querySelector(`[data-message-id="${messageId}"]`);
            if (reactionsContainer) {
                const newReaction = document.createElement('span');
                newReaction.className = 'badge bg-light text-dark border me-1 reaction-badge';
                newReaction.style.fontSize = '0.9rem';
                newReaction.textContent = newEmoji;

                const reactButton = reactionsContainer.parentElement.querySelector('.react-btn');
                reactionsContainer.insertBefore(newReaction, reactButton);
            }
        }

        function scrollToBottom() {
            const messageContainer = document.getElementById('message-container');
            messageContainer.scrollTop = messageContainer.scrollHeight;
        }

        window.addEventListener('load', scrollToBottom);

        const messageForm = document.querySelector('form');
        messageForm.addEventListener('submit', function(e) {
            const messageInput = document.getElementById('messageTextarea');
            if (!messageInput.value.trim()) {
                e.preventDefault();
                return;
            }

            setTimeout(() => {
                messageInput.style.height = 'auto';
            }, 100);
        });

        messageTextarea.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (this.value.trim()) {
                    messageForm.dispatchEvent(new Event('submit'));
                }
            }
        });
    </script>

</body>

</html>