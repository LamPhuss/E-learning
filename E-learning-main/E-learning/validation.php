<?php

function validation($username) {

    $check = '/^[A-Za-z0-9]+$/';
    if (!preg_match($check,$username)) {
        return false;
    } else {
        return true;
    }
}

function validPass($password) {
    $check_pass = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
    if (!preg_match ($check_pass, $password)) {
        return false;
    } else {
        return true;
    }
}
