<?php

/**
 * Model voor het inloggen van gebruikers.
 * Haalt gebruikersgegevens op via de genormaliseerde tabellen
 * Gebruiker, Rol en Contact met JOIN queries.
 */
class Inloggen
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
     * Zoekt een account op via e-mailadres.
     * Voert een JOIN uit over Gebruiker, Contact en Rol om alle
     * benodigde inloggegevens in één query op te halen.
     *
     * @param string $email Het e-mailadres van de gebruiker
     * @return object|false Account-object met Id, Voornaam, Wachtwoord, rol, email, etc.
     */
    public function getAccountByEmail($email)
    {
        $this->db->query("
            SELECT 
                g.Id,
                g.Voornaam,
                g.Tussenvoegsel,
                g.Achternaam,
                g.Wachtwoord,
                g.Gebruikersnaam,
                r.Naam AS rol,
                c.Email AS email
            FROM Gebruiker g
            INNER JOIN Contact c ON g.Id = c.GebruikerId
            INNER JOIN Rol r     ON g.Id = r.GebruikerId
            WHERE c.Email = :email
              AND g.Isactief = 1
            LIMIT 1
        ");
        $this->db->bind(':email', $email, PDO::PARAM_STR);
        return $this->db->single();
    }

    /**
     * Haalt het BezoekerId op voor een gebruiker (nodig voor ticket-operaties).
     *
     * @param int $gebruikerId Het Gebruiker.Id
     * @return int|null Het Bezoeker.Id of null als de gebruiker geen bezoeker is
     */
    public function getBezoekerId($gebruikerId)
    {
        $this->db->query("SELECT Id FROM Bezoeker WHERE GebruikerId = :gid AND Isactief = 1");
        $this->db->bind(':gid', $gebruikerId, PDO::PARAM_INT);
        $result = $this->db->single();
        return $result ? $result->Id : null;
    }

    /**
     * Haalt het MedewerkerId op voor een gebruiker (nodig voor admin-operaties).
     *
     * @param int $gebruikerId Het Gebruiker.Id
     * @return int|null Het Medewerker.Id of null
     */
    public function getMedewerkerId($gebruikerId)
    {
        $this->db->query("SELECT Id FROM Medewerker WHERE GebruikerId = :gid AND Isactief = 1");
        $this->db->bind(':gid', $gebruikerId, PDO::PARAM_INT);
        $result = $this->db->single();
        return $result ? $result->Id : null;
    }
}