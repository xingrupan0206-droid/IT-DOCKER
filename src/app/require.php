<?php
 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
 
require_once '../app/config/config.php';
require_once '../app/libraries/Database.php';
require_once '../app/libraries/BaseController.php';
require_once '../app/libraries/Core.php';
 
$init = new Core();