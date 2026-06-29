<?php

/**
 * Model voor het ophalen van actieve medewerkers.
 * Gebruikt de genormaliseerde Medewerker, Gebruiker en Contact tabellen.
 */
class MedewerkerModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAll()
    {
        // Haalt medewerkers op met hun naam en e-mailadres uit gekoppelde tabellen.
        $this->db->query("
            SELECT
                m.Id,
                m.Nummer,
                m.Medewerkersoort,
                g.Voornaam,
                g.Tussenvoegsel,
                g.Achternaam,
                c.Email
            FROM Medewerker m
            INNER JOIN Gebruiker g ON m.GebruikerId = g.Id
            LEFT JOIN Contact c ON g.Id = c.GebruikerId
            WHERE m.Isactief = 1
            ORDER BY g.Achternaam, g.Voornaam
        ");

        return $this->db->resultSet();
    }

    public function forceDatabaseError()
    {
        // Deze tabel bestaat expres niet, zodat het unhappy scenario een echte fout krijgt.
        $this->db->query("SELECT * FROM NietBestaandeMedewerkersTabel");
        return $this->db->resultSet();
    }

    public function create($data)
    {
        // Eerst een gebruiker aanmaken, omdat Medewerker aan Gebruiker gekoppeld is.
        $this->db->query("
            INSERT INTO Gebruiker
            (Voornaam, Tussenvoegsel, Achternaam, Gebruikersnaam, Wachtwoord, Isactief)
            VALUES
            (:voornaam, :tussenvoegsel, :achternaam, :gebruikersnaam, :wachtwoord, 1)
        ");
        $this->db->bind(':voornaam', $data['voornaam']);
        $this->db->bind(':tussenvoegsel', $data['tussenvoegsel'] !== '' ? $data['tussenvoegsel'] : null);
        $this->db->bind(':achternaam', $data['achternaam']);
        $this->db->bind(':gebruikersnaam', strtolower($data['voornaam'] . '.' . $data['achternaam']));
        $this->db->bind(':wachtwoord', 'wachtwoord123');
        $this->db->execute();

        $this->db->query("SELECT LAST_INSERT_ID() AS gebruikerId");
        $gebruiker = $this->db->single();

        // De nieuwe gebruiker krijgt de rol Medewerker.
        $this->db->query("INSERT INTO Rol (GebruikerId, Naam, Isactief) VALUES (:gebruikerId, 'Medewerker', 1)");
        $this->db->bind(':gebruikerId', $gebruiker->gebruikerId, PDO::PARAM_INT);
        $this->db->execute();

        // Contactgegevens opslaan zodat het e-mailadres zichtbaar is in het overzicht.
        $this->db->query("
            INSERT INTO Contact (GebruikerId, Email, Mobiel, Isactief)
            VALUES (:gebruikerId, :email, '0600000000', 1)
        ");
        $this->db->bind(':gebruikerId', $gebruiker->gebruikerId, PDO::PARAM_INT);
        $this->db->bind(':email', $data['email']);
        $this->db->execute();

        $this->db->query("SELECT COALESCE(MAX(Nummer), 1000) + 1 AS nieuwNummer FROM Medewerker");
        $nummer = $this->db->single();

        // Daarna de medewerkergegevens zelf opslaan.
        $this->db->query("
            INSERT INTO Medewerker (GebruikerId, Nummer, Medewerkersoort, Isactief)
            VALUES (:gebruikerId, :nummer, :functie, 1)
        ");
        $this->db->bind(':gebruikerId', $gebruiker->gebruikerId, PDO::PARAM_INT);
        $this->db->bind(':nummer', $nummer->nieuwNummer, PDO::PARAM_INT);
        $this->db->bind(':functie', $data['functie']);
        return $this->db->execute();
    }

    public function forceInsertDatabaseError($data)
    {
        // Unhappy scenario: insert naar een niet-bestaande tabel.
        $this->db->query("
            INSERT INTO NietBestaandeMedewerkersTabel
            (Voornaam, Achternaam, Email)
            VALUES (:voornaam, :achternaam, :email)
        ");
        $this->db->bind(':voornaam', $data['voornaam']);
        $this->db->bind(':achternaam', $data['achternaam']);
        $this->db->bind(':email', $data['email']);
        return $this->db->execute();
    }
}
