<?php

class Database {

    private $dbHost = DB_HOST;
    private $dbName = DB_NAME;
    private $dbUser = DB_USER;
    private $dbPass = DB_PASS;

    private $dbHandler;
    private $statement;

    public function __construct() {
        $conn = 'mysql:host=' . $this->dbHost . ';dbname=' . $this->dbName . ';charset=utf8';

        $options = [
            PDO::ATTR_PERSISTENT         => true,
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        ];

        try {
            $this->dbHandler = new PDO($conn, $this->dbUser, $this->dbPass, $options);
        } catch (PDOException $e) {
            die('Databasefout: ' . $e->getMessage());
        }
    }

    public function query($sql) {
        $this->statement = $this->dbHandler->prepare($sql);
    }

    public function bind($parameter, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):   $type = PDO::PARAM_INT; break;
                case is_bool($value):  $type = PDO::PARAM_BOOL; break;
                case is_null($value):  $type = PDO::PARAM_NULL; break;
                default:               $type = PDO::PARAM_STR;
            }
        }
        $this->statement->bindValue($parameter, $value, $type);
    }

    public function execute() {
        return $this->statement->execute();
    }

    public function resultSet() {
        $this->statement->execute();
        return $this->statement->fetchAll(PDO::FETCH_OBJ);
    }

    public function single() {
        $this->statement->execute();
        $result = $this->statement->fetch(PDO::FETCH_OBJ);
        $this->statement->closeCursor();
        return $result;
    }

    public function rowCount() {
        return $this->statement->rowCount();
    }

    // Transactie methoden
    public function getConnection() {
        return $this->dbHandler;
    }

    public function beginTransaction() {
        return $this->dbHandler->beginTransaction();
    }

    public function commit() {
        return $this->dbHandler->commit();
    }

    public function rollBack() {
        return $this->dbHandler->rollBack();
    }
}