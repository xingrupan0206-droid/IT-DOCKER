<?php
/**
 * De database verbindingsgegevens
 */
define('DB_HOST', 'mysql');
define('DB_NAME', 'Aurora');
define('DB_USER', 'appuser');
define('DB_PASS', 'apppass');


/**
 * De naam van de virtualhost
 */
define('URLROOT', 'http://pro4.php');

/**
 * Het pad naar de folder app
 */
define('APPROOT', dirname(dirname(__FILE__)));

/**
 * Test / demo modes voor unhappy scenarios
 * Zet op true om het corresponderende unhappy scenario te forceren
 */
define('TEST_VOORSTELLINGEN_EMPTY', false);
define('TEST_TOEVOEGEN_FOUT',       false);
define('TEST_WIJZIGEN_FOUT',        false);
define('TEST_VERWIJDEREN_BLOKKEER', false);
define('TEST_TICKETS_VOL',          false);
