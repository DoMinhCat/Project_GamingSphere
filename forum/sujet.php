<?php
session_start();
require('../include/database.php');
require('../include/check_timeout.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Forum - sujet';
require('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}

if (!isset($bdd)) {
    die("Erreur de connexion à la base de données");
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Sujet non spécifié.");
}

$id_sujet = (int) $_GET['id'];

$stmt = $bdd->prepare("SELECT * FROM forum_sujets WHERE id_sujet = ?");
$stmt->execute([$id_sujet]);
$sujet = $stmt->fetch();

if (!$sujet) {
    die("Sujet introuvable.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['contenu']) && !empty(trim($_POST['contenu']))) {
    $contenu_reponse = trim($_POST['contenu']);
    $auteur = $_SESSION['utilisateur'] ?? 'Anonyme';

    $stmt = $bdd->prepare("INSERT INTO forum_reponses (id_sujet, contenu, auteur) VALUES (?, ?, ?)");
    $stmt->execute([$id_sujet, $contenu_reponse, $auteur]);

    header('Location: ' . sujet . '?id=' . $id_sujet);
    exit;
}

$stmt = $bdd->prepare("SELECT * FROM forum_reponses WHERE id_sujet = ? ORDER BY date_msg ASC");
$stmt->execute([$id_sujet]);
$reponses = $stmt->fetchAll();
?>

<body>
    <?php include("../include/header.php"); ?>

    <div class="container my-5">
        <h2 class="mb-3"><?= htmlspecialchars($sujet['titre']) ?></h2>
        <div class="mb-4">
            <div class="p-3 border rounded bg-light">
                <p><?= nl2br(htmlspecialchars($sujet['contenu'] ?? '')) ?></p>
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