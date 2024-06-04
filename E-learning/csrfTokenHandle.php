<?php
require 'redis.php';

function checkToken($clientToken, $username,$redis){
    $tokens = $redis->hGetAll($username);
    $csrfToken = $tokens['csrfToken'];
    if (strcmp($csrfToken, $clientToken) === 0){
        return true;
    }
    else {
        return false;
    }
};

function refreshToken($username,$redis){
    $token = bin2hex(random_bytes(16));
    $redis->hSet($username, "csrfToken", $token);
}
?>