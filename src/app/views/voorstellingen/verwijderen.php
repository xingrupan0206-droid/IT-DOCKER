<?php require_once APPROOT . '/views/includes/header.php'; ?>

    <main class="container">
    <div class="delete-modal">
        <div class="delete-icon">&#9888;</div>
        <h2>Voorstelling verwijderen</h2>
        <p>Weet u zeker dat u de voorstelling <strong>'<?= htmlspecialchars($voorstelling->Naam) ?>'</strong> wilt verwijderen?</p>
        <p class="delete-warning">Deze actie kan niet ongedaan worden gemaakt.</p>

        <form method="POST" action="<?= URLROOT ?>/?url=voorstellingen/verwijderen/<?= $voorstelling->Id ?>" class="delete-actions">
            <a href="<?= URLROOT ?>/?url=voorstellingen/index" class="btn btn-cancel">Annuleren</a>
            <button type="submit" class="btn btn-danger">Verwijderen</button>
        </form>
    </div>
</main>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>