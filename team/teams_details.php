<?php
// Vérifiez si l'utilisateur est déjà membre de l'équipe
$stmt = $bdd->prepare("SELECT COUNT(*) FROM membres_equipe WHERE id_equipe = ? AND id_utilisateur = ?");
$stmt->execute([$teamId, $_SESSION['user_id']]);
$isMember = $stmt->fetchColumn() > 0;

// Affichez le bouton "Rejoindre l'équipe" uniquement si l'utilisateur n'est pas membre
if (!$isMember): ?>
    <form action="../team/join_team.php" method="POST" class="mt-3">
        <input type="hidden" name="team_id" value="<?= htmlspecialchars($teamId) ?>">
        <button type="submit" class="btn btn-primary">Rejoindre l'équipe</button>
    </form>
<?php endif; ?>