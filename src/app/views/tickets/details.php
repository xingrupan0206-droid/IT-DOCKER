<?php require_once APPROOT . '/views/includes/header.php'; ?>

    <main class="container">
    <div class="ticket-detail-page">

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($ticket): ?>
            <div class="ticket-detail-card">
                <div class="ticket-detail-header">
                    <h1>Ticketdetails</h1>
                    <span class="ticket-status <?= $ticket->status === 'Bezet' ? 'status-used' : 'status-active' ?>">
                        <?= $ticket->status === 'Bezet' ? 'Gescand' : htmlspecialchars($ticket->status) ?>
                    </span>
                </div>

                <div class="ticket-detail-body">
                    <div class="detail-row">
                        <span class="detail-label">Voorstelling</span>
                        <span class="detail-value"><?= htmlspecialchars($ticket->voorstelling) ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Datum</span>
                        <span class="detail-value"><?= date('d-m-Y', strtotime($ticket->datum)) ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Tijd</span>
                        <span class="detail-value"><?= date('H:i', strtotime($ticket->tijd)) ?></span>
                    </div>
                    <div class="detail-row" style="flex-direction: column; align-items: flex-start; gap: 10px;">
                        <span class="detail-label">Barcode</span>
                        <div class="visual-barcode" title="<?= htmlspecialchars($ticket->barcode) ?>" style="margin-top: 5px;">
                            <svg width="200" height="60" viewBox="0 0 200 60" preserveAspectRatio="none">
                                <rect width="200" height="60" fill="white"/>
                                <?php
                                $hash = md5($ticket->barcode);
                                $x = 10;
                                for ($i = 0; $i < 32; $i++) {
                                    $val = hexdec($hash[$i]);
                                    $width = (($val % 3) + 1) * 2;
                                    $gap = ((($val >> 2) % 2) + 1) * 2;
                                    echo '<rect x="'.$x.'" y="10" width="'.$width.'" height="40" fill="black"/>';
                                    $x += $width + $gap;
                                }
                                ?>
                            </svg>
                            <div class="ticket-barcode-text" style="font-family: monospace; font-size: 1.1rem; letter-spacing: 0.1em; text-align: center; margin-top: 5px;"><?= htmlspecialchars($ticket->barcode) ?></div>
                        </div>
                    </div>
                </div>

                <div class="ticket-detail-actions">
                    <a href="<?= URLROOT ?>/?url=ticketcontroller/overzicht" class="btn btn-secondary">Terug naar overzicht</a>
                    <a href="<?= URLROOT ?>/?url=ticketcontroller/download/<?= $ticket->id ?>" class="btn btn-primary">Download ticket</a>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-error">Ticket niet gevonden.</div>
            <a href="<?= URLROOT ?>/?url=ticketcontroller/overzicht" class="btn btn-secondary">Terug naar overzicht</a>
        <?php endif; ?>

    </div>
</main>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>


