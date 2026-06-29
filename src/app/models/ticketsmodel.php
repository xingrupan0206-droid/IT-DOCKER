<?php

/**
 * Model voor het beheren van tickets.
 * Gebruikt de genormaliseerde Ticket tabel met foreign keys naar
 * Bezoeker, Voorstelling en Prijs.
 */
class Ticketsmodel
{
    /** @var Database PDO database wrapper */
    private $db;

    /**
     * Constructor: initialiseert de database verbinding.
     */
    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Haalt alle tickets op voor een specifieke bezoeker, inclusief
     * voorstellingsnaam, datum en tijd via JOINs.
     *
     * @param int $bezoekerId Het Bezoeker.Id
     * @return array Array van ticket-objecten met voorstelling details
     */
    public function getTicketsByBezoeker($bezoekerId)
    {
        $this->db->query("
            SELECT 
                t.Id        AS id,
                t.Nummer    AS nummer,
                t.Barcode   AS barcode,
                t.Datum     AS datum,
                t.Tijd      AS tijd,
                t.Status    AS status,
                v.Naam      AS voorstelling,
                v.Datum     AS voorstelling_datum,
                v.Tijd      AS voorstelling_tijd,
                p.Tarief    AS prijs
            FROM Ticket t
            INNER JOIN Voorstelling v ON t.VoorstellingId = v.Id
            INNER JOIN Prijs p        ON t.PrijsId = p.Id
            WHERE t.BezoekerId = :bid
              AND t.Isactief = 1
            ORDER BY v.Datum ASC, v.Tijd ASC
        ");
        $this->db->bind(':bid', $bezoekerId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    /**
     * Haalt de details van één specifiek ticket op.
     *
     * @param int $id Het Ticket.Id
     * @return object|false Ticket-object met alle details
     */
    public function getTicketById($id)
    {
        $this->db->query("
            SELECT 
                t.Id            AS id,
                t.BezoekerId    AS bezoeker_id,
                t.Nummer        AS nummer,
                t.Barcode       AS barcode,
                t.Datum         AS datum,
                t.Tijd          AS tijd,
                t.Status        AS status,
                v.Naam          AS voorstelling,
                v.Datum         AS voorstelling_datum,
                v.Tijd          AS voorstelling_tijd,
                p.Tarief        AS prijs
            FROM Ticket t
            INNER JOIN Voorstelling v ON t.VoorstellingId = v.Id
            INNER JOIN Prijs p        ON t.PrijsId = p.Id
            WHERE t.Id = :id
              AND t.Isactief = 1
        ");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    /**
     * Controleert of een bezoeker al een ticket heeft voor een voorstelling.
     *
     * @param int $bezoekerId     Het Bezoeker.Id
     * @param int $voorstellingId Het Voorstelling.Id
     * @return bool True als er al een ticket bestaat
     */
    public function hasTicketForVoorstelling($bezoekerId, $voorstellingId)
    {
        $this->db->query("
            SELECT COUNT(*) AS count 
            FROM Ticket 
            WHERE BezoekerId = :bid 
              AND VoorstellingId = :vid 
              AND Isactief = 1
        ");
        $this->db->bind(':bid', $bezoekerId, PDO::PARAM_INT);
        $this->db->bind(':vid', $voorstellingId, PDO::PARAM_INT);
        $result = $this->db->single();
        return $result && $result->count > 0;
    }

    /**
     * Maakt een nieuw ticket aan met foreign keys naar Bezoeker, Voorstelling en Prijs.
     * Genereert automatisch een uniek reserveringsnummer en barcode.
     *
     * @param int $bezoekerId     Het Bezoeker.Id
     * @param int $voorstellingId Het Voorstelling.Id
     * @param int $prijsId        Het Prijs.Id
     * @return bool True als het ticket succesvol is aangemaakt
     */
    public function createTicket($bezoekerId, $voorstellingId, $prijsId)
    {
        // Genereer uniek reserveringsnummer
        $this->db->query("SELECT COALESCE(MAX(Nummer), 3000) + 1 AS nextNummer FROM Ticket");
        $result = $this->db->single();
        $nummer = $result->nextNummer;

        // Genereer unieke barcode (max 20 tekens)
        $barcode = 'TKT-' . strtoupper(bin2hex(random_bytes(4))) . substr(strtoupper(bin2hex(random_bytes(4))), 0, 4);

        $this->db->query("
            INSERT INTO Ticket (BezoekerId, VoorstellingId, PrijsId, Nummer, Barcode, Datum, Tijd, Status, Isactief)
            VALUES (:bid, :vid, :pid, :nummer, :barcode, CURDATE(), CURTIME(), 'Gereserveerd', 1)
        ");
        $this->db->bind(':bid',     $bezoekerId,     PDO::PARAM_INT);
        $this->db->bind(':vid',     $voorstellingId, PDO::PARAM_INT);
        $this->db->bind(':pid',     $prijsId,        PDO::PARAM_INT);
        $this->db->bind(':nummer',  $nummer,         PDO::PARAM_INT);
        $this->db->bind(':barcode', $barcode,        PDO::PARAM_STR);
        return $this->db->execute();
    }

    /**
     * Zoekt een ticket op via barcode (voor de scanner).
     *
     * @param string $barcode De barcode tekst
     * @return object|false Ticket-object of false
     */
    public function getTicketByBarcode($barcode)
    {
        $this->db->query("
            SELECT 
                t.*, 
                v.Naam AS voorstelling,
                v.Datum AS voorstelling_datum,
                v.Tijd AS voorstelling_tijd
            FROM Ticket t
            INNER JOIN Voorstelling v ON t.VoorstellingId = v.Id
            WHERE t.Barcode = :barcode
        ");
        $this->db->bind(':barcode', $barcode, PDO::PARAM_STR);
        return $this->db->single();
    }

    /**
     * Markeert een ticket als gescand (status: Bezet).
     *
     * @param string $barcode De barcode van het te scannen ticket
     * @return bool True als succesvol gemarkeerd
     */
    public function markeerGescand($barcode)
    {
        $this->db->query("UPDATE Ticket SET Status = 'Bezet' WHERE Barcode = :barcode");
        $this->db->bind(':barcode', $barcode, PDO::PARAM_STR);
        return $this->db->execute();
    }

    /**
     * Haalt alle voorstellingen op samen met hun actuele beschikbaarheid.
     * Handig voor het ticket dashboard.
     *
     * @return array Array van voorstelling-objecten
     */
    public function getAllVoorstellingenMetBeschikbaarheid()
    {
        $this->db->query("
            SELECT
                v.*,
                (v.MaxAantalTickets - COUNT(t.Id)) AS beschikbaar
            FROM Voorstelling v
            LEFT JOIN Ticket t
                ON t.VoorstellingId = v.Id
               AND t.Isactief = 1
            WHERE v.Isactief = 1
            GROUP BY v.Id
            ORDER BY v.Datum ASC, v.Tijd ASC
        ");
        return $this->db->resultSet();
    }

    /**
     * Haalt een voorstelling op samen met het actuele aantal beschikbare plaatsen.
     * Beschikbaarheid = MaxAantalTickets - aantal actieve gereserveerde tickets.
     * Leest de Voorstelling-tabel read-only; wijzigt niets.
     *
     * @param int $id Het Voorstelling.Id
     * @return object|false Voorstelling-object met extra veld 'beschikbaar', of false
     */
    public function getVoorstellingMetBeschikbaarheid($id)
    {
        $this->db->query("
            SELECT
                v.*,
                (v.MaxAantalTickets - COUNT(t.Id)) AS beschikbaar
            FROM Voorstelling v
            LEFT JOIN Ticket t
                ON t.VoorstellingId = v.Id
               AND t.Isactief = 1
            WHERE v.Id = :id
              AND v.Isactief = 1
            GROUP BY v.Id
        ");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    /**
     * Haalt alle actieve bezoekers op met hun naam en relatienummer.
     * Gebruikt voor de bezoeker-dropdown in het admin-formulier.
     *
     * @return array Array van bezoeker-objecten
     */
    public function getAllBezoekers()
    {
        $this->db->query("
            SELECT
                b.Id            AS bezoeker_id,
                b.Relatienummer AS relatienummer,
                g.Voornaam      AS voornaam,
                g.Tussenvoegsel AS tussenvoegsel,
                g.Achternaam    AS achternaam
            FROM Bezoeker b
            INNER JOIN Gebruiker g ON b.GebruikerId = g.Id
            WHERE b.Isactief = 1
              AND g.Isactief = 1
            ORDER BY g.Achternaam ASC, g.Voornaam ASC
        ");
        return $this->db->resultSet();
    }

    /**
     * Telt het aantal actieve tickets dat voor een voorstelling is gereserveerd.
     *
     * @param int $voorstellingId Het Voorstelling.Id
     * @return int Aantal gereserveerde tickets
     */
    public function getTicketCountByVoorstelling($voorstellingId)
    {
        $this->db->query("
            SELECT COUNT(*) AS aantal
            FROM Ticket
            WHERE VoorstellingId = :vid
              AND Isactief = 1
        ");
        $this->db->bind(':vid', $voorstellingId, PDO::PARAM_INT);
        $result = $this->db->single();
        return $result ? (int)$result->aantal : 0;
    }

    /**
     * Past de totale capaciteit (MaxAantalTickets) van een voorstelling aan.
     * Gooit een exceptie als de nieuwe capaciteit lager is dan het aantal reeds verkochte tickets.
     *
     * @param int $voorstellingId Het Voorstelling.Id
     * @param int $nieuweCapaciteit De nieuwe totale capaciteit
     * @return bool
     * @throws Exception
     */
    public function updateCapaciteit($voorstellingId, $nieuweCapaciteit)
    {
        // 1. Controleer hoeveel tickets er al gereserveerd zijn
        $reedsVerkocht = $this->getTicketCountByVoorstelling($voorstellingId);

        if ($nieuweCapaciteit < $reedsVerkocht) {
            throw new Exception("De nieuwe capaciteit ({$nieuweCapaciteit}) kan niet lager zijn dan het aantal reeds verkochte tickets ({$reedsVerkocht}).");
        }

        // 2. Update de Voorstelling
        $this->db->query("
            UPDATE Voorstelling
            SET MaxAantalTickets = :cap
            WHERE Id = :vid
        ");
        $this->db->bind(':cap', $nieuweCapaciteit, PDO::PARAM_INT);
        $this->db->bind(':vid', $voorstellingId, PDO::PARAM_INT);
        
        return $this->db->execute();
    }

}