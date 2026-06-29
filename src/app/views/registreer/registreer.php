<?php require_once APPROOT . '/views/includes/header.php'; ?>

<div class="wrapper">
    <div class="container">
        <div class="login-container">

            <h2>Registreren</h2>
            <p class="login-subtitle">Maak een nieuw account aan</p>

            <div class="login-box">

                <?php if (!empty($fout)): ?>
                    <p class="error"><?= htmlspecialchars($fout) ?></p>
                <?php endif; ?>

                <form method="POST" action="<?= URLROOT ?>/?url=registreercontroller/registreer">

                    <label>Voornaam <span class="verplicht">*</span></label>
                    <input
                        type="text"
                        name="voornaam"
                        placeholder="Voer uw voornaam in"
                        value="<?= htmlspecialchars($_POST['voornaam'] ?? '') ?>"
                        required
                    >

                    <label>Tussenvoegsel</label>
                    <input
                        type="text"
                        name="tussenvoegsel"
                        placeholder="Bijv. de, van, van de"
                        value="<?= htmlspecialchars($_POST['tussenvoegsel'] ?? '') ?>"
                    >

                    <label>Achternaam <span class="verplicht">*</span></label>
                    <input
                        type="text"
                        name="achternaam"
                        placeholder="Voer uw achternaam in"
                        value="<?= htmlspecialchars($_POST['achternaam'] ?? '') ?>"
                        required
                    >

                    <label>E-mailadres <span class="verplicht">*</span></label>
                    <input
                        type="email"
                        name="email"
                        placeholder="Voer uw e-mailadres in"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        required
                    >

                    <label>Wachtwoord <span class="verplicht">*</span></label>
                    <input
                        type="password"
                        name="wachtwoord"
                        placeholder="Voer uw wachtwoord in"
                        required
                    >

                    <button type="submit" name="registreer">Registreren</button>

                    <div class="login-divider"></div>
                    <span class="register-link">Al een account? <a href="<?= URLROOT ?>/?url=inlogcontroller/inloggen">Inloggen</a></span>

                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>
