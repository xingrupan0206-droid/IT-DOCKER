<?php

/**
 * Controller voor het inloggen van gebruikers.
 * Haalt gegevens op via het Inloggen model dat de genormaliseerde
 * tabellen Gebruiker, Rol en Contact bevraagt.
 */
class Inlogcontroller extends BaseController
{
    /** @var Inloggen Het inlog-model */
    private $model;

    /**
     * Constructor: laadt het Inloggen model.
     */
    public function __construct()
    {
        $this->model = $this->model('Inloggen');
    }

    /**
     * Verwerkt het inlogformulier en stelt de sessie in bij succes.
     * Bij een geldige login worden de volgende sessievariabelen gezet:
     * - gebruiker_id: het Gebruiker.Id
     * - naam: de voornaam van de gebruiker
     * - rol: de rolnaam (Bezoeker, Medewerker, Administrator)
     * - bezoeker_id: het Bezoeker.Id (indien van toepassing)
     * - medewerker_id: het Medewerker.Id (indien van toepassing)
     */
    public function inloggen()
    {
        $fout = '';

        if (isset($_POST['login'])) {
            $email      = trim($_POST['email']);
            $wachtwoord = $_POST['wachtwoord'];

            // Zoek het account op via e-mailadres
            $account = $this->model->getAccountByEmail($email);

            // Controleer of het account bestaat en het wachtwoord klopt
            if ($account && $wachtwoord === $account->Wachtwoord) {
                // Stel sessievariabelen in
                $_SESSION['gebruiker_id'] = $account->Id;
                $_SESSION['account_id']   = $account->Id;       // Backwards compatibel
                $_SESSION['naam']         = $account->Voornaam;
                $_SESSION['rol']          = strtolower($account->rol);

                // Haal bezoeker- of medewerker-ID op voor ticket/admin operaties
                $bezoekerId = $this->model->getBezoekerId($account->Id);
                if ($bezoekerId) {
                    $_SESSION['bezoeker_id'] = $bezoekerId;
                }
                $medewerkerId = $this->model->getMedewerkerId($account->Id);
                if ($medewerkerId) {
                    $_SESSION['medewerker_id'] = $medewerkerId;
                }

                // Stuur de gebruiker naar de homepagina
                header('Location: ' . URLROOT);
                exit();
            } else {
                $fout = 'E-mailadres of wachtwoord is onjuist.';
            }
        }

        $this->view('inloggen/inloggen', ['fout' => $fout]);
    }
}