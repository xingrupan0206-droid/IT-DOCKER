<?php require_once APPROOT . '/views/includes/header.php'; ?>

<main class="container">
    <div class="form-header dashboard-header">
        <h1>Ticket Beheer Dashboard</h1>
        <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('scanner-section').classList.toggle('d-none')">
            <i class="icon-barcode"></i> Ticket Scannen
        </button>
    </div>

    <div class="dashboard-subtitle-bar">
        <p class="dashboard-subtitle">Beheer tickets en bekijk de live capaciteit voor alle actieve voorstellingen.</p>
    </div>

    <!-- Scanner Section (Hidden by default unless there's a POST request) -->
    <div id="scanner-section" class="dashboard-card scanner-dashboard-card <?= $showScanner ? '' : 'd-none' ?>">
        <h2>Ticket Scanner</h2>
        
        <?php if (!empty($scanError)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($scanError); ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= URLROOT ?>/?url=ticketcontroller/dashboard" class="scanner-form-inline">
            <div class="form-group flex-1">
                <input id="barcode" name="barcode" type="text" class="form-control" required
                       value="<?= htmlspecialchars($_POST['barcode'] ?? ''); ?>"
                       placeholder="Scan of typ barcode..."
                       autofocus autocomplete="off">
            </div>
            <button type="submit" class="btn btn-primary">Zoek ticket</button>
            <button type="submit" name="mark" value="1" class="btn btn-success">Markeer als gescand</button>
        </form>

        <?php if ($scanResult): ?>
            <div class="scanner-result <?= $scanResult->Status === 'Bezet' ? 'scanner-used' : 'scanner-valid' ?> mt-4">
                <div class="scanner-result-header">
                    <h3>Ticket: <?= htmlspecialchars($scanResult->voorstelling); ?></h3>
                    <span class="status-badge <?= $scanResult->Status === 'Bezet' ? 'status-red' : 'status-green' ?>">
                        <?= $scanResult->Status === 'Bezet' ? 'Al gescand' : 'Geldig' ?>
                    </span>
                </div>
                <div class="dash-meta mt-2">
                    <span><?= date('d-m-Y', strtotime($scanResult->Datum)); ?></span>
                    <span>&middot;</span>
                    <span><?= date('H:i', strtotime($scanResult->Tijd)); ?></span>
                    <span>&middot;</span>
                    <span class="font-weight-bold"><?= htmlspecialchars($scanResult->Barcode); ?></span>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="dashboard-card main-list-card">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <?php if (empty($voorstellingen)): ?>
            <div class="empty-state">
                <p>Er zijn momenteel geen actieve voorstellingen gevonden.</p>
            </div>
        <?php else: ?>
            <div class="dash-list">
                <?php foreach ($voorstellingen as $v): 
                    $beschikbaar = (int)$v->beschikbaar;
                    $capaciteit  = (int)$v->MaxAantalTickets;
                    $percentage  = $capaciteit > 0 ? (($capaciteit - $beschikbaar) / $capaciteit) * 100 : 100;
                    
                    $statusClass = 'dash-status-green';
                    if ($beschikbaar === 0) {
                        $statusClass = 'dash-status-red';
                    } elseif ($beschikbaar < 10) {
                        $statusClass = 'dash-status-orange';
                    }
                ?>
                    <div class="dash-item">
                        <div class="dash-date">
                            <span class="dash-day"><?= date('d', strtotime($v->Datum)) ?></span>
                            <span class="dash-month"><?= date('M Y', strtotime($v->Datum)) ?></span>
                        </div>
                        
                        <div class="dash-info">
                            <h3 class="dash-title"><?= htmlspecialchars($v->Naam) ?></h3>
                            <div class="dash-meta">
                                <span><i class="icon-time"></i> <?= date('H:i', strtotime($v->Tijd)) ?></span>
                                <span>&middot;</span>
                                <span><?= htmlspecialchars($v->Zaal ?? 'Onbekende zaal') ?></span>
                                <span>&middot;</span>
                                <span><?= htmlspecialchars($v->Genre) ?></span>
                            </div>
                        </div>

                        <div class="dash-capacity">
                            <div class="dash-cap-text">
                                <span class="<?= $statusClass ?> font-weight-bold"><?= $beschikbaar ?></span> / <?= $capaciteit ?> vrij
                            </div>
                            <div class="dash-progress">
                                <div class="dash-progress-fill <?= $beschikbaar === 0 ? 'fill-red' : '' ?>" style="width: <?= $percentage ?>%;"></div>
                            </div>
                        </div>

                        <div class="dash-action">
                            <a href="<?= URLROOT ?>/?url=ticketcontroller/toevoegen/<?= $v->Id ?>" class="btn btn-outline-primary">Aanpassen</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>
