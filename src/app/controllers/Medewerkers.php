<?php

/**
 * Controller voor het medewerker-overzicht.
 * Happy: actieve medewerkers worden getoond.
 * Unhappy: er wordt bewust een databasefout gesimuleerd en netjes getoond.
 */
class Medewerkers extends BaseController
{
    private $model;

    public function __construct()
    {
        $this->model = null;
    }

    private function getModel()
    {
        // Model pas laden wanneer het nodig is.
        if ($this->model === null) {
            $this->model = $this->model('MedewerkerModel');
        }

        return $this->model;
    }

    public function index()
    {
        // Bepaalt of de pagina de happy of unhappy demo moet tonen.
        $scenario = (isset($_GET['scenario']) && $_GET['scenario'] === 'unhappy') ? 'unhappy' : 'happy';
        $medewerkers = [];
        $error = '';

        try {
            if ($scenario === 'unhappy') {
                // Unhappy: laat het model expres een databasefout veroorzaken.
                $this->getModel()->forceDatabaseError();
            } else {
                // Happy: haal de medewerkers normaal op uit de database.
                $medewerkers = $this->getModel()->getAll();
            }
        } catch (Exception $e) {
            // Technische fout niet tonen aan de gebruiker, alleen een nette melding.
            $error = 'De medewerkers konden niet worden geladen.';
        }

        // Stuurt alle data naar de view.
        $this->view('medewerkers/index', [
            'scenario' => $scenario,
            'medewerkers' => $medewerkers,
            'error' => $error
        ]);
    }

    public function toevoegen()
    {
        // Zelfde formulier, maar ander gedrag bij happy of unhappy.
        $scenario = (isset($_GET['scenario']) && $_GET['scenario'] === 'unhappy') ? 'unhappy' : 'happy';
        $success = '';
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Formulierwaarden netjes ophalen en spaties weghalen.
            $data = [
                'voornaam' => trim($_POST['voornaam'] ?? ''),
                'tussenvoegsel' => trim($_POST['tussenvoegsel'] ?? ''),
                'achternaam' => trim($_POST['achternaam'] ?? ''),
                'functie' => trim($_POST['functie'] ?? ''),
                'email' => trim($_POST['email'] ?? '')
            ];

            try {
                if ($scenario === 'unhappy') {
                    // Unhappy: opslaan naar een tabel die niet bestaat.
                    $this->getModel()->forceInsertDatabaseError($data);
                } else {
                    // Happy: medewerker echt opslaan in de database.
                    $this->getModel()->create($data);
                    $success = 'Medewerker succesvol toegevoegd.';
                }
            } catch (Exception $e) {
                // Als de database faalt, ziet de gebruiker deze foutmelding.
                $error = 'Medewerker kon niet worden toegevoegd. Probeer het later opnieuw.';
            }
        }

        // Formulier opnieuw tonen met succes- of foutmelding.
        $this->view('medewerkers/toevoegen', [
            'scenario' => $scenario,
            'success' => $success,
            'error' => $error
        ]);
    }
}
