<?php
/**
 * Ticketoverzicht view — toont alle gereserveerde tickets van de ingelogde gebruiker.
 *
 * Happy Scenario: Alle tickets worden correct weergegeven met datum, tijd en barcode.
 *                 De gebruiker kan tickets eenvoudig bekijken of downloaden.
 *
 * Unhappy Scenario: Bij een databasefout wordt een duidelijke foutmelding getoond.
 *                   Tickets worden niet weergegeven en de barcode ontbreekt.
 */
?>
<?php require_once APPROOT . '/views/includes/header.php'; ?>

    <main class="container">
    <div class="tickets-container">

        <!-- Pagina titel en welkomstbericht -->
        <h2>Mijn tickets</h2>
        <p class="tickets-subtitle">Welkom, <?= htmlspecialchars($_SESSION['naam']) ?></p>

        <!-- Succesmelding na bijvoorbeeld een nieuwe reservering -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <!-- Foutmelding uit de sessie, bijv. bij mislukte reservering -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- Unhappy Scenario: databasefout bij het laden van tickets -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <strong>Fout bij laden:</strong> <?= htmlspecialchars($error) ?>
            </div>
            <div class="tickets-empty">
                <div class="tickets-empty-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="48" height="48">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M15 9l-6 6M9 9l6 6"/>
                    </svg>
                </div>
                <p><strong>Tickets kunnen niet worden geladen.</strong></p>
                <p>Er is een fout ontstaan bij het ophalen van uw ticketgegevens. De barcode is niet beschikbaar en uw tickets kunnen momenteel niet worden gebruikt bij de ingang van het theater.</p>
                <p>Probeer het later opnieuw of neem contact op met de klantenservice.</p>
            </div>

        <!-- Happy Scenario: geen tickets gevonden (maar geen fout) -->
        <?php elseif (empty($tickets)): ?>
            <div class="tickets-empty">
                <div class="tickets-empty-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="48" height="48">
                        <rect x="3" y="4" width="18" height="16" rx="3"/>
                        <path d="M7 8h10M7 12h6M7 16h8"/>
                    </svg>
                </div>
                <p>U heeft nog geen tickets gereserveerd.</p>
                <a href="<?= URLROOT ?>/?url=voorstellingen/index" class="btn btn-primary">Bekijk voorstellingen</a>
            </div>

        <!-- Happy Scenario: tickets correct weergeven met datum, tijd en barcode -->
        <?php else: ?>
            <div class="tickets-list">
                <?php foreach ($tickets as $ticket): ?>
                    <div class="ticket-card <?= $ticket->status === 'Bezet' ? 'ticket-used' : '' ?>">

                        <!-- Linker gedeelte: voorstelling naam, datum en tijd -->
                        <div class="ticket-main">
                            <h3><?= htmlspecialchars($ticket->voorstelling) ?></h3>
                            <div class="ticket-meta">
                                <span class="ticket-meta-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="14" height="14"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                                    <?= date('d-m-Y', strtotime($ticket->datum)) ?>
                                </span>
                                <span class="ticket-meta-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="14" height="14"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                    <?= date('H:i', strtotime($ticket->tijd)) ?>
                                </span>
                            </div>
                        </div>

                        <!-- Rechter gedeelte: status, barcode en acties -->
                        <div class="ticket-right">
                            <span class="ticket-status <?= $ticket->status === 'Bezet' ? 'status-used' : 'status-active' ?>">
                                <?= $ticket->status === 'Bezet' ? 'Gescand' : htmlspecialchars($ticket->status) ?>
                            </span>
                            <div class="visual-barcode" title="<?= htmlspecialchars($ticket->barcode) ?>">
                                <!-- Simpele SVG representatie van een barcode -->
                                <svg width="120" height="40" viewBox="0 0 120 40" preserveAspectRatio="none">
                                    <rect width="120" height="40" fill="white"/>
                                    <?php
                                    $hash = md5($ticket->barcode);
                                    $x = 5;
                                    for ($i = 0; $i < 32; $i++) {
                                        $val = hexdec($hash[$i]);
                                        $width = ($val % 3) + 1;
                                        $gap = (($val >> 2) % 2) + 1;
                                        echo '<rect x="'.$x.'" y="5" width="'.$width.'" height="30" fill="black"/>';
                                        $x += $width + $gap;
                                    }
                                    ?>
                                </svg>
                                <div class="ticket-barcode-text"><?= htmlspecialchars($ticket->barcode) ?></div>
                            </div>

                            <!-- Actieknoppen: bekijken en downloaden -->
                            <div class="ticket-actions">
                                <a href="<?= URLROOT ?>/?url=ticketcontroller/details/<?= $ticket->id ?>" class="btn btn-ticket-action" title="Ticket bekijken">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                    Bekijken
                                </a>
                                <a href="<?= URLROOT ?>/?url=ticketcontroller/download/<?= $ticket->id ?>" class="btn btn-ticket-action" title="Ticket downloaden">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                    Downloaden
                                </a>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>