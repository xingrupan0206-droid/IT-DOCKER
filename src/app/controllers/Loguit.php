<?php

/**
 * Controller voor het uitloggen van de gebruiker.
 * Vernietigt de sessie en stuurt de gebruiker terug naar de homepagina.
 */
class Loguit extends BaseController {

    /**
     * Standaard actie: vernietig de sessie en stuur de gebruiker
     * terug naar de homepagina van Theater Aurora.
     */
    public function index() {
        // Verwijder alle sessievariabelen
        $_SESSION = [];

        // Vernietig de sessiecookie als die bestaat
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Vernietig de sessie volledig
        session_destroy();

        // Stuur de gebruiker terug naar de homepage
        header('Location: ' . URLROOT);
        exit();
    }
}
