<?php

/**
 * Controller voor het beheren en weergeven van tickets.
 * Bevat acties voor het ticketoverzicht, ticketdetails, downloads,
 * reserveringen en barcode-scanning.
 * Gebruikt foreign keys naar Bezoeker, Voorstelling en Prijs.
 */
class Ticketcontroller extends BaseController
{
    /** @var Ticketsmodel|null Het tickets model (lazy loaded) */
    private $model;

    /**
     * Constructor: controleert of de gebruiker is ingelogd.
     * Zo niet, dan wordt de gebruiker omgeleid naar het inlogscherm.
     */
    public function __construct()
    {
        if (!isset($_SESSION['account_id'])) {
            header('Location: ' . URLROOT . '/?url=inlogcontroller/inloggen');
            exit();
        }
    }

    /**
     * Lazy loading van het Ticketsmodel.
     * Voorkomt dat de app crasht als de database offline is.
     *
     * @return Ticketsmodel Het geladen model
     */
    private function getModel()
    {
        if ($this->model === null) {
            $this->model = $this->model('Ticketsmodel');
        }
        return $this->model;
    }

    /**
     * Standaard index actie — leidt door naar het ticketoverzicht.
     */
    public function index()
    {
        $this->overzicht();
    }

    /**
     * Toont alle gereserveerde tickets van de ingelogde bezoeker.
     * Happy: tickets worden correct weergegeven met datum, tijd en barcode.
     * Unhappy: bij een databasefout wordt een foutmelding getoond.
     */
    public function overzicht()
    {
        $error = '';
        $tickets = [];
        try {
            $bezoekerId = $_SESSION['bezoeker_id'] ?? null;
            if ($bezoekerId) {
                $tickets = $this->getModel()->getTicketsByBezoeker($bezoekerId);
            }
        } catch (Exception $e) {
            $error = 'Er is een fout opgetreden bij het laden van uw tickets. De database is momenteel niet bereikbaar.';
        }
        $this->view('tickets/ticketoverzicht', ['tickets' => $tickets, 'error' => $error]);
    }

