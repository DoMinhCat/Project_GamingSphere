<?php
session_start();
require_once('../include/database.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Forum';
$pageCategory = 'forum';
echo "<script>const pageCategory = '$pageCategory';</script>";
require('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
    echo '<script src="../include/check_timeout.js"></script>';
}

?>

<body>
    <?php include("../include/header.php"); ?>

    <main class="container mt-2 mb-5">
        <?php if (!empty($_GET['message'])) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif ?>
        <h1 class="mt-5 mb-4 text-center">Forum - Catégories</h1>
        <?php
        try {
            $query = $bdd->query("SELECT DISTINCT categories FROM forum_sujets WHERE parent_id IS NULL");
        } catch (PDOException) {
            header('location:' . index_front . '?message=bdd');
            exit;
        }

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            try {
                $categorie = $row['categories'];

                $stmt = $bdd->prepare("SELECT COUNT(*) FROM forum_sujets WHERE categories = ? AND parent_id IS NULL");
                $stmt->execute([$categorie]);
                $nb_sujets = $stmt->fetchColumn();

                $stmt = $bdd->prepare("SELECT id_sujet FROM forum_sujets WHERE categories = ? AND parent_id IS NULL");
                $stmt->execute([$categorie]);
                $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

                $nb_messages = 0;
                if (!empty($ids)) {
                    $placeholders = implode(',', array_fill(0, count($ids), '?'));
                    $stmt = $bdd->prepare("SELECT COUNT(*) FROM forum_reponses WHERE id_sujet IN ($placeholders)");
                    $stmt->execute($ids);
                    $nb_messages = $stmt->fetchColumn();
                }
            } catch (PDOException) {
                header('location:' . index_front . '?message=bdd');
                exit;
            }

        ?>
            <a href="<?= forum_category ?>?nom=<?= urlencode($categorie) ?>" class="text-decoration-none text-dark forumBlockLink mb-3">
                <div class="card mb-3 shadow-sm border-0">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">
                                <?= htmlspecialchars($categorie) ?>
                            </h5>
                        </div>
                        <div class="text-end">
                            <div><strong><?= $nb_sujets ?></strong> sujets</div>
                            <div><strong><?= $nb_messages ?></strong> messages</div>
                        </div>
                    </div>
                </div>
            </a>
        <?php } ?>
    </main>

    <?php include("../include/footer.php"); ?>
</body>

</html>