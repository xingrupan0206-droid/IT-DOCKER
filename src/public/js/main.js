document.addEventListener('DOMContentLoaded', () => {
    // Mobiel menu open/dicht zetten zonder extra bibliotheken.
    const navToggle = document.querySelector('[data-nav-toggle]');
    const navMenu = document.querySelector('[data-nav-menu]');

    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            const isOpen = navMenu.classList.toggle('is-open');
            navToggle.setAttribute('aria-expanded', String(isOpen));
        });
    }

    const dropdownToggles = document.querySelectorAll('[data-dropdown-toggle]');
    dropdownToggles.forEach((toggle) => {
        toggle.addEventListener('click', (event) => {
            event.stopPropagation();
            const dropdown = toggle.closest('[data-dropdown]');
            document.querySelectorAll('[data-dropdown].is-open').forEach((openDropdown) => {
                if (openDropdown !== dropdown) {
                    openDropdown.classList.remove('is-open');
                    const openToggle = openDropdown.querySelector('[data-dropdown-toggle]');
                    if (openToggle) {
                        openToggle.setAttribute('aria-expanded', 'false');
                    }
                }
            });
            const isOpen = dropdown.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', String(isOpen));
        });
    });

    document.addEventListener('click', (event) => {
        document.querySelectorAll('[data-dropdown].is-open').forEach((dropdown) => {
            if (!dropdown.contains(event.target)) {
                dropdown.classList.remove('is-open');
                const toggle = dropdown.querySelector('[data-dropdown-toggle]');
                if (toggle) {
                    toggle.setAttribute('aria-expanded', 'false');
                }
            }
        });
    });

    // Footerjaar automatisch vullen; scheelt handmatig bijwerken.
    const yearElement = document.querySelector('[data-current-year]');
    if (yearElement) {
        yearElement.textContent = new Date().getFullYear();
    }

    // Nieuwsbrief formulier afvangen
    const newsletterForm = document.getElementById('newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const input = newsletterForm.querySelector('input[type="email"]');
            if (input && input.value.trim() !== '') {
                alert('Bedankt voor uw aanmelding! U ontvangt binnenkort een bevestiging op ' + input.value.trim());
                input.value = '';
            } else {
                alert('Vul een geldig e-mailadres in.');
            }
        });
    }
});
