<?php require_once APPROOT . '/views/includes/header.php'; ?>

<?php $scenario = (isset($_GET['scenario']) && $_GET['scenario'] === 'unhappy') ? 'unhappy' : 'happy'; ?>

    <<main class="container">
    <div class="form-header">
        <h1>Voorstelling bewerken</h1>
        <div class="form-actions-top">
            <a href="<?= URLROOT ?>/?url=voorstellingen/index" class="btn btn-secondary">Annuleren</a>
            <button type="submit" form="voorstellingForm" name="opslaan" class="btn btn-primary">Wijzigingen opslaan</button>
        </div>
    </div>

    <?php if (!empty($fout)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($fout) ?></div>
    <?php endif; ?>

    <form id="voorstellingForm" method="POST" action="<?= URLROOT ?>/?url=voorstellingen/wijzigen/<?= $voorstelling->Id ?>&scenario=<?= htmlspecialchars($scenario) ?>" class="form-grid">
        <div class="form-group">
            <label for="naam">Titel <span class="verplicht">*</span></label>
            <input type="text" id="naam" name="naam" value="<?= htmlspecialchars($_POST['naam'] ?? $voorstelling->Naam) ?>" required>
        </div>

        <div class="form-group">
            <label for="genre">Genre <span class="verplicht">*</span></label>
            <select id="genre" name="genre" required>
                <?php $g = $_POST['genre'] ?? $voorstelling->Genre; ?>
                <option value="">Selecteer genre</option>
                <option value="Drama" <?= $g === 'Drama' ? 'selected' : '' ?>>Drama</option>
                <option value="Komedie" <?= $g === 'Komedie' ? 'selected' : '' ?>>Komedie</option>
                <option value="Musical" <?= $g === 'Musical' ? 'selected' : '' ?>>Musical</option>
                <option value="Avontuur" <?= $g === 'Avontuur' ? 'selected' : '' ?>>Avontuur</option>
                <option value="Gala" <?= $g === 'Gala' ? 'selected' : '' ?>>Gala</option>
                <option value="Mysterie" <?= $g === 'Mysterie' ? 'selected' : '' ?>>Mysterie</option>
            </select>
        </div>

        <div class="form-group">
            <label for="datum">Datum <span class="verplicht">*</span></label>
            <input type="date" id="datum" name="datum" value="<?= htmlspecialchars($_POST['datum'] ?? $voorstelling->Datum) ?>" required>
        </div>

        <div class="form-group">
            <label for="tijd">Tijd <span class="verplicht">*</span></label>
            <input type="time" id="tijd" name="tijd" value="<?= htmlspecialchars($_POST['tijd'] ?? $voorstelling->Tijd) ?>" required>
        </div>

        <div class="form-group">
            <label for="zaal">Zaal <span class="verplicht">*</span></label>
            <select id="zaal" name="zaal" required>
                <?php $z = $_POST['zaal'] ?? $voorstelling->Zaal; ?>
                <option value="">Selecteer zaal</option>
                <option value="Grote Zaal" <?= $z === 'Grote Zaal' ? 'selected' : '' ?>>Grote Zaal</option>
                <option value="Kleine Zaal" <?= $z === 'Kleine Zaal' ? 'selected' : '' ?>>Kleine Zaal</option>
            </select>
        </div>

        <div class="form-group">
            <label for="prijs">Prijs (&euro;) <span class="verplicht">*</span></label>
            <input type="number" id="prijs" name="prijs" step="0.01" min="0" value="<?= htmlspecialchars($_POST['prijs'] ?? $voorstelling->Prijs) ?>" required>
        </div>

        <div class="form-group">
            <label for="maxtickets">Max. aantal tickets</label>
            <input type="number" id="maxtickets" name="maxtickets" min="1" value="<?= htmlspecialchars($_POST['maxtickets'] ?? $voorstelling->MaxAantalTickets) ?>" required>
        </div>

        <div class="form-group">
            <label for="beschikbaarheid">Beschikbaarheid</label>
            <select id="beschikbaarheid" name="beschikbaarheid">
                <?php $b = $_POST['beschikbaarheid'] ?? $voorstelling->Beschikbaarheid; ?>
                <option value="Ingepland" <?= $b === 'Ingepland' ? 'selected' : '' ?>>Ingepland</option>
                <option value="Uitverkocht" <?= $b === 'Uitverkocht' ? 'selected' : '' ?>>Uitverkocht</option>
                <option value="Geannuleerd" <?= $b === 'Geannuleerd' ? 'selected' : '' ?>>Geannuleerd</option>
            </select>
        </div>

        <div class="form-group form-group-full">
            <label for="beschrijving">Beschrijving</label>
            <textarea id="beschrijving" name="beschrijving" rows="4"><?= htmlspecialchars($_POST['beschrijving'] ?? $voorstelling->Beschrijving) ?></textarea>
        </div>

        <div class="form-group form-group-full">
            <label for="afbeelding">Afbeelding</label>
            <div class="file-upload">
                <input type="text" id="afbeelding" name="afbeelding" placeholder="Bestandsnaam (bijv. show-1.svg)" value="<?= htmlspecialchars($_POST['afbeelding'] ?? $voorstelling->Afbeelding) ?>">
            </div>
            <small class="help-text">Plaats afbeeldingen in public/img/ en vul hier de bestandsnaam in.</small>
        </div>
    </form>

    <div class="form-actions-bottom">
        <a href="<?= URLROOT ?>/?url=voorstellingen/index" class="btn btn-secondary">Annuleren</a>
        <button type="submit" form="voorstellingForm" name="opslaan" class="btn btn-primary">Wijzigingen opslaan</button>
    </div>

    <div style="margin-top: 16px; text-align: right;">
        <a href="<?= URLROOT ?>/?url=voorstellingen/verwijderen/<?= $voorstelling->Id ?>" class="btn btn-danger">Verwijderen</a>
    </div>
</main>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>