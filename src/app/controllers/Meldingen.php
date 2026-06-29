<?php

/**
 * Controller voor het meldingen-overzicht.
 * Happy: actieve meldingen worden getoond.
 * Unhappy: er wordt bewust een databasefout gesimuleerd en netjes getoond.
 */
class Meldingen extends BaseController
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
            $this->model = $this->model('MeldingModel');
        }

        return $this->model;
    }

    public function index()
    {
        // Kijkt of de gebruiker happy of unhappy wil tonen.
        $scenario = (isset($_GET['scenario']) && $_GET['scenario'] === 'unhappy') ? 'unhappy' : 'happy';
        $meldingen = [];
        $error = '';

        try {
            if ($scenario === 'unhappy') {
                // Unhappy: expres een databasefout laten ontstaan.
                $this->getModel()->forceDatabaseError();
            } else {
                // Happy: meldingen normaal ophalen.
                $meldingen = $this->getModel()->getAll();
            }
        } catch (Exception $e) {
            // De technische databasefout wordt vervangen door een nette melding.
            $error = 'De meldingen konden niet worden geladen.';
        }

        // Geeft de data door aan de view.
        $this->view('meldingen/index', [
            'scenario' => $scenario,
            'meldingen' => $meldingen,
            'error' => $error
        ]);
    }

    public function opstellen()
    {
        // Zelfde formulier, maar ander gedrag bij happy of unhappy.
        $scenario = (isset($_GET['scenario']) && $_GET['scenario'] === 'unhappy') ? 'unhappy' : 'happy';
        $success = '';
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Formulierwaarden netjes ophalen en spaties weghalen.
            $data = [
                'titel' => trim($_POST['titel'] ?? ''),
                'bericht' => trim($_POST['bericht'] ?? ''),
                'doelgroep' => trim($_POST['doelgroep'] ?? 'Notificatie')
            ];

            try {
                if ($scenario === 'unhappy') {
                    // Unhappy: opslaan naar een tabel die niet bestaat.
                    $this->getModel()->forceInsertDatabaseError($data);
                } else {
                    // Happy: melding echt opslaan in de database.
                    $this->getModel()->create($data);
                    $success = 'Melding succesvol verstuurd.';
                }
            } catch (Exception $e) {
                // Als de database faalt, ziet de gebruiker deze foutmelding.
                $error = 'Melding kon niet worden verstuurd. Probeer het later opnieuw.';
            }
        }

        // Formulier opnieuw tonen met succes- of foutmelding.
        $this->view('meldingen/opstellen', [
            'scenario' => $scenario,
            'success' => $success,
            'error' => $error
        ]);
    }
}
