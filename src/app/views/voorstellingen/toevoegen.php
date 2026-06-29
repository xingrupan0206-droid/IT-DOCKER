<?php require_once APPROOT . '/views/includes/header.php'; ?>

<?php $scenario = (isset($_GET['scenario']) && $_GET['scenario'] === 'unhappy') ? 'unhappy' : 'happy'; ?>

    <<main class="container">
    <div class="form-header">
        <h1>Nieuwe voorstelling toevoegen</h1>
        <div class="form-actions-top">
            <a href="<?= URLROOT ?>/?url=voorstellingen/index" class="btn btn-secondary">Annuleren</a>
            <button type="submit" form="voorstellingForm" name="opslaan" class="btn btn-primary">Opslaan</button>
        </div>
    </div>

    <?php if (!empty($fout)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($fout) ?></div>
    <?php endif; ?>

    <form id="voorstellingForm" method="POST" action="<?= URLROOT ?>/?url=voorstellingen/toevoegen&scenario=<?= htmlspecialchars($scenario) ?>" class="form-grid">
        <div class="form-group">
            <label for="naam">Titel <span class="verplicht">*</span></label>
            <input type="text" id="naam" name="naam" placeholder="Voer titel in" value="<?= htmlspecialchars($_POST['naam'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="genre">Genre <span class="verplicht">*</span></label>
            <select id="genre" name="genre" required>
                <option value="">Selecteer genre</option>
                <option value="Drama" <?= (($_POST['genre'] ?? '') === 'Drama') ? 'selected' : '' ?>>Drama</option>
                <option value="Komedie" <?= (($_POST['genre'] ?? '') === 'Komedie') ? 'selected' : '' ?>>Komedie</option>
                <option value="Musical" <?= (($_POST['genre'] ?? '') === 'Musical') ? 'selected' : '' ?>>Musical</option>
                <option value="Avontuur" <?= (($_POST['genre'] ?? '') === 'Avontuur') ? 'selected' : '' ?>>Avontuur</option>
                <option value="Gala" <?= (($_POST['genre'] ?? '') === 'Gala') ? 'selected' : '' ?>>Gala</option>
                <option value="Mysterie" <?= (($_POST['genre'] ?? '') === 'Mysterie') ? 'selected' : '' ?>>Mysterie</option>
            </select>
        </div>

        <div class="form-group">
            <label for="datum">Datum <span class="verplicht">*</span></label>
            <input type="date" id="datum" name="datum" value="<?= htmlspecialchars($_POST['datum'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="tijd">Tijd <span class="verplicht">*</span></label>
            <input type="time" id="tijd" name="tijd" value="<?= htmlspecialchars($_POST['tijd'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="zaal">Zaal <span class="verplicht">*</span></label>
            <select id="zaal" name="zaal" required>
                <option value="">Selecteer zaal</option>
                <option value="Grote Zaal" <?= (($_POST['zaal'] ?? '') === 'Grote Zaal') ? 'selected' : '' ?>>Grote Zaal</option>
                <option value="Kleine Zaal" <?= (($_POST['zaal'] ?? '') === 'Kleine Zaal') ? 'selected' : '' ?>>Kleine Zaal</option>
            </select>
        </div>

        <div class="form-group">
            <label for="prijs">Prijs (&euro;) <span class="verplicht">*</span></label>
            <input type="number" id="prijs" name="prijs" step="0.01" min="0" placeholder="0.00" value="<?= htmlspecialchars($_POST['prijs'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="maxtickets">Max. aantal tickets</label>
            <input type="number" id="maxtickets" name="maxtickets" min="1" value="<?= htmlspecialchars($_POST['maxtickets'] ?? '100') ?>" required>
        </div>

        <div class="form-group">
            <label for="beschikbaarheid">Beschikbaarheid</label>
            <select id="beschikbaarheid" name="beschikbaarheid">
                <option value="Ingepland" <?= (($_POST['beschikbaarheid'] ?? 'Ingepland') === 'Ingepland') ? 'selected' : '' ?>>Ingepland</option>
                <option value="Uitverkocht" <?= (($_POST['beschikbaarheid'] ?? '') === 'Uitverkocht') ? 'selected' : '' ?>>Uitverkocht</option>
                <option value="Geannuleerd" <?= (($_POST['beschikbaarheid'] ?? '') === 'Geannuleerd') ? 'selected' : '' ?>>Geannuleerd</option>
            </select>
        </div>

        <div class="form-group form-group-full">
            <label for="beschrijving">Beschrijving</label>
            <textarea id="beschrijving" name="beschrijving" rows="4" placeholder="Voer beschrijving in"><?= htmlspecialchars($_POST['beschrijving'] ?? '') ?></textarea>
        </div>

        <div class="form-group form-group-full">
            <label for="afbeelding">Afbeelding</label>
            <div class="file-upload">
                <input type="text" id="afbeelding" name="afbeelding" placeholder="Bestandsnaam (bijv. show-1.svg)" value="<?= htmlspecialchars($_POST['afbeelding'] ?? '') ?>">
            </div>
            <small class="help-text">Plaats afbeeldingen in public/img/ en vul hier de bestandsnaam in.</small>
        </div>
    </form>

    <div class="form-actions-bottom">
        <a href="<?= URLROOT ?>/?url=voorstellingen/index" class="btn btn-secondary">Annuleren</a>
        <button type="submit" form="voorstellingForm" name="opslaan" class="btn btn-primary">Opslaan</button>
    </div>
</main>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>