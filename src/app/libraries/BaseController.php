<?php

class BaseController {

    public function model($model) {
        require_once APPROOT . '/models/' . $model . '.php';
        return new $model();
    }

    public function view($view, $data = []) {
        if (file_exists(APPROOT . '/views/' . $view . '.php')) {
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    ${$key} = $value;
                }
            }
            require_once APPROOT . '/views/' . $view . '.php';
        } else {
            die('View niet gevonden: ' . $view);
        }
    }
}