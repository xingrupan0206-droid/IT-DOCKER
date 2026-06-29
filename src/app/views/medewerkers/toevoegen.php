<?php
/**
 * Formulier voor het toevoegen van een nieuwe medewerker.
 * Happy: medewerker wordt opgeslagen.
 * Unhappy: opslaan veroorzaakt expres een echte databasefout.
 */
?>
<?php require_once APPROOT . '/views/includes/header.php'; ?>

<main class="container">
    <section class="overview-container">
        <div class="overview-header">
            <p class="overview-label">Project Aurora</p>
            <h1>Nieuwe Medewerker Toevoegen</h1>
            <p>Vul de gegevens in en sla de medewerker op in de database.</p>
        </div>

        <div class="scenario-message <?= $scenario === 'unhappy' ? 'scenario-error' : 'scenario-success'; ?>">
            <strong><?= $scenario === 'unhappy' ? 'Unhappy scenario actief' : 'Happy scenario actief'; ?></strong>
            <span>
                <?= $scenario === 'unhappy'
                    ? 'Bij opslaan wordt expres een niet-bestaande databasetabel gebruikt.'
                    : 'Bij opslaan wordt de medewerker normaal toegevoegd.'; ?>
            </span>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Formulier waarmee de beheerder een nieuwe medewerker toevoegt. -->
        <form class="overview-form" method="POST" action="<?= URLROOT ?>/?url=medewerkers/toevoegen&scenario=<?= htmlspecialchars($scenario); ?>">
            <div class="form-row">
                <div class="form-group">
                    <label for="voornaam">Voornaam</label>
                    <input type="text" id="voornaam" name="voornaam" value="Samira" required>
                </div>
                <div class="form-group">
                    <label for="tussenvoegsel">Tussenvoegsel</label>
                    <input type="text" id="tussenvoegsel" name="tussenvoegsel" placeholder="Optioneel">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="achternaam">Achternaam</label>
                    <input type="text" id="achternaam" name="achternaam" value="Bakker" required>
                </div>
                <div class="form-group">
                    <label for="functie">Functie</label>
                    <input type="text" id="functie" name="functie" value="Ticketcontroleur" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">E-mailadres</label>
                <input type="email" id="email" name="email" value="samira.bakker@aurora.nl" required>
            </div>

            <button class="btn btn-primary" type="submit">Opslaan</button>
        </form>

        <!-- Knoppen rechtsonder om snel happy of unhappy te tonen. -->
        <div class="scenario-controls" aria-label="Scenario kiezen">
            <a class="btn <?= $scenario === 'happy' ? 'btn-primary' : 'btn-secondary'; ?>"
               href="<?= URLROOT ?>/?url=medewerkers/toevoegen&scenario=happy">
                Toon Happy
            </a>
            <a class="btn <?= $scenario === 'unhappy' ? 'btn-danger' : 'btn-secondary'; ?>"
               href="<?= URLROOT ?>/?url=medewerkers/toevoegen&scenario=unhappy">
                Toon Unhappy
            </a>
        </div>
    </section>
</main>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>
