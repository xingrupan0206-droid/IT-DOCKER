<?php
/**
 * Model voor account-gerelateerde databasebewerkingen.
 * Bevat functies voor het ophalen, controleren en aanmaken van accounts.
 */
class AccountModel
{
    /** @var Database PDO database wrapper */
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Haalt alle actieve accounts op met voornaam, tussenvoegsel,
     * achternaam, email en rol, gesorteerd op ID.
     */
    public function getAll()
    {
        $this->db->query("
            SELECT 
                g.Id AS account_id, 
                g.Voornaam AS voornaam, 
                g.Tussenvoegsel AS tussenvoegsel, 
                g.Achternaam AS achternaam, 
                c.Email AS email, 
                r.Naam AS rol 
            FROM Gebruiker g
            LEFT JOIN Contact c ON g.Id = c.GebruikerId
            LEFT JOIN Rol r ON g.Id = r.GebruikerId
            WHERE g.Isactief = 1
            ORDER BY g.Id ASC
        ");
        return $this->db->resultSet();
    }

    /**
     * Haalt een enkel account op via ID.
     */
    public function getById($id)
    {
        $this->db->query("
            SELECT 
                g.Id, g.Voornaam, g.Tussenvoegsel, g.Achternaam, g.Gebruikersnaam,
                c.Email,
                r.Naam AS rol
            FROM Gebruiker g
            LEFT JOIN Contact c ON g.Id = c.GebruikerId
            LEFT JOIN Rol r ON g.Id = r.GebruikerId
            WHERE g.Id = :id AND g.Isactief = 1
        ");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    /**
     * Controleert of een e-mailadres al in gebruik is.
     */
    public function emailExists($email)
    {
        $this->db->query("SELECT Id FROM Contact WHERE Email = :email AND Isactief = 1");
        $this->db->bind(':email', $email, PDO::PARAM_STR);
        return $this->db->single() !== false;
    }

    /**
     * Maakt een nieuw account aan met opgegeven gegevens en rol.
     * Afhankelijk van de rol wordt ook een Medewerker- of Bezoeker-record aangemaakt.
     */
    public function create($voornaam, $tussenvoegsel, $achternaam, $email, $wachtwoord, $rol)
    {
        $gebruikersnaam = strtolower($voornaam . '.' . $achternaam);

        // Stap 1: gebruiker aanmaken
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

        // Stap 2: nieuw GebruikerId ophalen
        $this->db->query("SELECT Id FROM Gebruiker WHERE Gebruikersnaam = :gn ORDER BY Id DESC LIMIT 1");
        $this->db->bind(':gn', $gebruikersnaam, PDO::PARAM_STR);
        $result = $this->db->single();
        $gebruikerId = $result->Id;

        // Stap 3: rol toewijzen
        $this->db->query("INSERT INTO Rol (GebruikerId, Naam, Isactief) VALUES (:gid, :rol, 1)");
        $this->db->bind(':gid', $gebruikerId, PDO::PARAM_INT);
        $this->db->bind(':rol', $rol,         PDO::PARAM_STR);
        $this->db->execute();

        // Stap 4: contactgegevens toevoegen
        $this->db->query("INSERT INTO Contact (GebruikerId, Email, Mobiel, Isactief) VALUES (:gid, :email, '', 1)");
        $this->db->bind(':gid',   $gebruikerId, PDO::PARAM_INT);
        $this->db->bind(':email', $email,        PDO::PARAM_STR);
        $this->db->execute();

        // Stap 5: rol-specifieke tabellen vullen
        if ($rol === 'Medewerker') {
            // Medewerker record met automatisch nummer
            $maxQuery = $this->db;
            $maxQuery->query("SELECT COALESCE(MAX(Id), 0) + 1 AS nextId FROM Medewerker");
            $maxResult = $maxQuery->single();
            $medewerkerId = $maxResult->nextId;
            $nummer = 1000 + $medewerkerId;

            $maxQuery->query("INSERT INTO Medewerker (Id, GebruikerId, Nummer, Medewerkersoort, Isactief) VALUES (:id, :gid, :nummer, 'Algemeen', 1)");
            $maxQuery->bind(':id',     $medewerkerId,  PDO::PARAM_INT);
            $maxQuery->bind(':gid',    $gebruikerId,   PDO::PARAM_INT);
            $maxQuery->bind(':nummer', $nummer,        PDO::PARAM_INT);
            $maxQuery->execute();
        } elseif ($rol === 'Bezoeker') {
            // Bezoeker record met uniek relatienummer
            $relatienummer = 2000 + $gebruikerId;
            $this->db->query("INSERT INTO Bezoeker (GebruikerId, Relatienummer, Isactief) VALUES (:gid, :rn, 1)");
            $this->db->bind(':gid', $gebruikerId,   PDO::PARAM_INT);
            $this->db->bind(':rn',  $relatienummer, PDO::PARAM_INT);
            $this->db->execute();
        }

        return true;
    }
}
