<?php
/**
 * Account overzicht pagina.
 * Toont alle accounts gesplitst in Administrators, Medewerkers en Klanten.
 * Alleen toegankelijk voor medewerkers en administrators.
 */
require_once APPROOT . '/views/includes/header.php'; ?>

    <main class="container">
    <h1 class="page-title">Account Overzicht</h1>

    <!-- Succesmelding na toevoegen nieuw account (verdwijnt na 3 sec) -->
    <?php if (isset($_GET['toegevoegd'])): ?>
        <p class="success" id="toegevoegd-melding">Nieuw account is succesvol aangemaakt.</p>
        <script>
        setTimeout(function() {
            var m = document.getElementById('toegevoegd-melding');
            if (m) { m.style.display = 'none'; }
        }, 3000);
        </script>
    <?php endif; ?>

    <!-- Knop om nieuw account toe te voegen alleen zichtbaar als er data is -->
    <?php if (!empty($accounts)): ?>
        <div style="text-align:right; margin-bottom:12px;">
            <a href="<?= URLROOT ?>/?url=account/toevoegen" class="btn btn-primary">+ Nieuw account</a>
        </div>
    <?php endif; ?>

    <!-- Drie aparte tabellen per rol -->
    <?php
    $rollen = ['Administrator', 'Medewerker', 'Bezoeker'];
    $labels = [
        'Administrator' => 'Administrators',
        'Medewerker'    => 'Medewerkers',
        'Bezoeker'      => 'Klanten'
    ];
    foreach ($rollen as $rol):
        $filtered = array_filter($accounts ?? [], function ($a) use ($rol) {
            return $a->rol === $rol;
        });
    ?>
        <div class="table-box">
            <div class="table-title"><?= $labels[$rol] ?></div>
            <?php if (empty($filtered)): ?>
                <p style="padding:18px; color:rgba(255,255,255,0.5);">Geen <?= strtolower($labels[$rol]) ?> gevonden.</p>
            <?php else: ?>
            <table class="account-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Voornaam</th>
                    <th>Tussenvoegsel</th>
                    <th>Achternaam</th>
                    <th>Email</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($filtered as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a->account_id); ?></td>
                        <td><?= htmlspecialchars($a->voornaam); ?></td>
                        <td><?= htmlspecialchars($a->tussenvoegsel ?? ''); ?></td>
                        <td><?= htmlspecialchars($a->achternaam); ?></td>
                        <td><?= htmlspecialchars($a->email); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</main>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>