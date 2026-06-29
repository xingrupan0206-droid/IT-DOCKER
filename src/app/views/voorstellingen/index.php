<?php
/**
 * Voorstellingen overzicht view — toont alle beschikbare voorstellingen in een raster.
 *
 * Happy Scenario: Minimaal zes voorstellingen worden weergegeven met afbeelding, titel en datum.
 *                 De bezoeker kan op een voorstelling klikken voor meer informatie in een modal.
 *
 * Unhappy Scenario: Bij elke voorstelling wordt een placeholder weergegeven met de melding
 *                   "Deze voorstelling is momenteel niet beschikbaar."
 *                   De bezoeker kan geen voorstellingen bekijken of tickets reserveren.
 */
?>
<?php require_once APPROOT . '/views/includes/header.php'; ?>

    <main class="container">

        <!-- Paginakop met titel en eventueel admin-knop -->
        <div class="voorstellingen-header">
            <h1>Voorstellingen</h1>
            <?php if ($isAdmin): ?>
                <a href="<?= URLROOT ?>/?url=voorstellingen/toevoegen" class="btn btn-primary">+ Voorstelling toevoegen</a>
            <?php endif; ?>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Succesmelding na toevoegen/wijzigen/verwijderen -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- Zoek- en filterbalken -->
        <div class="filters">
            <input type="text" id="zoekVoorstelling" placeholder="Zoek voorstelling..." class="filter-search">
            <select class="filter-select" id="genreFilter">
                <option value="">Alle genres</option>
                <option value="Drama">Drama</option>
                <option value="Komedie">Komedie</option>
                <option value="Musical">Musical</option>
                <option value="Avontuur">Avontuur</option>
                <option value="Gala">Gala</option>
            </select>
        </div>

        <!-- UNHAPPY SCENARIO: Geen voorstellingen beschikbaar -->
        <?php if (empty($voorstellingen)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="64" height="64">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M15 9l-6 6M9 9l6 6"/>
                    </svg>
                </div>
                <p><strong>Deze voorstelling is momenteel niet beschikbaar.</strong></p>
                <p>Er zijn geen voorstellingen om te bekijken of tickets te reserveren.</p>
            </div>

            <!-- HAPPY SCENARIO: Voorstellingen in een raster weergeven -->
        <?php else: ?>
            <div class="show-grid" id="voorstellingenGrid">
                <?php foreach ($voorstellingen as $v): ?>
                    <!-- Elke voorstellingskaart bevat afbeelding, titel, datum en klikbare interactie -->
                    <article class="show-card"
                             data-id="<?= $v->Id ?>"
                             data-naam="<?= htmlspecialchars($v->Naam) ?>"
                             data-genre="<?= htmlspecialchars($v->Genre) ?>"
                             data-datum="<?= date('d-m-Y', strtotime($v->Datum)) ?>"
                             data-tijd="<?= date('H:i', strtotime($v->Tijd)) ?>"
                             data-zaal="<?= htmlspecialchars($v->Zaal ?? '') ?>"
                             data-prijs="<?= htmlspecialchars($v->Prijs) ?>"
                             data-beschrijving="<?= htmlspecialchars($v->Beschrijving ?? '') ?>"
                             data-status="<?= htmlspecialchars($v->Beschikbaarheid) ?>"
                             data-afbeelding="<?= htmlspecialchars($v->Afbeelding ?? '') ?>">

                        <!-- Afbeelding van de voorstelling -->
                        <div class="show-image-wrap">
                            <!-- Beschikbaarheidsbadge bovenop de afbeelding -->
                            <span class="show-status-badge show-status-<?= strtolower($v->Beschikbaarheid) ?>">
                            <?= htmlspecialchars($v->Beschikbaarheid) ?>
                        </span>

                            <?php if (!empty($v->Afbeelding)): ?>
                                <img src="<?= URLROOT ?>/img/<?= htmlspecialchars($v->Afbeelding) ?>"
                                     alt="<?= htmlspecialchars($v->Naam) ?>"
                                     class="show-image"
                                     onerror="this.parentElement.innerHTML='<div class=\'show-image-placeholder\'><svg viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'1.5\'><rect x=\'3\' y=\'3\' width=\'18\' height=\'18\' rx=\'2\'/><circle cx=\'8.5\' cy=\'8.5\' r=\'1.5\'/><path d=\'M21 15l-5-5L5 21\'/></svg></div>'">
                            <?php else: ?>
                                <!-- Placeholder als er geen afbeelding beschikbaar is -->
                                <div class="show-image-placeholder">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Kaartinhoud: titel, genre, datum -->
                        <div class="show-content">
                            <h3><?= htmlspecialchars($v->Naam) ?></h3>
                            <div class="show-meta-row">
                                <span><?= htmlspecialchars($v->Genre) ?></span>
                                <span><?= date('d-m-Y', strtotime($v->Datum)) ?></span>
                                <span><?= date('H:i', strtotime($v->Tijd)) ?></span>
                            </div>
                            <p><?= htmlspecialchars(mb_strimwidth($v->Beschrijving ?? '', 0, 120, '...')) ?></p>

                            <!-- Admin knoppen (alleen voor administrators) -->
                            <?php if ($isAdmin): ?>
                                <div class="admin-actions">
                                    <a href="<?= URLROOT ?>/?url=voorstellingen/wijzigen/<?= $v->Id ?>" class="btn btn-secondary btn-block">Wijzigen</a>
                                    <a href="<?= URLROOT ?>/?url=voorstellingen/verwijderen/<?= $v->Id ?>" class="btn btn-danger btn-block">Verwijderen</a>
                                </div>

                                <!-- Bezoeker knoppen -->
                            <?php else: ?>
                                <?php if (in_array($v->Id, $gereserveerd)): ?>
                                    <span class="btn btn-gereserveerd btn-block">Gereserveerd</span>
                                <?php else: ?>
                                    <!-- Klik op de kaart of "Meer info" knop om een vergroot overzicht te openen -->
                                    <button type="button" class="btn btn-secondary btn-block btn-meer-info">Meer info</button>
                                    <a href="<?= URLROOT ?>/?url=ticketcontroller/reserveren/<?= $v->Id ?>" class="btn btn-primary btn-block">Tickets reserveren</a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Modal: vergroot weergave met meer informatie over de geselecteerde voorstelling -->
        <div id="infoModal" class="modal-overlay" style="display: none;">
            <div class="modal-box">
                <button class="modal-close" type="button">&times;</button>
                <div id="modalImageWrap" class="modal-image-wrap"></div>
                <h2 id="modalTitle"></h2>
                <div class="modal-meta" id="modalMeta"></div>
                <div class="modal-desc" id="modalDesc"></div>
                <div class="modal-price" id="modalPrice"></div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary modal-close-btn">Sluiten</button>
                    <a href="#" id="modalReserveer" class="btn btn-primary">Tickets reserveren</a>
                </div>
            </div>
        </div>
    </main>

    <script>
        /**
         * JavaScript voor de voorstellingenpagina:
         * - Zoekfunctionaliteit op naam en genre
         * - Genre-filter dropdown
         * - Klik op kaart opent de modal met uitgebreide info
         * - Modal sluiten via knoppen of klikken buiten de modal
         */
        (function() {
            // === ZOEK- EN FILTERFUNCTIONALITEIT ===
            const zoekInput = document.getElementById('zoekVoorstelling');
            const genreFilter = document.getElementById('genreFilter');
            const cards = document.querySelectorAll('.show-card');

            /**
             * Filtert de kaarten op basis van de zoekterm en geselecteerd genre.
             */
            function filterCards() {
                const term = (zoekInput ? zoekInput.value : '').toLowerCase().trim();
                const genre = genreFilter ? genreFilter.value : '';
                cards.forEach(card => {
                    const naam = card.getAttribute('data-naam').toLowerCase();
                    const cardGenre = card.getAttribute('data-genre');
                    const matchZoek = naam.includes(term) || cardGenre.toLowerCase().includes(term);
                    const matchGenre = !genre || cardGenre === genre;
                    card.style.display = (matchZoek && matchGenre) ? '' : 'none';
                });
            }

            if (zoekInput) zoekInput.addEventListener('input', filterCards);
            if (genreFilter) genreFilter.addEventListener('change', filterCards);

            // === MODAL FUNCTIONALITEIT ===
            const modal = document.getElementById('infoModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalMeta = document.getElementById('modalMeta');
            const modalDesc = document.getElementById('modalDesc');
            const modalPrice = document.getElementById('modalPrice');
            const modalReserveer = document.getElementById('modalReserveer');
            const modalImageWrap = document.getElementById('modalImageWrap');

            /**
             * Sluit de modal en verwijdert de afbeelding.
             */
            function sluitModal() {
                modal.style.display = 'none';
                if (modalImageWrap) modalImageWrap.innerHTML = '';
            }

            /**
             * Opent de modal met uitgebreide informatie over de geklikte voorstelling.
             * De kaart wordt als het ware "vergroot" in een overlay.
             */
            function openModal(card) {
                const naam = card.getAttribute('data-naam');
                const genre = card.getAttribute('data-genre');
                const datum = card.getAttribute('data-datum');
                const tijd = card.getAttribute('data-tijd');
                const zaal = card.getAttribute('data-zaal');
                const prijs = card.getAttribute('data-prijs');
                const beschrijving = card.getAttribute('data-beschrijving');
                const status = card.getAttribute('data-status');
                const afbeelding = card.getAttribute('data-afbeelding');

                // Titel instellen
                modalTitle.textContent = naam;

                // Afbeelding in de modal tonen
                if (afbeelding && modalImageWrap) {
                    modalImageWrap.innerHTML = '<img src="<?= URLROOT ?>/img/' + afbeelding + '" alt="' + naam + '" class="modal-image">';
                } else if (modalImageWrap) {
                    modalImageWrap.innerHTML = '';
                }

                // Metadata: genre, datum, tijd, zaal, status
                modalMeta.innerHTML =
                    '<span>' + genre + '</span>' +
                    '<span>' + datum + '</span>' +
                    '<span>' + tijd + '</span>' +
                    '<span>' + zaal + '</span>' +
                    '<span class="modal-status modal-status-' + status.toLowerCase() + '">' + status + '</span>';

                // Beschrijving
                modalDesc.textContent = beschrijving || 'Geen beschrijving beschikbaar.';

                // Prijs
                modalPrice.innerHTML = '&euro; ' + parseFloat(prijs).toFixed(2);

                // Reserveringlink instellen
                if (card.querySelector('.btn-gereserveerd')) {
                    modalReserveer.style.display = 'none';
                    // We also show a disabled text
                    if (!document.getElementById('modalAlreadyReserved')) {
                        const span = document.createElement('span');
                        span.id = 'modalAlreadyReserved';
                        span.className = 'btn btn-gereserveerd';
                        span.textContent = 'Reeds Gereserveerd';
                        modalReserveer.parentNode.appendChild(span);
                    } else {
                        document.getElementById('modalAlreadyReserved').style.display = 'inline-block';
                    }
                } else {
                    modalReserveer.style.display = 'inline-block';
                    modalReserveer.href = '<?= URLROOT ?>/?url=ticketcontroller/reserveren/' + card.getAttribute('data-id');
                    if (document.getElementById('modalAlreadyReserved')) {
                        document.getElementById('modalAlreadyReserved').style.display = 'none';
                    }
                }

                // Modal tonen
                modal.style.display = 'flex';
            }

            // Klik op de "Meer info" knop opent de modal
            document.querySelectorAll('.btn-meer-info').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    openModal(this.closest('.show-card'));
                });
            });

            // Klik op de show-card zelf (afbeelding of titel) opent ook de modal
            cards.forEach(card => {
                card.addEventListener('click', function(e) {
                    // Voorkom dat links en knoppen de modal openen
                    if (e.target.closest('a') || e.target.closest('button')) return;
                    openModal(this);
                });
            });

            // Modal sluiten via knoppen
            document.querySelectorAll('.modal-close, .modal-close-btn').forEach(el => {
                el.addEventListener('click', sluitModal);
            });

            // Modal sluiten door buiten de modal te klikken
            modal.addEventListener('click', function(e) {
                if (e.target === modal) sluitModal();
            });

            // Modal sluiten met Escape-toets
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal.style.display === 'flex') sluitModal();
            });
        })();
    </script>

<?php require_once APPROOT . '/views/includes/footer.php'; ?> 
 
 
 
 
 
