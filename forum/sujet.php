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

if (empty($_GET['category']) || empty($_GET['id'])) {
    header('location:' . forum_main . '?message=' . urlencode('Sujet non spécifié !'));
    exit;
}
$categorie_nom = $_GET['category'];
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

        header('Location: ' . sujet . '?id=' . $id_sujet . '&category=' . $categorie_nom);
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
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="<?= forum_main ?>" class="text-decoration-none footer-link">Forum</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="<?= forum_category . '?nom=' . $sujet['categories'] ?>" class="text-decoration-none footer-link"><?= htmlspecialchars($categorie_nom) ?></a>
                        </li>
                        <li class="breadcrumb-item active footer-link" aria-current="page"><?= htmlspecialchars($sujet['titre']) ?></li>
                    </ol>
                </nav>
                <div class="mb-4">
                    <a href="<?= forum_category . '?nom=' . $sujet['categories'] ?>" class="text-decoration-none fs-3 return_arrow d-flex align-items-center gap-2">
                        <i class="bi bi-chevron-left"></i>
                        <h1 class="m-0"><?= htmlspecialchars($categorie_nom) ?></h1>
                    </a>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="text-center mb-4">
                    <h1 class="display-6 fw-bold mb-0"><?= htmlspecialchars($sujet['titre']) ?></h1>
                </div>
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-header bg-sujet text-white d-flex align-items-center">
                        <i class="bi bi-chat-square-text me-2"></i>
                        <span class="fw-bold">Sujet discussion</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <p class="mb-0 lh-lg"><?= nl2br(htmlspecialchars($sujet['contenu'])) ?></p>
                        </div>
                        <div class="border-top pt-3">
                            <div class="d-flex align-items-center justify-content-between small">
                                <div class="d-flex align-items-center">
                                    <a href="<?= profil . '?user=' . ($sujet['auteur'] != 'Anonyme' ? $sujet['auteur'] : '#') ?>">
                                        <i class="bi bi-person-circle me-1 text-muted"></i>
                                        <strong class="text-decoration-none"><?= htmlspecialchars($sujet['auteur']) ?></strong>
                                    </a>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-clock me-1"></i>
                                    <?= date("d/m/Y à H:i", strtotime($sujet['date_msg'])) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-5">
                    <div class="d-flex align-items-center mb-4">
                        <h3 class="h4 mb-0 me-3">
                            </i> Réponses
                        </h3>
                        <span class="badge bg-secondary"><?= count($reponses) ?></span>
                    </div>

                    <?php if (count($reponses) === 0): ?>
                        <div class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-chat-square display-4 mb-3"></i>
                                <p class="h5">Aucune réponse pour le moment</p>
                                <p>Soyez le premier à répondre à cette discussion !</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="replies-container">
                            <?php foreach ($reponses as $index => $rep): ?>
                                <div class="card mb-3 border-start border-secondary border-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-light text-dark me-2">#<?= $index + 1 ?></span>
                                                <div class="d-flex align-items-center text-muted small">
                                                    <i class="bi bi-person-circle me-1"></i>
                                                    <strong class="text-dark"><?= htmlspecialchars($rep['auteur']) ?></strong>
                                                </div>
                                            </div>
                                            <div class="text-muted small">
                                                <i class="bi bi-clock me-1"></i>
                                                <?= date("d/m/Y à H:i", strtotime($rep['date_msg'])) ?>
                                            </div>
                                        </div>
                                        <div class="ms-4">
                                            <p class="mb-0 lh-lg"><?= nl2br(htmlspecialchars($rep['contenu'])) ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0 d-flex align-items-center">
                            Ajouter une réponse
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="" method="post">
                            <div class="mb-3">
                                <label for="content" class="form-label fw-bold">
                                    <i class="bi bi-pencil-square me-1"></i>
                                    Votre message
                                </label>
                                <textarea
                                    name="contenu"
                                    id="content"
                                    class="form-control form-control-lg"
                                    rows="5"
                                    placeholder="Écrivez votre réponse ici..."
                                    required
                                    style="resize: vertical;"></textarea>
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Soyez respectueux et constructif dans vos réponses.
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="bi bi-person-badge me-1"></i>
                                    Connecté en tant que: <strong><?= $_SESSION['user_pseudo'] ?? 'Anonyme' ?></strong>
                                </small>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send me-1"></i>
                                    Publier la réponse
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include("../include/footer.php"); ?>

</body>

</html>