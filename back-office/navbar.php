<nav class="navbar container-fluid navbar-expand-md bg-light border-bottom px-3 py-2">
    <div class="container-fluid">

        <div class="d-flex">
            <button onclick="window.location.href = window.location.origin + '<?= $page ?>'"
                class="btn btn-primary me-2">
                ← Retour
            </button>

            <button onclick="window.location.href = window.location.origin + '<?= index_back ?>'"
                class="btn btn-primary">
                Accueil Back-Office
            </button>
        </div>

        <div class="ms-auto mt-2 mt-md-0">
            <button onclick="window.location.href = window.location.origin + '<?= index_front ?>'"
                class="btn btn-primary">
                Front Office
            </button>
        </div>
    </div>
</nav>