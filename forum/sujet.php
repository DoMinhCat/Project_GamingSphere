<?php
session_start();
require_once('../include/database.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Forum - sujet';
$pageCategory = 'forum';
echo "<script>const pageCategory = '$pageCategory';</script>";
require('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('location:' . forum_main . '?message=' . urlencode('Sujet non spécifié !'));
    exit;
}

$id_sujet = (int) $_GET['id'];
try {
    $stmt = $bdd->prepare("SELECT * FROM forum_sujets WHERE id_sujet = ?");
    $stmt->execute([$id_sujet]);
    $sujet = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$sujet) {
        header('location:' . forum_main . '?message=' . urlencode('Sujet non trouvé !'));
        exit;
    }
} catch (PDOException) {
    header('Location:' . forum_main . '?message=' . urlencode('Erreur de la base de données !'));
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['contenu']) && !empty(trim($_POST['contenu']))) {
    try {
        $contenu_reponse = trim($_POST['contenu']);
        $auteur = $_SESSION['user_pseudo'] ?? 'Anonyme';

        $stmt = $bdd->prepare("INSERT INTO forum_reponses (id_sujet, contenu, auteur) VALUES (?, ?, ?)");
        $stmt->execute([$id_sujet, $contenu_reponse, $auteur]);

        header('Location: ' . sujet . '?id=' . $id_sujet);
        exit;
    } catch (PDOException) {
        header('Location:' . forum_main . '?message=' . urlencode('Erreur lors de l\'ajoute de la réponse !'));
        exit();
    }
}

$stmt = $bdd->prepare("SELECT * FROM forum_reponses WHERE id_sujet = ? ORDER BY date_msg ASC");
$stmt->execute([$id_sujet]);
$reponses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<body>
    <?php include("../include/header.php"); ?>

    <div class="container my-5">
        <div class="mb-3 d-flex align-items-center gap-2">
            <a href="<?= forum_category . '?nom=' . $sujet['categories'] ?>" class="text-decoration-none fs-3 return_arrow">
                <i class="bi bi-chevron-left"></i>
            </a>
            <h1 class="m-0"><?= htmlspecialchars($sujet['titre']) ?></h1>
        </div>

        <div class="mb-4">
            <div class="p-3 border rounded">
                <p><?= nl2br(htmlspecialchars($sujet['contenu'] ?? '')) ?></p>
                <p class="text-muted text-end">Posté par <?= htmlspecialchars($sujet['auteur']) ?> le <?= date("d/m/Y à H:i", strtotime($sujet['date_msg'])) ?></p>
            </div>
        </div>

        <h4 class="mb-3">Réponses</h4>
        <?php if (count($reponses) === 0): ?>
            <p>Aucune réponse pour le moment.</p>
        <?php else: ?>
            <?php foreach ($reponses as $rep): ?>
                <div class="card mb-3 mx-0">
                    <div class="card-body">
                        <p class="mb-1"><?= nl2br(htmlspecialchars($rep['contenu'])) ?></p>
                        <p class="text-muted m-0 text-end">Par <?= htmlspecialchars($rep['auteur']) ?> le <?= date("d/m/Y à H:i", strtotime($rep['date_msg'])) ?></p>
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