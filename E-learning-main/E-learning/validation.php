<?php

function validation($username, $password) {
    $check = '/^[A-Za-z0-9]+$/';
    if (!preg_match($check,$username) || !preg_match($check,$password)) {
        return false;
    } else {
        return true;
    }
    
}