<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Sujet';
include('../include/head.php');
include('../include/database.php');

if (!isset($bdd)) {
    die("Erreur de connexion à la base de données");
}

// Vérification de l'ID du sujet
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Sujet non spécifié.");
}

$id_sujet = (int) $_GET['id'];

// Récupération du sujet
$stmt = $bdd->prepare("SELECT * FROM messages WHERE id_message = ?");
$stmt->execute([$id_sujet]);
$sujet = $stmt->fetch();

if (!$sujet) {
    die("Sujet introuvable.");
}

// Traitement de l'envoi de réponse
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['contenu']) && !empty(trim($_POST['contenu']))) {
    $contenu_reponse = trim($_POST['contenu']);
    $auteur = $_SESSION['utilisateur'] ?? 'Anonyme';

    $stmt = $bdd->prepare("INSERT INTO reponses_forum (id_sujet, contenu, auteur) VALUES (?, ?, ?)");
    $stmt->execute([$id_sujet, $contenu_reponse, $auteur]);

    // Recharger la page pour afficher la réponse
    header("Location: sujet.php?id=" . $id_sujet);
    exit;
}

// Récupération des réponses
$stmt = $bdd->prepare("SELECT * FROM reponses_forum WHERE id_sujet = ? ORDER BY date_msg ASC");
$stmt->execute([$id_sujet]);
$reponses = $stmt->fetchAll();
?>

<body>
<?php include("../include/header.php"); ?>

<div class="container my-5">
    <h2 class="mb-3"><?= htmlspecialchars($sujet['titre']) ?></h2>
    <div class="mb-4">
        <div class="p-3 border rounded bg-light">
            <p><?= nl2br(htmlspecialchars($sujet['contenu'])) ?></p>
            <p class="text-muted text-end">Posté par <?= htmlspecialchars($sujet['auteur']) ?> le <?= date("d/m/Y à H:i", strtotime($sujet['date_msg'])) ?></p>
        </div>
    </div>

    <h4 class="mb-3">Réponses</h4>
    <?php if (count($reponses) === 0): ?>
        <p class="text-muted">Aucune réponse pour le moment.</p>
    <?php else: ?>
        <?php foreach ($reponses as $rep): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <p><?= nl2br(htmlspecialchars($rep['contenu'])) ?></p>
                    <p class="text-muted text-end">Par <?= htmlspecialchars($rep['auteur']) ?> le <?= date("d/m/Y à H:i", strtotime($rep['date_msg'])) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <h4 class="mt-5">Ajouter une réponse</h4>
    <form method="post">
        <div class="mb-3">
            <textarea name="contenu" class="form-control" rows="4" placeholder="Votre message..." required></textarea>
        </div>
        <button type="submit" class="btn btn-success">Envoyer</button>
    </form>
</div>

<?php include("../include/footer.php"); ?>
</body>
</html>
