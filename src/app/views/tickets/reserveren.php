<?php require_once APPROOT . '/views/includes/header.php'; ?>

    <main class="container">
    <div class="form-header">
        <h1>Ticket reserveren</h1>
    </div>

    <div class="reserveren-card">
        <h2><?= htmlspecialchars($voorstelling->Naam) ?></h2>
        <div class="reserveren-meta">
            <span><?= htmlspecialchars($voorstelling->Genre) ?></span>
            <span><?= date('d-m-Y', strtotime($voorstelling->Datum)) ?></span>
            <span><?= date('H:i', strtotime($voorstelling->Tijd)) ?></span>
            <span><?= htmlspecialchars($voorstelling->Zaal ?? 'Onbekend') ?></span>
        </div>
        <p class="reserveren-prijs">&euro; <?= number_format($voorstelling->Prijs, 2) ?></p>
        <p class="reserveren-desc"><?= htmlspecialchars($voorstelling->Beschrijving ?? 'Geen beschrijving beschikbaar.') ?></p>

        <form method="POST" action="<?= URLROOT ?>/?url=ticketcontroller/reserveren/<?= $voorstelling->Id ?>">
            <div class="reserveren-actions">
                <a href="<?= URLROOT ?>/?url=voorstellingen/index" class="btn btn-cancel">Annuleren</a>
                <button type="submit" class="btn btn-primary">Bevestigen</button>
            </div>
        </form>
    </div>
</main>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>