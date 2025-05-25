<?php
session_start();
require('../include/database.php');
require_once __DIR__ . '/../path.php';
require('../include/check_timeout.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('location:' . magasin_game . '?error=' . urlencode('Jeu introuvable !'));
    exit;
}

$id_jeu = (int) $_GET['id'];

$stmt = $bdd->prepare("SELECT * FROM jeu WHERE id_jeu = ?");
$stmt->execute([$id_jeu]);
$jeu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$jeu) {
    header('location:' . magasin_game . '?error=' . urlencode('Jeu introuvable !'));
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = "Détail du jeu";
$pageCategory = 'magasin';
echo "<script>const pageCategory = '$pageCategory';</script>";
include('../include/head.php');
?>

<body>
    <?php include('../include/header.php'); ?>

    <div id="alert-container" class="container mt-3"></div>

    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-lg border-0">
                    <div class="row g-0">
                        <div class="col-md-5">
                            <?php if (!empty($jeu['image'])): ?>
                                <img src="../back-office/uploads/<?= htmlspecialchars($jeu['image']) ?>"
                                    class="img-fluid rounded-start h-100"
                                    alt="<?= htmlspecialchars($jeu['nom']) ?>"
                                    style="object-fit: cover; min-height: 400px;">
                            <?php else: ?>
                                <img src="/magasin/img/no_image.png"
                                    class="img-fluid rounded-start h-100"
                                    alt="Aucune image disponible"
                                    style="object-fit: cover; min-height: 400px;">
                            <?php endif; ?>
                        </div>

                        <div class="col-md-7">
                            <div class="card-body h-100 d-flex flex-column p-4">
                                <h1 class="card-title mb-3 text-primary fw-bold">
                                    <?= htmlspecialchars($jeu['nom']) ?>
                                </h1>

                                <!-- Prix -->
                                <div class="mb-3">
                                    <span class="badge bg-success fs-5 px-3 py-2">
                                        <?= ($jeu['prix'] != 0 ? htmlspecialchars($jeu['prix']) . '€' : 'Gratuit') ?>
                                    </span>
                                </div>

                                <!-- Game Info Card -->
                                <div class="row mb-3">
                                    <div class="col-4">
                                        <div class="text-center p-2 bg-light rounded">
                                            <small class="text-muted d-block">Plateforme</small>
                                            <strong><?= htmlspecialchars($jeu['plateforme']) ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-center p-2 bg-light rounded">
                                            <small class="text-muted d-block">Type</small>
                                            <strong><?= htmlspecialchars($jeu['type']) ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-center p-2 bg-light rounded">
                                            <small class="text-muted d-block">Catégorie</small>
                                            <strong><?= htmlspecialchars($jeu['catégorie']) ?></strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <p class="mb-1"><i class="bi bi-building"></i> <strong>Éditeur:</strong></p>
                                        <p class="text-muted"><?= htmlspecialchars($jeu['éditeur']) ?></p>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-1"><i class="bi bi-calendar"></i> <strong>Date de sortie:</strong></p>
                                        <p class="text-muted"><?= date('d/m/Y', strtotime($jeu['date_sortie'])) ?></p>
                                    </div>
                                </div>

                                <!-- Note -->
                                <div class="mb-3">
                                    <p class="mb-1"><strong>Note:</strong></p>
                                    <div class="d-flex align-items-center">
                                        <?php
                                        $note = (float)$jeu['note_jeu'];
                                        for ($i = 1; $i <= 5; $i++):
                                            echo '<i class="bi bi-star-fill text-warning"></i>';
                                        ?>
                                        <?php endfor; ?>
                                        <span class="ms-2 text-muted">(<?= $note ?>/5)</span>
                                    </div>
                                </div>

                                <!-- Ajouter au panier -->
                                <div class="mt-auto">
                                    <button class="btn btn-primary btn-lg w-100 btn-add-to-cart"
                                        data-id="<?= $jeu['id_jeu'] ?>">
                                        <i class="bi bi-cart-plus me-2"></i>
                                        Ajouter au panier
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>


                    <?php if (!empty($jeu['description'])): ?>
                        <div class="card-footer bg-light">
                            <h5 class="mb-3"><i class="bi bi-info-circle"></i> Description</h5>
                            <p class="card-text mb-0">
                                <?= nl2br(htmlspecialchars($jeu['description'])) ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="text-center mt-4">
                    <a href="<?= magasin_main ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Retour au magasin
                    </a>
                </div>
            </div>
        </div>
    </main>

    <?php include('../include/footer.php'); ?>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll(".btn-add-to-cart").forEach(button => {
                button.addEventListener("click", async () => {
                    const gameId = button.getAttribute("data-id");
                    const originalText = button.innerHTML;

                    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Ajout...';
                    button.disabled = true;

                    try {
                        const response = await fetch(`../panier/add_to_cart.php?id=${gameId}`);
                        const data = await response.json();

                        const alertBox = document.createElement("div");
                        alertBox.className = `alert alert-dismissible fade show ${data.status === "success" ? "alert-success" : "alert-danger"}`;
                        alertBox.innerHTML = `
                            ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        `;

                        const alertContainer = document.getElementById("alert-container");
                        alertContainer.appendChild(alertBox);

                        setTimeout(() => {
                            if (alertBox.parentNode) {
                                alertBox.remove();
                            }
                        }, 5000);

                        if (data.panierCount !== undefined) {
                            updatePanierBadge(data.panierCount);
                        }
                    } catch (error) {
                        console.error("Erreur lors de l'ajout au panier :", error);

                        const alertBox = document.createElement("div");
                        alertBox.className = "alert alert-danger alert-dismissible fade show";
                        alertBox.innerHTML = `
                            Une erreur est survenue. Veuillez réessayer.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        `;

                        const alertContainer = document.getElementById("alert-container");
                        alertContainer.appendChild(alertBox);
                    } finally {
                        // Reset button
                        button.innerHTML = originalText;
                        button.disabled = false;
                    }
                });
            });
        });

        function updatePanierBadge(count) {
            const badge = document.querySelector(".panier-badge");
            if (badge) {
                if (count > 0) {
                    badge.textContent = count;
                    badge.style.display = 'inline';
                } else {
                    badge.style.display = 'none';
                }
            }
        }
    </script>
</body>

</html>