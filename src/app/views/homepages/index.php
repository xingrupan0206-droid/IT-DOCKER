<?php require_once APPROOT . '/views/includes/header.php'; ?>

    <main id="home">
    <section class="hero">
        <div class="hero-background" aria-hidden="true">
            <img src="https://images.unsplash.com/photo-1777398410361-a1bf3e83e60e?w=1440&h=900&fit=crop&auto=format" alt="" class="hero-background-image">
        </div>

        <div class="hero-overlay" aria-hidden="true"></div>

        <div class="container hero-content-wrap">
            <div class="hero-content hero-content-centered">
                <p class="hero-kicker">Seizoen 2026</p>
                <h1>Welkom bij <span>Theater Aurora</span></h1>
                <p class="hero-text">
                    Reserveer eenvoudig tickets voor de nieuwste voorstellingen en beleef een onvergetelijke avond.
                </p>
                <div class="hero-actions hero-actions-centered">
                    <a class="btn btn-primary" href="#voorstellingen">Bekijk Voorstellingen</a>
                    <a class="btn btn-secondary" href="<?= URLROOT ?>/?url=voorstellingen/index">Tickets Reserveren</a>
                </div>
            </div>
        </div>

        <div class="scroll-pulse" aria-hidden="true">
            <div class="scroll-line"></div>
        </div>
    </section>

    <section class="section section-tight" id="voorstellingen">
        <div class="container">
            <div class="section-heading section-heading-centered">
                <p class="section-label">Agenda</p>
                <h2>Actuele Voorstellingen</h2>
                <p>Ontdek ons aanbod en kies een voorstelling die bij uw avond past.</p>
            </div>

            <?php if (empty($voorstellingen)): ?>
                <div class="empty-state">
                    <p><strong>Er zijn momenteel geen voorstellingen beschikbaar.</strong></p>
                    <p>Kom later terug voor nieuwe optredens.</p>
                </div>
            <?php else: ?>
                <div class="show-grid">
                    <?php
                    $count = 0;
                    foreach ($voorstellingen as $v):
                        if ($count >= 3) break;
                        $count++;
                        ?>
                        <article class="show-card">
                            <div class="show-image-wrap" style="position: relative;">
                                <span style="
                                        position: absolute;
                                        top: 10px;
                                        left: 10px;
                                        z-index: 10;
                                        background: <?= ($v->Beschikbaarheid === 'Uitverkocht') ? '#dc2626' : (($v->Beschikbaarheid === 'Geannuleerd') ? '#4b5563' : '#16a34a') ?>;
                                        color: #fff;
                                        font-size: 0.7rem;
                                        font-weight: 700;
                                        letter-spacing: 0.06em;
                                        text-transform: uppercase;
                                        padding: 5px 12px;
                                        border-radius: 4px;
                                        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
                                        pointer-events: none;
                                        ">
                                    <?= htmlspecialchars($v->Beschikbaarheid) ?>
                                </span>

                                <?php if (!empty($v->Afbeelding)): ?>
                                    <img src="<?= URLROOT ?>/img/<?= htmlspecialchars($v->Afbeelding) ?>" alt="<?= htmlspecialchars($v->Naam) ?>" class="show-image" onerror="this.style.display='none'">
                                <?php else: ?>
                                    <div class="show-image-placeholder">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="show-content">
                                <h3><?= htmlspecialchars($v->Naam) ?></h3>
                                <div class="show-meta-row">
                                    <span><?= htmlspecialchars($v->Genre) ?></span>
                                    <span><?= date('d-m-Y', strtotime($v->Datum)) ?></span>
                                    <span><?= date('H:i', strtotime($v->Tijd)) ?></span>
                                </div>
                                <p><?= htmlspecialchars(mb_strimwidth($v->Beschrijving ?? '', 0, 120, '...')) ?></p>

                                <?php if (isset($_SESSION['account_id']) && in_array($v->Id, $gereserveerd)): ?>
                                    <span class="btn btn-gereserveerd btn-block">Gereserveerd</span>
                                <?php else: ?>
                                    <a href="<?= URLROOT ?>/?url=ticketcontroller/reserveren/<?= $v->Id ?>" class="btn btn-primary btn-block">Tickets reserveren</a>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="section section-muted" id="tickets">
        <div class="container">
            <div class="section-heading section-heading-centered">
                <p class="section-label">Voordelen</p>
                <h2>Waarom Theater Aurora</h2>
                <p>Wij maken reserveren, toegang en beheer zo eenvoudig mogelijk.</p>
            </div>

            <div class="benefit-grid">
                <article class="benefit-card">
                    <span class="benefit-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l4 8 8 1-6 6 2 8-8-4-8 4 2-8-6-6 8-1z"/></svg>
                    </span>
                    <h3>Veilig online reserveren</h3>
                    <p>Uw gegevens worden veilig verwerkt tijdens het reserveren van tickets.</p>
                </article>

                <article class="benefit-card">
                    <span class="benefit-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="3"/><path d="M7 8h10M7 12h6M7 16h8"/></svg>
                    </span>
                    <h3>Eenvoudig ticketbeheer</h3>
                    <p>Tickets zijn overzichtelijk terug te vinden en later makkelijk te beheren.</p>
                </article>

                <article class="benefit-card">
                    <span class="benefit-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h6v10H4z"/><path d="M14 7h6v10h-6z"/><path d="M8 7V4m8 3V4M8 17v3m8-3v3"/></svg>
                    </span>
                    <h3>Snelle toegang via barcode-scanning</h3>
                    <p>Bezoekers kunnen snel worden gecontroleerd bij de ingang.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="section" id="over-ons">
        <div class="container">
            <div class="section-heading section-heading-centered">
                <p class="section-label">Testimonials</p>
                <h2>Wat Bezoekers Zeggen</h2>
                <p>Een paar korte reacties geven direct een goed beeld van het theater.</p>
            </div>

            <div class="review-grid">
                <article class="review-card">
                    <div class="review-stars">★★★★★</div>
                    <p>“Een betoverende avond. Het theater heeft een prachtige sfeer en alles voelde heel verzorgd aan.”</p>
                    <strong>Marieke van den Berg</strong>
                    <span>Vaste bezoeker</span>
                </article>

                <article class="review-card">
                    <div class="review-stars">★★★★★</div>
                    <p>“De zaal is mooi, de voorstelling was sterk en het reserveren ging straks vast heel soepel.”</p>
                    <strong>Thomas Akkerman</strong>
                    <span>Eerste bezoeker</span>
                </article>

                <article class="review-card">
                    <div class="review-stars">★★★★☆</div>
                    <p>“Een overzichtelijke website en een duidelijke eerste indruk. Precies wat een theater nodig heeft.”</p>
                    <strong>Sophie Hendriksen</strong>
                    <span>Abonnementhouder</span>
                </article>
            </div>
        </div>
    </section>

    <section class="newsletter-banner" id="login" aria-labelledby="newsletter-title">
        <div class="newsletter-background" aria-hidden="true">
            <img src="https://images.unsplash.com/photo-1775250869743-d08c72844f6e?w=1440&h=400&fit=crop&auto=format" alt="" class="newsletter-image">
        </div>
        <div class="newsletter-overlay" aria-hidden="true"></div>
        <div class="container newsletter-content">
            <p class="section-label">Nieuwsbrief</p>
            <h2 id="newsletter-title">Mis Geen Enkele Voorstelling</h2>
            <p>Schrijf u in voor updates over nieuwe voorstellingen, acties en theaternieuws.</p>
            <form class="newsletter-form" id="newsletter-form">
                <label class="sr-only" for="newsletter-email">E-mailadres</label>
                <input id="newsletter-email" type="email" placeholder="uw@emailadres.nl" required>
                <button type="submit" class="btn btn-primary">Aanmelden</button>
            </form>
        </div>
    </section>
</main>
<?php require_once APPROOT . '/views/includes/footer.php'; ?>