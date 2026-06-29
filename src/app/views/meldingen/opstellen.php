<?php
/**
 * Formulier voor het opstellen van een nieuwe melding.
 * Happy: melding wordt opgeslagen.
 * Unhappy: opslaan veroorzaakt expres een echte databasefout.
 */
?>
<?php require_once APPROOT . '/views/includes/header.php'; ?>

<main class="container">
    <section class="overview-container">
        <div class="overview-header">
            <p class="overview-label">Project Aurora</p>
            <h1>Nieuwe Melding Opstellen</h1>
            <p>Schrijf een melding en verstuur deze naar de juiste doelgroep.</p>
        </div>

        <div class="scenario-message <?= $scenario === 'unhappy' ? 'scenario-error' : 'scenario-success'; ?>">
            <strong><?= $scenario === 'unhappy' ? 'Unhappy scenario actief' : 'Happy scenario actief'; ?></strong>
            <span>
                <?= $scenario === 'unhappy'
                    ? 'Bij versturen wordt expres een niet-bestaande databasetabel gebruikt.'
                    : 'Bij versturen wordt de melding normaal opgeslagen.'; ?>
            </span>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Formulier waarmee de beheerder een nieuwe melding opstelt. -->
        <form class="overview-form" method="POST" action="<?= URLROOT ?>/?url=meldingen/opstellen&scenario=<?= htmlspecialchars($scenario); ?>">
            <div class="form-group">
                <label for="titel">Titel</label>
                <input type="text" id="titel" name="titel" value="Belangrijke update" required>
            </div>

            <div class="form-group">
                <label for="bericht">Bericht</label>
                <textarea id="bericht" name="bericht" rows="5" required>De planning voor de voorstelling is bijgewerkt.</textarea>
            </div>

            <div class="form-group">
                <label for="doelgroep">Doelgroep</label>
                <select id="doelgroep" name="doelgroep" required>
                    <option value="Notificatie">Medewerkers en gebruikers</option>
                    <option value="Review">Gebruikers</option>
                    <option value="Klacht">Medewerkers</option>
                </select>
            </div>

            <button class="btn btn-primary" type="submit">Versturen</button>
        </form>

        <!-- Knoppen rechtsonder om snel happy of unhappy te tonen. -->
        <div class="scenario-controls" aria-label="Scenario kiezen">
            <a class="btn <?= $scenario === 'happy' ? 'btn-primary' : 'btn-secondary'; ?>"
               href="<?= URLROOT ?>/?url=meldingen/opstellen&scenario=happy">
                Toon Happy
            </a>
            <a class="btn <?= $scenario === 'unhappy' ? 'btn-danger' : 'btn-secondary'; ?>"
               href="<?= URLROOT ?>/?url=meldingen/opstellen&scenario=unhappy">
                Toon Unhappy
            </a>
        </div>
    </section>
</main>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>
