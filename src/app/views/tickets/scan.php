<?php require_once APPROOT . '/views/includes/header.php'; ?>

    <main class="container">
    <div class="scanner-container">
        <h1>Ticket Scanner</h1>
        <p class="scanner-subtitle">Scan een barcode om een ticket te valideren</p>

        <?php if (!empty($scanError)): ?>
            <div class="scanner-alert scanner-alert-error">
                <span class="scanner-alert-icon">&#9888;</span>
                <?= htmlspecialchars($scanError); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="scanner-alert scanner-alert-success">
                <span class="scanner-alert-icon">&#10003;</span>
                <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="scanner-box">
            <form method="POST" action="<?= URLROOT ?>/?url=ticketcontroller/scan" class="scanner-form">
                <div class="scanner-input-group">
                    <label for="barcode">Barcode</label>
                    <input id="barcode" name="barcode" type="text" required
                           value="<?= htmlspecialchars($_GET['barcode'] ?? ''); ?>"
                           placeholder="Scan of typ barcode..."
                           autofocus autocomplete="off">
                </div>
                <div class="scanner-buttons">
                    <button type="submit" class="btn btn-scan-zoek">
                        <span>&#128269;</span> Zoek ticket
                    </button>
                    <button type="submit" name="mark" value="1" class="btn btn-scan-mark">
                        <span>&#9989;</span> Markeer als gescand
                    </button>
                </div>
            </form>
        </div>

        <?php if ($scanResult): ?>
            <div class="scanner-result <?= $scanResult->Status === 'Bezet' ? 'scanner-used' : 'scanner-valid' ?>">
                <div class="scanner-result-header">
                    <h2>Ticket gevonden</h2>
                    <span class="scanner-status-badge">
                        <?= $scanResult->Status === 'Bezet' ? 'Al gescand' : 'Geldig' ?>
                    </span>
                </div>

                <div class="scanner-result-body">
                    <div class="scanner-info-row">
                        <span class="scanner-label">Voorstelling</span>
                        <span class="scanner-value"><?= htmlspecialchars($scanResult->voorstelling); ?></span>
                    </div>
                    <div class="scanner-info-row">
                        <span class="scanner-label">Datum</span>
                        <span class="scanner-value"><?= date('d-m-Y', strtotime($scanResult->Datum)); ?></span>
                    </div>
                    <div class="scanner-info-row">
                        <span class="scanner-label">Tijd</span>
                        <span class="scanner-value"><?= date('H:i', strtotime($scanResult->Tijd)); ?></span>
                    </div>
                    <div class="scanner-info-row">
                        <span class="scanner-label">Barcode</span>
                        <span class="scanner-value scanner-barcode"><?= htmlspecialchars($scanResult->Barcode); ?></span>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>