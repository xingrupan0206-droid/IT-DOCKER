<?php

/**
 * Model voor het registreren van nieuwe gebruikers.
 * Voert inserts uit in de genormaliseerde tabellen: Gebruiker, Rol, Contact en Bezoeker.
 */
class Registreer
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
     * Controleert of een e-mailadres al in gebruik is in de Contact tabel.
     *
     * @param string $email Het te controleren e-mailadres
     * @return bool True als het e-mailadres al bestaat
     */
    public function emailBestaat($email)
    {
        $this->db->query("SELECT Id FROM Contact WHERE Email = :email AND Isactief = 1");
        $this->db->bind(':email', $email, PDO::PARAM_STR);
        $result = $this->db->single();
        return $result !== false;
    }

    /**
     * Registreert een nieuwe gebruiker door gegevens in 4 tabellen in te voegen:
     * 1. Gebruiker — basisgegevens en wachtwoord
     * 2. Rol — standaard rol 'Bezoeker'
     * 3. Contact — e-mailadres
     * 4. Bezoeker — relatienummer
     *
     * @param string      $voornaam      Voornaam van de gebruiker
     * @param string|null $tussenvoegsel Optioneel tussenvoegsel
     * @param string      $achternaam    Achternaam van de gebruiker
     * @param string      $email         E-mailadres
     * @param string      $wachtwoord    Wachtwoord (wordt opgeslagen zoals ontvangen)
     * @return bool True als de registratie succesvol was
     */
    public function registreer($voornaam, $tussenvoegsel, $achternaam, $email, $wachtwoord)
    {
        // Stap 1: Maak de gebruiker aan
        $gebruikersnaam = strtolower($voornaam . '.' . $achternaam);
        $this->db->query("
            INSERT INTO Gebruiker (Voornaam, Tussenvoegsel, Achternaam, Gebruikersnaam, Wachtwoord, Isactief)
            VALUES (:voornaam, :tussenvoegsel, :achternaam, :gebruikersnaam, :wachtwoord, 1)
        ");
        $this->db->bind(':voornaam',       $voornaam,       PDO::PARAM_STR);
        $this->db->bind(':tussenvoegsel',  $tussenvoegsel,  PDO::PARAM_STR);
        $this->db->bind(':achternaam',     $achternaam,     PDO::PARAM_STR);
        $this->db->bind(':gebruikersnaam', $gebruikersnaam, PDO::PARAM_STR);
        $this->db->bind(':wachtwoord',     $wachtwoord,     PDO::PARAM_STR);
        $this->db->execute();

        // Haal het nieuwe GebruikerId op
        $dbHandler = new Database();
        $dbHandler->query("SELECT Id FROM Gebruiker WHERE Gebruikersnaam = :gn ORDER BY Id DESC LIMIT 1");
        $dbHandler->bind(':gn', $gebruikersnaam, PDO::PARAM_STR);
        $result = $dbHandler->single();
        $gebruikerId = $result->Id;

        // Stap 2: Ken de rol 'Bezoeker' toe
        $dbHandler->query("INSERT INTO Rol (GebruikerId, Naam, Isactief) VALUES (:gid, 'Bezoeker', 1)");
        $dbHandler->bind(':gid', $gebruikerId, PDO::PARAM_INT);
        $dbHandler->execute();

        // Stap 3: Voeg contactgegevens toe
        $dbHandler->query("INSERT INTO Contact (GebruikerId, Email, Mobiel, Isactief) VALUES (:gid, :email, '', 1)");
        $dbHandler->bind(':gid',   $gebruikerId, PDO::PARAM_INT);
        $dbHandler->bind(':email', $email,        PDO::PARAM_STR);
        $dbHandler->execute();

        // Stap 4: Maak een bezoekersrecord aan met uniek relatienummer
        $relatienummer = 2000 + $gebruikerId;
        $dbHandler->query("INSERT INTO Bezoeker (GebruikerId, Relatienummer, Isactief) VALUES (:gid, :rn, 1)");
        $dbHandler->bind(':gid', $gebruikerId,   PDO::PARAM_INT);
        $dbHandler->bind(':rn',  $relatienummer, PDO::PARAM_INT);
        $dbHandler->execute();

        return true;
    }
}
