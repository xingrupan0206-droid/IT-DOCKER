<?php

class AccountController
{
    private $conn;

    // Database verbinding
    public function __construct($database)
    {
        $this->conn = $database;
    }

    // Alleen administrator
    public function checkAdmin()
    {
        // Niet ingelogd
        if (!isset($_SESSION['rol'])) {

            header("Location: index.php");
            exit();
        }

        // Geen administrator
        if ($_SESSION['rol'] != 'administrator') {

            header("Location: index.php");
            exit();
        }
    }

    // Alle accounts ophalen
    public function accountOverzicht()
    {
        $sql = "SELECT * FROM AccountOverzicht";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>