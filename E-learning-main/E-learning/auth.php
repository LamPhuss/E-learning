<?php
require 'database.php';
require 'redis.php';
$lifetime = 86400;
$maxlifetime = time() + $lifetime;
$path = '/';
$domain = '.ngrok-free.app';
$secure = true;
$httponly = true;
$samesite = 'lax';
session_set_cookie_params(array(
    'lifetime' => $maxlifetime,
    'path' => $path,
    'domain' => $domain,
    'secure' => true,
    'httponly' => true,
    'samesite' => $samesite
));
session_start();
if (isset($_SESSION["username"]) && isset($_SESSION["password"]) && isset($_SESSION['session_id'])) {
    if (isset($_SESSION['LAST_ACTIVITY'])) {
        $timeout = 86400;
        check_timeout($timeout, $_SESSION['LAST_ACTIVITY']);
        $sessionid = $_SESSION['session_id'];
        $username = $_SESSION["username"];
        $cookie = $_COOKIE['PHPSESSID'];
        if (checkSession($redis, $sessionid, $cookie, $username)) {
            $password = $_SESSION["password"];
            $sql = "SELECT * FROM users WHERE username=? and password=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
            } else {
                var_dump($sessionid);
                die(header("Location: index.php"));
            }
        } else {
            die(header("Location: index.php"));
        }
    } else {
        die(header("Location: index.php"));
    }
} else {
    die(header("Location: index.php"));
}
function check_timeout($timeout, $SESSION)
{
    if (isset($_SESSION['LAST_ACTIVITY'])) {
        if ((time() - $_SESSION['LAST_ACTIVITY']) > $timeout) {
            session_destroy();
        }
        $_SESSION['LAST_ACTIVITY'] = time();
    } else {
        session_destroy();
    }
}
function checkSession($redis, $sessionid,$cookie, $username)
{
    $sessions = $redis->hGetAll($username);
    $sessionidcheck = $sessions['sessionid'];
    $cookiecheck = $sessions['cookie'];
    if (strcmp($sessionid, $sessionidcheck) === 0 && strcmp($cookiecheck, $cookie)  === 0 ) {
        return true;
    } else {
        return false;
    }
}
