<?php
session_start();
require('../include/database.php');

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
$title = "Détail du jeu - " . htmlspecialchars($jeu['nom']);
$pageCategory = 'magasin';
echo "<script>const pageCategory = '$pageCategory';</script>";
include('../include/head.php');
?>

<body>
    <?php include('../include/header.php'); ?>
    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <?php if (!empty($jeu['image'])): ?>
                        <img src="../back-office/uploads/<?= htmlspecialchars($jeu['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($jeu['nom']) ?>" style="height: 400px; object-fit: cover;">
                    <?php else: ?>
                        <!-- fixer img ici pareil que dans magasin_main -->
                        <img src="../../assets/img/no_image.png" class="card-img-top" alt="Aucune image disponible" style="height: 400px; object-fit: cover;">
                    <?php endif; ?>

                    <div class="card-body p-4">
                        <h2 class="card-title"><?= htmlspecialchars($jeu['nom']) ?></h2>
                        <p class="card-text"><strong>Prix :</strong> <?= htmlspecialchars($jeu['prix']) ?> €</p>
                        <p class="card-text"><strong>Description :</strong><br> <?= nl2br(htmlspecialchars($jeu['description'] ?? "Aucune description disponible.")) ?></p>
                        <button class="btn btn-success mt-3 btn-add-to-cart" data-id="<?= $jeu['id_jeu'] ?>">Ajouter au panier</button>
                    </div>
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

                    try {
                        const response = await fetch(`../panier/add_to_cart.php?id=${gameId}`);
                        const data = await response.json();

                        const alertBox = document.createElement("div");
                        alertBox.className = `alert mt-3 text-center alert-${data.status === "success" ? "success" : "danger"}`;
                        alertBox.textContent = data.message;

                        const alertContainer = document.getElementById("alert-container");
                        alertContainer.appendChild(alertBox);

                        setTimeout(() => alertBox.remove(), 5000);

                        if (data.panierCount !== undefined) {
                            updatePanierBadge(data.panierCount);
                        }
                    } catch (error) {
                        console.error("Erreur lors de l'ajout au panier :", error);
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