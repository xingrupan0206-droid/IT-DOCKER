<?php
/**
 * Meldingen-overzicht view.
 *
 * Happy Scenario: actieve meldingen worden correct weergegeven.
 * Unhappy Scenario: bij een databasefout wordt een duidelijke foutmelding getoond.
 */
?>
<?php require_once APPROOT . '/views/includes/header.php'; ?>

<main class="container">
    <section class="overview-container">
        <div class="overview-header">
            <p class="overview-label">Project Aurora</p>
            <h1>Overzicht Meldingen</h1>
            <p>Bekijk belangrijke meldingen en test het happy of unhappy scenario.</p>
        </div>

        <!-- Knoppen waarmee je tijdens de presentatie tussen happy en unhappy wisselt. -->
        <div class="scenario-controls" aria-label="Scenario kiezen">
            <a class="btn <?= $scenario === 'happy' ? 'btn-primary' : 'btn-secondary'; ?>"
               href="<?= URLROOT ?>/?url=meldingen/index&scenario=happy">
                Happy
            </a>
            <a class="btn <?= $scenario === 'unhappy' ? 'btn-danger' : 'btn-secondary'; ?>"
               href="<?= URLROOT ?>/?url=meldingen/index&scenario=unhappy">
                Unhappy
            </a>
        </div>

        <div class="scenario-message <?= $scenario === 'unhappy' ? 'scenario-error' : 'scenario-success'; ?>">
            <strong><?= $scenario === 'unhappy' ? 'Unhappy scenario actief' : 'Happy scenario actief'; ?></strong>
            <span>
                <?= $scenario === 'unhappy'
                    ? 'Er wordt expres een databasefout veroorzaakt met een niet-bestaande tabel.'
                    : 'De database wordt normaal geladen en de meldingen worden getoond.'; ?>
            </span>
        </div>

        <div class="overview-actions">
            <a class="btn btn-primary" href="<?= URLROOT ?>/?url=meldingen/opstellen">
                Nieuwe melding opstellen
            </a>
        </div>

        <!-- Toont eerst fouten, daarna lege staat, en anders de meldingentabel. -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <strong>Fout bij laden:</strong> <?= htmlspecialchars($error); ?>
            </div>
        <?php elseif (empty($meldingen)): ?>
            <div class="overview-empty">Geen meldingen gevonden.</div>
        <?php else: ?>
            <div class="table-box">
                <div class="table-title">Meldingen</div>
                <table class="account-table">
                    <thead>
                    <tr>
                        <th>Nummer</th>
                        <th>Type</th>
                        <th>Bericht</th>
                        <th>Datum</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($meldingen as $melding): ?>
                        <tr>
                            <td><?= htmlspecialchars($melding->Nummer); ?></td>
                            <td><?= htmlspecialchars($melding->Type); ?></td>
                            <td><?= htmlspecialchars($melding->Bericht); ?></td>
                            <td><?= htmlspecialchars($melding->DatumAangemaakt); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>
