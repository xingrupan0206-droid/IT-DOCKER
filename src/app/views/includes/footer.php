<!-- Footer: hier staan de laatste navigatielinks en extra informatie. -->
<footer class="site-footer" id="contact">
    <div class="container footer-grid">
        <div>
            <h2 class="footer-title">Theater Aurora</h2>
            <p>Onvergetelijke theaterervaring in het hart van de stad. Boek uw kaartjes snel en veilig online.</p>
            <div class="footer-social-row" aria-label="Social media links">
                <a href="#" class="social-link" aria-label="Facebook">
                    <span class="social-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9h3V5h-3c-2.2 0-4 1.8-4 4v2H7v4h3v8h4v-8h3l1-4h-4V9c0-.6.4-1 1-1z"/></svg>
                    </span>
                </a>
                <a href="#" class="social-link" aria-label="Instagram">
                    <span class="social-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17" cy="7" r="1"/></svg>
                    </span>
                </a>
                <a href="#" class="social-link" aria-label="Twitter">
                    <span class="social-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4l16 16M20 4L4 20"/></svg>
                    </span>
                </a>
            </div>
        </div>

        <div>
            <h3 class="footer-subtitle">Navigatie</h3>
            <div class="footer-links">
                <a href="<?= URLROOT ?>/">Home</a>
                <a href="<?= URLROOT ?>/?url=voorstellingen/index">Voorstellingen</a>
                <a href="<?= URLROOT ?>/?url=ticketcontroller/overzicht">Tickets</a>
                <a href="<?= URLROOT ?>/#over-ons">Over Ons</a>
                <a href="#contact">Contact</a>
            </div>
        </div>

        <div>
            <h3 class="footer-subtitle">Social</h3>
            <div class="footer-links">
                <a href="#">Facebook</a>
                <a href="#">Instagram</a>
                <a href="#">X</a>
            </div>
        </div>

        <div>
            <h3 class="footer-subtitle">Juridisch</h3>
            <div class="footer-links">
                <a href="#">Privacybeleid</a>
                <a href="#">Voorwaarden</a>
            </div>
        </div>
    </div>

    <div class="container footer-bottom">
        <p>&copy; <span data-current-year><?= date('Y'); ?></span> Theater Aurora. Alle rechten voorbehouden.</p>
    </div>
</footer>

<!-- Kleine scriptlaag voor menu en jaartal; verder zo licht mogelijk gehouden. -->
<script src="<?= URLROOT; ?>/js/main.js"></script>

</body>
</html>