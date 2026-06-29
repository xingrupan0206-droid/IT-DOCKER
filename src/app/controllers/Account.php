<?php
/**
 * Controller voor het account-beheer (overzicht + toevoegen).
 * Alleen medewerkers/administrators hebben toegang tot het overzicht,
 * alleen administrators mogen nieuwe accounts aanmaken.
 */
class Account extends BaseController
{
    /** @var AccountModel */
    private $model;

    public function __construct()
    {
        $this->model = $this->model('AccountModel');
    }

    /** Stuurt door naar overzicht. */
    public function index()
    {
        $this->overzicht();
    }

    /**
     * Toont een overzicht van alle actieve accounts,
     * gesplitst in Administrators, Medewerkers en Klanten.
     * Bij een databasefout wordt een foutmelding getoond.
     */
    public function overzicht()
    {
        // Alleen ingelogde medewerkers en administrators
        if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['medewerker', 'administrator'])) {
            header('Location: ' . URLROOT);
            exit();
        }

        $accounts = [];
        $error = '';

        try {
            $accounts = $this->model->getAll();
        } catch (PDOException $e) {
            $error = 'Geen verbinding met de database. Probeer het later opnieuw.';
        } catch (Exception $e) {
            $error = 'Er is een fout opgetreden bij het laden van accounts.';
        }

        $this->view('account/overzicht', [
            'accounts' => $accounts,
            'error' => $error
        ]);
    }

    /**
     * Toont een formulier om een nieuw account aan te maken.
     * Bij succes wordt de gebruiker doorgestuurd naar het overzicht.
     * Bij fout (geen verbinding, ongeldige input) blijft men op het formulier.
     */
    public function toevoegen()
    {
        // Alleen administrators mogen accounts aanmaken
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrator') {
            header('Location: ' . URLROOT);
            exit();
        }

        $fout = '';

        if (isset($_POST['toevoegen'])) {
            $voornaam      = trim($_POST['voornaam']);
            $tussenvoegsel = trim($_POST['tussenvoegsel']) ?: null;
            $achternaam    = trim($_POST['achternaam']);
            $email         = trim($_POST['email']);
            $wachtwoord    = $_POST['wachtwoord'];
            $rol           = $_POST['rol'];

            // Validatie van verplichte velden
            if (empty($voornaam) || empty($achternaam) || empty($email) || empty($wachtwoord)) {
                $fout = 'Vul alle verplichte velden in.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $fout = 'Voer een geldig e-mailadres in.';
            } else {
                try {
                    // Controleer of email al bestaat
                    if ($this->model->emailExists($email)) {
                        $fout = 'Dit e-mailadres is al in gebruik.';
                    } else {
                        // Account aanmaken en doorsturen naar overzicht
                        $this->model->create($voornaam, $tussenvoegsel, $achternaam, $email, $wachtwoord, $rol);
                        header('Location: ' . URLROOT . '/?url=account/overzicht&toegevoegd=1');
                        exit();
                    }
                } catch (PDOException $e) {
                    $fout = 'Geen verbinding met de database. Probeer het later opnieuw.';
                } catch (Exception $e) {
                    $fout = 'Er is een fout opgetreden bij het aanmaken van het account.';
                }
            }
        }

        $this->view('account/toevoegen', [
            'fout' => $fout
        ]);
    }
}
