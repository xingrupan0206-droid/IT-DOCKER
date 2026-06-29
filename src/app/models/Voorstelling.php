<?php

/**
 * Model voor het beheren van voorstellingen.
 * Gebruikt de genormaliseerde Voorstelling tabel met een foreign key naar Medewerker.
 */
class Voorstelling
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
     * Haalt alle actieve voorstellingen op, gesorteerd op datum en tijd.
     *
     * @return array Array van voorstelling-objecten
     */
    public function getAll()
    {
        $this->db->query("SELECT * FROM Voorstelling WHERE Isactief = 1 ORDER BY Datum ASC, Tijd ASC");
        return $this->db->resultSet();
    }

    /**
     * Haalt één specifieke voorstelling op via ID.
     *
     * @param int $id Het Voorstelling.Id
     * @return object|false Voorstelling-object of false
     */
    public function getById($id)
    {
        $this->db->query("SELECT * FROM Voorstelling WHERE Id = :id AND Isactief = 1");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    /**
     * Maakt een nieuwe voorstelling aan in de database.
     *
     * @param array $data Associatieve array met voorstellingsgegevens
     * @return bool True als succesvol aangemaakt
     */
    public function create($data)
    {
        $this->db->query("
            INSERT INTO Voorstelling 
            (MedewerkerId, Naam, Beschrijving, Genre, Datum, Tijd, Zaal, MaxAantalTickets, Prijs, Afbeelding, Beschikbaarheid, Opmerking, Isactief) 
            VALUES 
            (:medewerkerid, :naam, :beschrijving, :genre, :datum, :tijd, :zaal, :maxtickets, :prijs, :afbeelding, :beschikbaarheid, :opmerking, 1)
        ");
        $this->db->bind(':medewerkerid',    $data['medewerkerid'],    PDO::PARAM_INT);
        $this->db->bind(':naam',            $data['naam'],            PDO::PARAM_STR);
        $this->db->bind(':beschrijving',    $data['beschrijving'],    PDO::PARAM_STR);
        $this->db->bind(':genre',           $data['genre'],           PDO::PARAM_STR);
        $this->db->bind(':datum',           $data['datum'],           PDO::PARAM_STR);
        $this->db->bind(':tijd',            $data['tijd'],            PDO::PARAM_STR);
        $this->db->bind(':zaal',            $data['zaal'],            PDO::PARAM_STR);
        $this->db->bind(':maxtickets',      $data['maxtickets'],      PDO::PARAM_INT);
        $this->db->bind(':prijs',           $data['prijs'],           PDO::PARAM_STR);
        $this->db->bind(':afbeelding',      $data['afbeelding'],      PDO::PARAM_STR);
        $this->db->bind(':beschikbaarheid', $data['beschikbaarheid'], PDO::PARAM_STR);
        $this->db->bind(':opmerking',       $data['opmerking'],       PDO::PARAM_STR);
        return $this->db->execute();
    }

    /**
     * Werkt een bestaande voorstelling bij.
     *
     * @param int   $id   Het Voorstelling.Id
     * @param array $data Associatieve array met bijgewerkte gegevens
     * @return bool True als succesvol bijgewerkt
     */
    public function update($id, $data)
    {
        $this->db->query("
            UPDATE Voorstelling SET 
                Naam = :naam,
                Beschrijving = :beschrijving,
                Genre = :genre,
                Datum = :datum,
                Tijd = :tijd,
                Zaal = :zaal,
                MaxAantalTickets = :maxtickets,
                Prijs = :prijs,
                Afbeelding = :afbeelding,
                Beschikbaarheid = :beschikbaarheid,
                Opmerking = :opmerking
            WHERE Id = :id
        ");
        $this->db->bind(':id',              $id,                      PDO::PARAM_INT);
        $this->db->bind(':naam',            $data['naam'],            PDO::PARAM_STR);
        $this->db->bind(':beschrijving',    $data['beschrijving'],    PDO::PARAM_STR);
        $this->db->bind(':genre',           $data['genre'],           PDO::PARAM_STR);
        $this->db->bind(':datum',           $data['datum'],           PDO::PARAM_STR);
        $this->db->bind(':tijd',            $data['tijd'],            PDO::PARAM_STR);
        $this->db->bind(':zaal',            $data['zaal'],            PDO::PARAM_STR);
        $this->db->bind(':maxtickets',      $data['maxtickets'],      PDO::PARAM_INT);
        $this->db->bind(':prijs',           $data['prijs'],           PDO::PARAM_STR);
        $this->db->bind(':afbeelding',      $data['afbeelding'],      PDO::PARAM_STR);
        $this->db->bind(':beschikbaarheid', $data['beschikbaarheid'], PDO::PARAM_STR);
        $this->db->bind(':opmerking',       $data['opmerking'],       PDO::PARAM_STR);
        return $this->db->execute();
    }

    /**
     * Verwijdert een voorstelling (soft delete via Isactief = 0).
     *
     * @param int $id Het Voorstelling.Id
     * @return bool True als succesvol verwijderd
     */
    public function delete($id)
    {
        $this->db->query("UPDATE Voorstelling SET Isactief = 0 WHERE Id = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }

    /**
     * Controleert of er tickets zijn gereserveerd voor een voorstelling.
     *
     * @param int $id Het Voorstelling.Id
     * @return bool True als er tickets bestaan
     */
    public function hasTickets($id)
    {
        $this->db->query("SELECT COUNT(*) AS count FROM Ticket WHERE VoorstellingId = :id AND Isactief = 1");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $result = $this->db->single();
        return $result->count > 0;
    }

    /**
     * Zoekt het juiste Prijs.Id op basis van het tarief van een voorstelling.
     * Als het tarief niet bestaat, wordt het eerste actieve tarief gebruikt.
     *
     * @param float $tarief Het gewenste tarief
     * @return int Het Prijs.Id
     */
    public function getPrijsIdByTarief($tarief)
    {
        $this->db->query("SELECT Id FROM Prijs WHERE Tarief = :tarief AND Isactief = 1 LIMIT 1");
        $this->db->bind(':tarief', $tarief, PDO::PARAM_STR);
        $result = $this->db->single();
        if ($result) {
            return $result->Id;
        }
        // Fallback: gebruik het eerste actieve tarief
        $this->db->query("SELECT Id FROM Prijs WHERE Isactief = 1 ORDER BY Tarief ASC LIMIT 1");
        $result = $this->db->single();
        return $result ? $result->Id : 1;
    }

    /**
     * Forceert een databasefout voor het unhappy scenario bij toevoegen.
     */
    public function forceInsertError($data)
    {
        $this->db->query("INSERT INTO NietBestaandeVoorstellingTabel (Naam) VALUES (:naam)");
        $this->db->bind(':naam', $data['naam'], PDO::PARAM_STR);
        return $this->db->execute();
    }

    /**
     * Forceert een databasefout voor het unhappy scenario bij wijzigen.
     */
    public function forceUpdateError($id, $data)
    {
        $this->db->query("UPDATE NietBestaandeVoorstellingTabel SET Naam = :naam WHERE Id = :id");
        $this->db->bind(':naam', $data['naam'], PDO::PARAM_STR);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
}