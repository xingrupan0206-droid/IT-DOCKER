<?php

class Registreercontroller extends BaseController {

    private $model;

    public function __construct() {
        $this->model = $this->model('Registreer');
    }

    public function registreer() {
        $fout = '';

        if (isset($_POST['registreer'])) {
            $voornaam      = trim($_POST['voornaam']);
            $tussenvoegsel = trim($_POST['tussenvoegsel']) ?: null;
            $achternaam    = trim($_POST['achternaam']);
            $email         = trim($_POST['email']);
            $wachtwoord    = $_POST['wachtwoord'];

            // Validatie
            if (empty($voornaam) || empty($achternaam) || empty($email) || empty($wachtwoord)) {
                $fout = 'Vul alle verplichte velden in.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $fout = 'Voer een geldig e-mailadres in.';
            } elseif ($this->model->emailBestaat($email)) {
                $fout = 'Dit e-mailadres is al in gebruik.';
            } else {
                $this->model->registreer($voornaam, $tussenvoegsel, $achternaam, $email, $wachtwoord);
                header('Location: ' . URLROOT . '/?url=inlogcontroller/inloggen&geregistreerd=1');
                exit();
            }
        }

        $this->view('registreer/registreer', ['fout' => $fout]);
    }
}
