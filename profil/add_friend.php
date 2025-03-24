<?php
session_start();
include('../include/database.php');
if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $bdd->prepare("SELECT u.pseudo, r.date_début
                               FROM relations
                               JOIN utilisateurs u ON u.id_utilisateurs = r.user_id1
                               WHERE r.user_id2 = ? AND r.status = 'pending'");
        $stmt->execute([$_SESSION['user_id']]);
        $friendRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $notificationCount = count($friendRequests);
    } catch (PDOException $e) {
        $notificationCount = 0;
    }
} else {
    $notificationCount = 0;
}

if (!isset($_SESSION['user_id'])) {
    header('Location: profil.php?error=not_logged_in');
    exit;
}

$friendPseudo = htmlspecialchars($_POST['friend_pseudo'] ?? '');
if (empty($friendPseudo)) {
    header('Location: profil.php?error=no_user_specified');
    exit;
}
try {
    $stmt = $bdd->prepare("SELECT id_utilisateurs FROM utilisateurs WHERE pseudo = ?");
    $stmt->execute([$friendPseudo]);
    $friend = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$friend) {
        header('Location: profil.php?error=user_not_found');
        exit;
    }

    $friendId = $friend['id_utilisateurs'];
    $userId = $_SESSION['user_id'];
    $stmt = $bdd->prepare("
        SELECT * FROM relations 
        WHERE (user_id1 = ? AND user_id2 = ?) OR (user_id1 = ? AND user_id2 = ?)
    ");
    $stmt->execute([$userId, $friendId, $friendId, $userId]);
    $existingRelation = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingRelation) {
        if ($existingRelation['ami'] == 1) {
            header('Location: profil.php?user=' . urlencode($friendPseudo) . '&error=already_friends');
        } elseif ($existingRelation['status'] === 'pending') {
            header('Location: profil.php?user=' . urlencode($friendPseudo) . '&error=request_pending');
        } else {
            header('Location: profil.php?user=' . urlencode($friendPseudo) . '&error=relation_exists');
        }
        exit;
    }
    $stmt = $bdd->prepare("
        INSERT INTO relations (user_id1, user_id2, ami, status, date_début) 
        VALUES (?, ?, 0, 'pending', NOW())
    ");
    $stmt->execute([$userId, $friendId]);

    header('Location: profil.php?user=' . urlencode($friendPseudo) . '&success=friend_request_sent');
    exit;

} catch (PDOException $e) {
    header('Location: profil.php?error=database_error');
    exit;
}
?>