    /**
     * Toont het reserveringsbevestigingsscherm en verwerkt de reservering.
     * Maakt een nieuw ticket aan met foreign keys naar Bezoeker, Voorstelling en Prijs.
     *
     * @param int|null $id Het Voorstelling.Id
     */
    public function reserveren($id = null)
    {
        if (!$id) {
            header('Location: ' . URLROOT . '/?url=voorstellingen/index');
            exit();
        }

        try {
            $voorstellingModel = $this->model('Voorstelling');
            $voorstelling = $voorstellingModel->getById($id);
        } catch (Exception $e) {
            $_SESSION['error'] = 'Kan voorstelling niet laden.';
            header('Location: ' . URLROOT . '/?url=voorstellingen/index');
            exit();
        }

        if (!$voorstelling) {
            header('Location: ' . URLROOT . '/?url=voorstellingen/index');
            exit();
        }

        // Verwerk de reservering bij POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $bezoekerId = $_SESSION['bezoeker_id'] ?? null;
                if (!$bezoekerId) {
                    $_SESSION['error'] = 'U moet ingelogd zijn als bezoeker om tickets te reserveren.';
                    header('Location: ' . URLROOT . '/?url=voorstellingen/index');
                    exit();
                }

                if ($this->getModel()->hasTicketForVoorstelling($bezoekerId, $voorstelling->Id)) {
                    $_SESSION['error'] = 'U heeft al een ticket voor deze voorstelling.';
                    header('Location: ' . URLROOT . '/?url=voorstellingen/index');
                    exit();
                }

                // Zoek het juiste PrijsId op basis van het tarief van de voorstelling
                $prijsId = $voorstellingModel->getPrijsIdByTarief($voorstelling->Prijs);

                $succes = $this->getModel()->createTicket($bezoekerId, $voorstelling->Id, $prijsId);
                if ($succes) {
                    $_SESSION['success'] = 'Uw ticket voor "' . $voorstelling->Naam . '" is succesvol gereserveerd!';
                } else {
                    $_SESSION['error'] = 'Reservering mislukt. Probeer het opnieuw.';
                }
            } catch (Exception $e) {
                $_SESSION['error'] = 'Reserveren mislukt: database is offline.';
            }
            header('Location: ' . URLROOT . '/?url=ticketcontroller/overzicht');
            exit();
        }

        $this->view('tickets/reserveren', ['voorstelling' => $voorstelling]);
    }

    /**
     * Toont de details van een specifiek ticket.
     *
     * @param int $id Het Ticket.Id
     */
    public function details($id)
    {
        $error = '';
        $ticket = null;
        try {
            $ticket = $this->getModel()->getTicketById($id);
            $bezoekerId = $_SESSION['bezoeker_id'] ?? null;
            if (!$ticket || $ticket->bezoeker_id != $bezoekerId) {
                header('Location: ' . URLROOT . '/?url=ticketcontroller/overzicht');
                exit();
            }
        } catch (Exception $e) {
            $error = 'Kan ticketdetails niet laden wegens een databasefout.';
        }
        $this->view('tickets/details', ['ticket' => $ticket, 'error' => $error]);
    }

    /**
     * Genereert en stuurt een downloadbaar .txt bestand van het ticket.
     *
     * @param int $id Het Ticket.Id
     */
    public function download($id)
    {
        try {
            $ticket = $this->getModel()->getTicketById($id);
            $bezoekerId = $_SESSION['bezoeker_id'] ?? null;
            if ($ticket && $ticket->bezoeker_id == $bezoekerId) {
                header('Content-Type: text/plain');
                header('Content-Disposition: attachment; filename="ticket-' . $ticket->nummer . '.txt"');
                echo "========================================\n";
                echo "           THEATER AURORA TICKET        \n";
                echo "========================================\n\n";
                echo "Voorstelling: " . $ticket->voorstelling . "\n";
                echo "Datum:        " . date('d-m-Y', strtotime($ticket->voorstelling_datum)) . "\n";
                echo "Tijd:         " . date('H:i', strtotime($ticket->voorstelling_tijd)) . "\n";
                echo "Barcode:      " . ($ticket->barcode ?: "GEEN BARCODE") . "\n\n";
                echo "Status:       " . $ticket->status . "\n";
                echo "Ticketnr:     " . $ticket->nummer . "\n\n";
                echo "========================================\n";
                echo "Toon deze barcode bij de ingang.\n";
                exit();
            } else {
                header('Location: ' . URLROOT . '/?url=ticketcontroller/overzicht');
                exit();
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Downloaden mislukt wegens een databasefout.';
            header('Location: ' . URLROOT . '/?url=ticketcontroller/overzicht');
            exit();
        }
    }

    /**
     * Barcode-scanner voor medewerkers en administrators.
     * Zoekt tickets op via barcode en kan ze markeren als gescand.
     */
    public function scan()
    {
        if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['medewerker', 'administrator'])) {
            header('Location: ' . URLROOT);
            exit();
        }

        $scanResult = null;
        $scanError = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['barcode'])) {
            $barcode = trim($_POST['barcode']);
            try {
                $scanResult = $this->getModel()->getTicketByBarcode($barcode);

                if (!$scanResult) {
                    $scanError = 'Ticket met barcode "' . htmlspecialchars($barcode) . '" niet gevonden.';
                } elseif (isset($_POST['mark'])) {
                    $this->getModel()->markeerGescand($barcode);
                    $scanResult->Status = 'Bezet';
                }
            } catch (Exception $e) {
                $scanError = 'Er trad een fout op bij het scannen.';
            }
        }

        $this->view('tickets/scan', [
            'scanResult' => $scanResult,
            'scanError'  => $scanError
        ]);
    }

    /**
     * Dashboard voor Admins en Medewerkers om alle voorstellingen te bekijken
     * en tickets te kunnen toevoegen.
     */
    public function dashboard()
    {
        // 1. Rolcontrole: alleen voor admins of medewerkers
        if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['administrator', 'medewerker'])) {
            header('Location: ' . URLROOT);
            exit();
        }

        $model = $this->getModel();
        $scanResult = null;
        $scanError = '';
        $showScanner = false;

        // 2. Scan functionaliteit verwerken (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['barcode'])) {
            $showScanner = true;
            $barcode = trim($_POST['barcode']);
            try {
                $scanResult = $model->getTicketByBarcode($barcode);

                if (!$scanResult) {
                    $scanError = 'Ticket met barcode "' . htmlspecialchars($barcode) . '" niet gevonden.';
                } elseif (isset($_POST['mark'])) {
                    $model->markeerGescand($barcode);
                    $scanResult->Status = 'Bezet';
                }
            } catch (Exception $e) {
                $scanError = 'Er trad een fout op bij het scannen.';
            }
        }

        // 3. Voorstellingen ophalen
        $voorstellingen = [];
        try {
            $voorstellingen = $model->getAllVoorstellingenMetBeschikbaarheid();
            
            // TEST MODE: forceer uitverkocht status
            if (defined('TEST_TICKETS_VOL') && TEST_TICKETS_VOL) {
                foreach ($voorstellingen as $v) {
                    $v->beschikbaar = 0;
                }
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Kon voorstellingen niet laden voor het dashboard.';
        }

        $this->view('tickets/dashboard', [
            'voorstellingen' => $voorstellingen,
            'scanResult'     => $scanResult,
            'scanError'      => $scanError,
            'showScanner'    => $showScanner
        ]);
    }

    /**
     * Admin/Medewerker: Pas de beschikbaarheid (MaxAantalTickets) van een voorstelling aan.
     * Toont het aanpas-scherm (GET) en verwerkt de wijziging (POST).
     *
     * @param int|null $id Het Voorstelling.Id
     */
    public function toevoegen($id = null)
    {
        // 1. Rolcontrole: alleen voor admins of medewerkers
        if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['administrator', 'medewerker'])) {
            header('Location: ' . URLROOT);
            exit();
        }

        if (!$id) {
            header('Location: ' . URLROOT . '/?url=ticketcontroller/dashboard');
            exit();
        }

        // 2. Haal voorstelling op met actuele beschikbaarheid
        $model = $this->getModel();
        $voorstelling = null;
        try {
            $voorstelling = $model->getVoorstellingMetBeschikbaarheid($id);
        } catch (Exception $e) {
            $_SESSION['error'] = 'Databasefout bij ophalen voorstelling.';
            header('Location: ' . URLROOT . '/?url=ticketcontroller/dashboard');
            exit();
        }

        if (!$voorstelling) {
            $_SESSION['error'] = 'Voorstelling niet gevonden.';
            header('Location: ' . URLROOT . '/?url=ticketcontroller/dashboard');
            exit();
        }

        // TEST MODE: forceer uitverkocht status voor unhappy scenario demonstratie
        if (defined('TEST_TICKETS_VOL') && TEST_TICKETS_VOL) {
            $voorstelling->beschikbaar = 0;
        }

        // 3. Verwerk het POST-verzoek (capaciteit aanpassen)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nieuweCapaciteit = isset($_POST['capaciteit']) ? (int)$_POST['capaciteit'] : 0;

            if ($nieuweCapaciteit < 0) {
                $_SESSION['error'] = 'Capaciteit moet een geldig getal zijn.';
            } else {
                try {
                    $model->updateCapaciteit($id, $nieuweCapaciteit);

                    $_SESSION['success'] = "Capaciteit succesvol aangepast naar {$nieuweCapaciteit}.";
                    
                    // Redirect om dubbele POST bij refresh te voorkomen
                    header('Location: ' . URLROOT . '/?url=ticketcontroller/toevoegen/' . $id);
                    exit();

                } catch (Exception $e) {
                    $_SESSION['error'] = 'Aanpassen mislukt: ' . $e->getMessage();
                    
                    // Ververs de beschikbaarheid na de fout zodat de view actuele data toont
                    $voorstelling = $model->getVoorstellingMetBeschikbaarheid($id);
                }
            }
        }

        // 4. Toon de view
        $this->view('tickets/toevoegen', [
            'voorstelling' => $voorstelling
        ]);
    }
}