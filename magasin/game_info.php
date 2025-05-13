<?php
session_start();
require('../include/database.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID de jeu invalide.";
    exit();
}

$id_jeu = (int) $_GET['id'];

$stmt = $bdd->prepare("SELECT * FROM jeu WHERE id_jeu = ?");
$stmt->execute([$id_jeu]);
$jeu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$jeu) {
    echo "Jeu introuvable.";
    exit();
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
    <div id="alert-container"></div>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <?php if (!empty($jeu['image'])): ?>
                        <img src="../back-office/uploads/<?= htmlspecialchars($jeu['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($jeu['nom']) ?>" style="height: 400px; object-fit: cover;">
                    <?php else: ?>
                        <img src="../../assets/img/no_image.png" class="card-img-top" alt="Aucune image disponible" style="height: 400px; object-fit: cover;">
                    <?php endif; ?>

                    <div class="card-body">
                        <h2 class="card-title"><?= htmlspecialchars($jeu['nom']) ?></h2>
                        <p class="card-text"><strong>Prix :</strong> <?= htmlspecialchars($jeu['prix']) ?> €</p>
                        <p class="card-text"><strong>Description :</strong><br> <?= nl2br(htmlspecialchars($jeu['description'] ?? "Aucune description disponible.")) ?></p>
                        <button class="btn btn-success mt-3 btn-add-to-cart" data-id="<?= $jeu['id_jeu'] ?>">Acheter ce jeu</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
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