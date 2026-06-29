<?php

class Homepages extends BaseController
{

    public function index($firstname = NULL, $infix = NULL, $lastname = NULL)
    {
        $data = [
            'title' => 'Theater Aurora',
        ];

        // Haal actuele voorstellingen op
        $voorstellingModel = $this->model('Voorstelling');
        $voorstellingen = $voorstellingModel->getAll();
        $data['voorstellingen'] = $voorstellingen;

        // Check welke voorstellingen al gereserveerd zijn
        $gereserveerd = [];
        if (isset($_SESSION['account_id'])) {
            $ticketsModel = $this->model('Ticketsmodel');
            foreach ($voorstellingen as $v) {
                if ($ticketsModel->hasTicketForVoorstelling($_SESSION['account_id'], $v->Naam)) {
                    $gereserveerd[] = $v->Id;
                }
            }
        }
        $data['gereserveerd'] = $gereserveerd;

        $this->view('homepages/index', $data);
    }

}