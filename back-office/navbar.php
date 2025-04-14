<nav style="background-color: #f8f9fa; padding: 10px; border-bottom: 1px solid #ddd;">
    <button onclick="window.location.href = window.location.origin + '/back-office/<?= $page ?>'"
        style="padding: 10px 15px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
        ← Retour
    </button>

    <button onclick="window.location.href = window.location.origin + '/back-office/index.php'"
        style="margin-left: 15px; padding: 10px 15px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
        Accueil Back-Office
    </button>

    <div class="ms-auto">
        <button onclick="window.location.href = window.location.origin + '/index.php'"
            style="margin-left: 15px; padding: 10px 15px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Front Office
        </button>
    </div>
</nav>