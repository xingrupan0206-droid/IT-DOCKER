<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theater Aurora</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;1,300&family=Jost:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= URLROOT ?>/css/header.css">
    <link rel="stylesheet" href="<?= URLROOT ?>/css/homepage.css">
    <link rel="stylesheet" href="<?= URLROOT ?>/css/footer.css">
    <link rel="stylesheet" href="<?= URLROOT ?>/css/login.css">
    <link rel="stylesheet" href="<?= URLROOT ?>/css/ticket.css">
    <link rel="stylesheet" href="<?= URLROOT ?>/css/scanner.css">
    <link rel="stylesheet" href="<?= URLROOT ?>/css/accountoverzicht.css">
    <link rel="stylesheet" href="<?= URLROOT ?>/css/voorstellingen.css">
</head>
<body>


<nav class="navbar">
    <div class="container">
        <a href="<?= URLROOT ?>" class="navbar-brand">Theater Aurora</a>

        <button class="nav-toggle" type="button" data-nav-toggle aria-expanded="false" aria-controls="primary-navigation" aria-label="Open menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <ul class="navbar-nav" id="primary-navigation" data-nav-menu>
            <li><a href="<?= URLROOT ?>/" class="nav-link">Home</a></li>

            <li class="nav-item nav-dropdown" data-dropdown>
                <button class="nav-link nav-dropdown-toggle" type="button" data-dropdown-toggle aria-expanded="false" aria-controls="overview-dropdown">
                    Beheer
                </button>
                <div class="nav-dropdown-menu" id="overview-dropdown" data-dropdown-menu>
                    <?php if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], ['administrator', 'medewerker'])): ?>
                        <a href="<?= URLROOT ?>/?url=account/overzicht">Account Overzicht</a>
                        <a href="<?= URLROOT ?>/?url=medewerkers/toevoegen">Nieuwe Medewerker</a>
                        <a href="<?= URLROOT ?>/?url=meldingen/opstellen">Nieuwe Melding</a>
                    <?php endif; ?>
                    <a href="<?= URLROOT ?>/?url=medewerkers/index">Medewerkers</a>
                    <a href="<?= URLROOT ?>/?url=meldingen/index">Meldingen</a>
                </div>
            </li>

            <li class="nav-item-voorstellingen"><a href="<?= URLROOT ?>/?url=voorstellingen/index" class="nav-link nav-link-voorstellingen" aria-label="Voorstellingen bekijken">Programma</a></li>

            <?php if (isset($_SESSION['account_id'])): ?>
                <li><a href="<?= URLROOT ?>/?url=ticketcontroller/overzicht" class="nav-link">Mijn tickets</a></li>
            <?php endif; ?>

            <?php if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], ['administrator', 'medewerker'])): ?>
                <li><a href="<?= URLROOT ?>/?url=ticketcontroller/dashboard" class="nav-link">Ticket Beheer</a></li>
            <?php endif; ?>

            <?php if (!isset($_SESSION['naam'])): ?>
                <li><a href="<?= URLROOT ?>/?url=inlogcontroller/inloggen" class="nav-link">Inloggen</a></li>
            <?php else: ?>
                <li><a href="<?= URLROOT ?>/?url=loguit/index" class="nav-link nav-logout-btn">Uitloggen</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
