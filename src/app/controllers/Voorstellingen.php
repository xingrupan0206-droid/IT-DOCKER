<?php

/**
 * Controller voor het beheren en weergeven van voorstellingen.
 * Heeft acties voor index, toevoegen, wijzigen en verwijderen.
 */
class Voorstellingen extends BaseController {

    private $model;

    public function __construct() {
        $this->model = $this->model('Voorstelling');
    }

    private function checkAdmin() {
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrator') {
            header('Location: ' . URLROOT);
            exit();
        }
    }

    public function index() {
        $voorstellingen = [];
        $error = '';

        try {
            $voorstellingen = $this->model->getAll();
        } catch (Exception $e) {
            $error = 'De voorstellingen kunnen niet worden geladen.';
        }

        // TEST MODE: forceer empty state (unhappy path demonstratie)
        if (defined('TEST_VOORSTELLINGEN_EMPTY') && TEST_VOORSTELLINGEN_EMPTY) {
            $voorstellingen = [];
        }

        $gereserveerd = [];
        // Check of bezoeker is ingelogd
        if (isset($_SESSION['bezoeker_id'])) {
            try {
                $ticketsModel = $this->model('Ticketsmodel');
                foreach ($voorstellingen as $v) {
                    if ($ticketsModel->hasTicketForVoorstelling($_SESSION['bezoeker_id'], $v->Id)) {
                        $gereserveerd[] = $v->Id;
                    }
                }
            } catch (Exception $e) {
                // Tickets check faalt stil, voorstellingen zijn wel geladen
            }
        }

        $data = [
            'title' => 'Voorstellingen',
            'voorstellingen' => $voorstellingen,
            'isAdmin' => isset($_SESSION['rol']) && $_SESSION['rol'] === 'administrator',
            'gereserveerd' => $gereserveerd,
            'error' => $error
        ];

        $this->view('voorstellingen/index', $data);
    }

    public function wijzigen($id = null) {
        $this->checkAdmin();
        if (!$id) {
            header('Location: ' . URLROOT . '/?url=voorstellingen/index');
            exit();
        }

        $voorstelling = null;
        try {
            $voorstelling = $this->model->getById($id);
        } catch (Exception $e) {
            $_SESSION['error'] = 'Kan voorstelling niet laden: database is niet bereikbaar.';
            header('Location: ' . URLROOT . '/?url=voorstellingen/index');
            exit();
        }

        if (!$voorstelling) {
            header('Location: ' . URLROOT . '/?url=voorstellingen/index');
            exit();
        }

        $fout = '';
        $scenario = (isset($_GET['scenario']) && $_GET['scenario'] === 'unhappy') ? 'unhappy' : 'happy';

        if (isset($_POST['opslaan'])) {
            $data = $this->validateVoorstelling($_POST);
            if (is_string($data)) {
                $fout = $data;
            } else {
                try {
                    if ($scenario === 'unhappy') {
                        $this->model->forceUpdateError($id, $data);
                    } else {
                        if ($this->model->update($id, $data)) {
                            $_SESSION['success'] = 'Voorstelling succesvol bijgewerkt.';
                            header('Location: ' . URLROOT . '/?url=voorstellingen/index');
                            exit();
                        } else {
                            $fout = 'Er is een fout opgetreden bij het bijwerken.';
                        }
                    }
                } catch (Exception $e) {
                    $fout = 'De wijzigingen konden niet worden opgeslagen vanwege een databasefout. Probeer het later opnieuw.';
                }
            }
        }

        $this->view('voorstellingen/wijzigen', [
            'voorstelling' => $voorstelling,
            'fout' => $fout,
            'title' => 'Voorstelling bewerken'
        ]);
    }

    public function verwijderen($id = null) {
        $this->checkAdmin();
        if (!$id) {
            header('Location: ' . URLROOT . '/?url=voorstellingen/index');
            exit();
        }

        $voorstelling = $this->model->getById($id);
        if (!$voorstelling) {
            header('Location: ' . URLROOT . '/?url=voorstellingen/index');
            exit();
        }

        // TEST MODE: forceer blokkeren door reserveringen (unhappy path demonstratie)
        $forceBlock = defined('TEST_VERWIJDEREN_BLOKKEER') && TEST_VERWIJDEREN_BLOKKEER;

        if ($forceBlock || $this->model->hasTickets($id)) {
            $_SESSION['error'] = 'Deze voorstelling kan niet worden verwijderd omdat er al reserveringen zijn.';
            header('Location: ' . URLROOT . '/?url=voorstellingen/index');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->model->delete($id)) {
                $_SESSION['success'] = 'Voorstelling succesvol verwijderd.';
            } else {
                $_SESSION['error'] = 'Er is een fout opgetreden bij het verwijderen.';
            }
            header('Location: ' . URLROOT . '/?url=voorstellingen/index');
            exit();
        }

        $this->view('voorstellingen/verwijderen', [
            'voorstelling' => $voorstelling,
            'title' => 'Voorstelling verwijderen'
        ]);
    }

    private function validateVoorstelling($post) {
        $naam = trim($post['naam'] ?? '');
        $genre = trim($post['genre'] ?? '');
        $datum = trim($post['datum'] ?? '');
        $tijd = trim($post['tijd'] ?? '');
        $zaal = trim($post['zaal'] ?? '');
        $beschrijving = trim($post['beschrijving'] ?? '');
        $prijs = trim($post['prijs'] ?? '');
        $maxtickets = trim($post['maxtickets'] ?? '');
        $afbeelding = trim($post['afbeelding'] ?? '');
        $beschikbaarheid = trim($post['beschikbaarheid'] ?? 'Ingepland');

        if (empty($naam) || empty($genre) || empty($datum) || empty($tijd) || empty($zaal) || $prijs === '') {
            return 'Vul alle verplichte velden in.';
        }
        if (!is_numeric($prijs) || $prijs < 0) {
            return 'Voer een geldige prijs in.';
        }
        if (!is_numeric($maxtickets) || $maxtickets < 1) {
            return 'Voer een geldig aantal tickets in.';
        }

        return [
            'naam' => $naam,
            'genre' => $genre,
            'datum' => $datum,
            'tijd' => $tijd,
            'zaal' => $zaal,
            'beschrijving' => $beschrijving,
            'prijs' => $prijs,
            'maxtickets' => $maxtickets,
            'afbeelding' => $afbeelding,
            'beschikbaarheid' => $beschikbaarheid
        ];
    }

    public function toevoegen() {
        $this->checkAdmin();
        $fout = '';
        $scenario = (isset($_GET['scenario']) && $_GET['scenario'] === 'unhappy') ? 'unhappy' : 'happy';

        if (isset($_POST['opslaan'])) {
            $data = $this->validateVoorstelling($_POST);
            if (is_string($data)) {
                $fout = $data;
            } else {
                $data['medewerkerid'] = $_SESSION['medewerker_id'] ?? 1;
                $data['opmerking'] = '';

                try {
                    if ($scenario === 'unhappy') {
                        $this->model->forceInsertError($data);
                    } else {
                        if ($this->model->create($data)) {
                            $_SESSION['success'] = 'Voorstelling succesvol toegevoegd.';
                            header('Location: ' . URLROOT . '/?url=voorstellingen/index');
                            exit();
                        } else {
                            $fout = 'Er is een fout opgetreden bij het opslaan.';
                        }
                    }
                } catch (Exception $e) {
                    $fout = 'De voorstelling kon niet worden opgeslagen vanwege een databasefout. Probeer het later opnieuw.';
                }
            }
        }

        $this->view('voorstellingen/toevoegen', [
            'fout' => $fout,
            'title' => 'Nieuwe voorstelling toevoegen'
        ]);
    }
}