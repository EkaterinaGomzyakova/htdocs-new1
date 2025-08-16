<?php

if (!function_exists('dump')) {
    function dump($data, $allUsers = false, $die = false)
    {
        global $USER;

        if ($USER->IsAdmin() || $allUsers) {
            echo '<pre style="padding: 10px; background: #fff; color: #000; border: 1px solid #777; border-radius: 4px; text-align: left;">';
            print_r($data);
            echo '</pre>';
        }

        if ($die) {
            die();
        }
    }
}
