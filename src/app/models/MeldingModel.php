<?php

/**
 * Model voor het ophalen van actieve meldingen.
 * Gebruikt de genormaliseerde Melding tabel.
 */
class MeldingModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAll()
    {
        // Haalt alleen actieve meldingen op uit de Melding tabel.
        $this->db->query("
            SELECT
                Id,
                Nummer,
                Type,
                Bericht,
                DatumAangemaakt
            FROM Melding
            WHERE Isactief = 1
            ORDER BY DatumAangemaakt DESC, Id DESC
        ");

        return $this->db->resultSet();
    }

    public function forceDatabaseError()
    {
        // Deze tabel bestaat niet, dus dit veroorzaakt een echte database-error.
        $this->db->query("SELECT * FROM NietBestaandeMeldingenTabel");
        return $this->db->resultSet();
    }

    public function create($data)
    {
        // Nieuw meldingsnummer bepalen op basis van het hoogste bestaande nummer.
        $this->db->query("SELECT COALESCE(MAX(Nummer), 4000) + 1 AS nieuwNummer FROM Melding");
        $nummer = $this->db->single();

        // Titel en bericht worden samen opgeslagen in het bestaande Bericht veld.
        $this->db->query("
            INSERT INTO Melding (BezoekerId, MedewerkerId, Nummer, Type, Bericht, Isactief)
            VALUES (NULL, NULL, :nummer, :type, :bericht, 1)
        ");
        $this->db->bind(':nummer', $nummer->nieuwNummer, PDO::PARAM_INT);
        $this->db->bind(':type', $data['doelgroep']);
        $this->db->bind(':bericht', $data['titel'] . ' - ' . $data['bericht']);
        return $this->db->execute();
    }

    public function forceInsertDatabaseError($data)
    {
        // Unhappy scenario: insert naar een niet-bestaande tabel.
        $this->db->query("
            INSERT INTO NietBestaandeMeldingenTabel (Titel, Bericht, Doelgroep)
            VALUES (:titel, :bericht, :doelgroep)
        ");
        $this->db->bind(':titel', $data['titel']);
        $this->db->bind(':bericht', $data['bericht']);
        $this->db->bind(':doelgroep', $data['doelgroep']);
        return $this->db->execute();
    }
}
