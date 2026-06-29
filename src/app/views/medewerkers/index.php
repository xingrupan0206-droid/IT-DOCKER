<?php
/**
 * Medewerker-overzicht view.
 *
 * Happy Scenario: actieve medewerkers worden correct weergegeven.
 * Unhappy Scenario: bij een databasefout wordt een duidelijke foutmelding getoond.
 */
?>
<?php require_once APPROOT . '/views/includes/header.php'; ?>

<main class="container">
    <section class="overview-container">
        <div class="overview-header">
            <p class="overview-label">Project Aurora</p>
            <h1>Overzicht Medewerkers</h1>
            <p>Bekijk alle actieve medewerkers en test het happy of unhappy scenario.</p>
        </div>

        <!-- Knoppen waarmee je tijdens de presentatie tussen happy en unhappy wisselt. -->
        <div class="scenario-controls" aria-label="Scenario kiezen">
            <a class="btn <?= $scenario === 'happy' ? 'btn-primary' : 'btn-secondary'; ?>"
               href="<?= URLROOT ?>/?url=medewerkers/index&scenario=happy">
                Happy
            </a>
            <a class="btn <?= $scenario === 'unhappy' ? 'btn-danger' : 'btn-secondary'; ?>"
               href="<?= URLROOT ?>/?url=medewerkers/index&scenario=unhappy">
                Unhappy
            </a>
        </div>

        <div class="scenario-message <?= $scenario === 'unhappy' ? 'scenario-error' : 'scenario-success'; ?>">
            <strong><?= $scenario === 'unhappy' ? 'Unhappy scenario actief' : 'Happy scenario actief'; ?></strong>
            <span>
                <?= $scenario === 'unhappy'
                    ? 'Er wordt expres een databasefout veroorzaakt met een niet-bestaande tabel.'
                    : 'De database wordt normaal geladen en de medewerkers worden getoond.'; ?>
            </span>
        </div>

        <div class="overview-actions">
            <a class="btn btn-primary" href="<?= URLROOT ?>/?url=medewerkers/toevoegen">
                Nieuwe medewerker toevoegen
            </a>
        </div>

        <!-- Toont eerst fouten, daarna lege staat, en anders de medewerkerstabel. -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <strong>Fout bij laden:</strong> <?= htmlspecialchars($error); ?>
            </div>
        <?php elseif (empty($medewerkers)): ?>
            <div class="overview-empty">Geen medewerkers gevonden.</div>
        <?php else: ?>
            <div class="table-box">
                <div class="table-title">Medewerkers</div>
                <table class="account-table">
                    <thead>
                    <tr>
                        <th>Nummer</th>
                        <th>Naam</th>
                        <th>Soort</th>
                        <th>Email</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($medewerkers as $medewerker): ?>
                        <?php
                        // Bouwt de volledige naam netjes op, ook als er geen tussenvoegsel is.
                        $naam = trim(implode(' ', array_filter([
                            $medewerker->Voornaam,
                            $medewerker->Tussenvoegsel,
                            $medewerker->Achternaam
                        ])));
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($medewerker->Nummer); ?></td>
                            <td><?= htmlspecialchars($naam); ?></td>
                            <td><?= htmlspecialchars($medewerker->Medewerkersoort); ?></td>
                            <td><?= htmlspecialchars($medewerker->Email ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>
