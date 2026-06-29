<?php require_once APPROOT . '/views/includes/header.php'; ?>

<div class="wrapper">
    <div class="container">
        <div class="login-container">

            <h2>Inloggen</h2>
            <p class="login-subtitle">Log in op uw account</p>

            <div class="login-box">

                <?php if (isset($_GET['geregistreerd'])): ?>
                    <p class="success">Account aangemaakt! U kunt nu inloggen.</p>
                <?php endif; ?>

                <?php if (!empty($fout)): ?>
                    <p class="error"><?= htmlspecialchars($fout) ?></p>
                <?php endif; ?>

                <form method="POST" action="<?= URLROOT ?>/?url=inlogcontroller/inloggen">

                    <label>E-mailadres</label>
                    <input
                        type="email"
                        name="email"
                        placeholder="Voer uw e-mailadres in"
                        required
                    >

                    <label>Wachtwoord</label>
                    <input
                        type="password"
                        name="wachtwoord"
                        placeholder="Voer uw wachtwoord in"
                        required
                    >

                    <button type="submit" name="login">Inloggen</button>

                    <div class="login-divider"></div>
                    <span class="register-link">Nog geen account? <a href="<?= URLROOT ?>/?url=registreercontroller/registreer">Registreren</a></span>

                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>
