<?php require_once APPROOT . '/views/includes/header.php'; ?>

<main class="container">
    <div class="form-header">
        <h1>Capaciteit Aanpassen</h1>
    </div>

    <div class="reserveren-card admin-toevoegen-card">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <h2><?= htmlspecialchars($voorstelling->Naam) ?></h2>
        
        <div class="reserveren-meta">
            <span><?= htmlspecialchars($voorstelling->Genre) ?></span>
            <span><?= date('d-m-Y', strtotime($voorstelling->Datum)) ?></span>
            <span><?= date('H:i', strtotime($voorstelling->Tijd)) ?></span>
            <span><?= htmlspecialchars($voorstelling->Zaal ?? 'Onbekend') ?></span>
        </div>
        
        <?php 
            $beschikbaar = (int)$voorstelling->beschikbaar; 
            $capaciteit  = (int)$voorstelling->MaxAantalTickets;
            $verkocht    = $capaciteit - $beschikbaar;
            $percentage  = $capaciteit > 0 ? ($verkocht / $capaciteit) * 100 : 100;
        ?>

        <div class="beschikbaarheid-indicator">
            <div class="beschikbaarheid-header">
                <strong>Huidige status:</strong>
                <span>
                    <?= $verkocht ?> van de <?= $capaciteit ?> plaatsen bezet
                </span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill <?= $beschikbaar === 0 ? 'progress-full' : '' ?>" style="width: <?= $percentage ?>%;"></div>
            </div>
        </div>

        <form method="POST" action="<?= URLROOT ?>/?url=ticketcontroller/toevoegen/<?= $voorstelling->Id ?>" class="admin-toevoegen-form">
            
            <div class="form-group">
                <label for="capaciteit">Nieuwe Totale Capaciteit:</label>
                <input type="number" name="capaciteit" id="capaciteit" class="form-control" 
                       min="<?= $verkocht ?>" 
                       value="<?= $capaciteit ?>" required>
                <small class="form-text">De capaciteit kan niet lager zijn dan het aantal reeds verkochte tickets (<?= $verkocht ?>).</small>
            </div>

            <div class="reserveren-actions mt-4">
                <a href="<?= URLROOT ?>/?url=ticketcontroller/dashboard" class="btn btn-cancel">Annuleren</a>
                <button type="submit" class="btn btn-primary">Capaciteit Opslaan</button>
            </div>
        </form>

    </div>
</main>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>
