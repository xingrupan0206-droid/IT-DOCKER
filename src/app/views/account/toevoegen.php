<?php
/**
 * Nieuw account toevoegen pagina.
 * Toont een formulier in login-stijl voor het aanmaken van een account.
 * Alleen toegankelijk voor administrators.
 */
require_once APPROOT . '/views/includes/header.php'; ?>

<div class="wrapper">
    <div class="container">
        <div class="login-container">

            <h2>Nieuw Account</h2>
            <p class="login-subtitle">Voeg een nieuw account toe</p>

            <div class="login-box">

                <!-- Foutmelding: validatie, email bestaat, of databasefout -->
                <?php if (!empty($fout)): ?>
                    <p class="error"><?= htmlspecialchars($fout) ?></p>
                <?php endif; ?>

                <!-- Formulier wordt naar dezelfde URL gestuurd (POST) -->
                <form method="POST" action="<?= URLROOT ?>/?url=account/toevoegen">

                    <!-- Verplicht: wordt opgeslagen in Gebruiker.Voornaam -->
                    <label>Voornaam <span class="verplicht">*</span></label>
                    <input
                        type="text"
                        name="voornaam"
                        placeholder="Voer voornaam in"
                        value="<?= htmlspecialchars($_POST['voornaam'] ?? '') ?>"
                        required
                    >

                    <!-- Optioneel: bijv. de, van, van der -->
                    <label>Tussenvoegsel</label>
                    <input
                        type="text"
                        name="tussenvoegsel"
                        placeholder="Bijv. de, van, van de"
                        value="<?= htmlspecialchars($_POST['tussenvoegsel'] ?? '') ?>"
                    >

                    <!-- Verplicht: wordt opgeslagen in Gebruiker.Achternaam -->
                    <label>Achternaam <span class="verplicht">*</span></label>
                    <input
                        type="text"
                        name="achternaam"
                        placeholder="Voer achternaam in"
                        value="<?= htmlspecialchars($_POST['achternaam'] ?? '') ?>"
                        required
                    >

                    <!-- Verplicht:  wordt opgeslagen in Contact.Email, gecontroleerd op duplicaten -->
                    <label>E-mailadres <span class="verplicht">*</span></label>
                    <input
                        type="email"
                        name="email"
                        placeholder="Voer e-mailadres in"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        required
                    >

                    <!-- Verplicht: wordt opgeslagen in Gebruiker.Wachtwoord -->
                    <label>Wachtwoord <span class="verplicht">*</span></label>
                    <input
                        type="password"
                        name="wachtwoord"
                        placeholder="Voer wachtwoord in"
                        required
                    >

                    <!--
                        Verplicht: bepaalt de rechten.
                        Bezoeker = tickets kopen, Medewerker = beheer,
                        Administrator = volledige toegang.
                    -->
                    <label>Rol <span class="verplicht">*</span></label>
                    <select name="rol" required>
                        <option value="">Kies een rol</option>
                        <option value="Bezoeker" <?= (isset($_POST['rol']) && $_POST['rol'] === 'Bezoeker') ? 'selected' : '' ?>>Bezoeker</option>
                        <option value="Medewerker" <?= (isset($_POST['rol']) && $_POST['rol'] === 'Medewerker') ? 'selected' : '' ?>>Medewerker</option>
                        <option value="Administrator" <?= (isset($_POST['rol']) && $_POST['rol'] === 'Administrator') ? 'selected' : '' ?>>Administrator</option>
                    </select>

                    <button type="submit" name="toevoegen">Account Aanmaken</button>

                    <div class="login-divider"></div>
                    <span class="register-link"><a href="<?= URLROOT ?>/?url=account/overzicht">Terug naar overzicht</a></span>

                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>
