<?php
require 'database.php';
include 'validation.php';

require 'redis.php';
if (isset($_POST["username"]) && isset($_POST["password"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $enc_password = md5($password);
    if (!validation($username, $password)) {
        $error_message = "lspecial_char";
        header("Location: index.php?" . $error_message);
        exit;
    };



    if (checkUsername($conn, $username)) {
        $sql = "SELECT username,password FROM users WHERE username =? AND password =?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $enc_password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $lifetime=86400;
            $maxlifetime = time() + $lifetime;
            $path = '/';
            $domain = '.ngrok-free.app';
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
            $_SESSION["username"] = $username;
            $_SESSION["password"] = $enc_password;
            $_SESSION['LAST_ACTIVITY'] = time();
            $token = bin2hex(random_bytes(16));
            $redis->hSet($username, "csrfToken", $token);
            $sessionid = bin2hex(random_bytes(32));
            $_SESSION['session_id'] = $sessionid;
            $redis->hSet($username, "sessionid", $sessionid);
            $redis->hSet($username, "cookie", $_COOKIE['PHPSESSID']);
            header("Location: start.php");
            exit;
        } else {
            $error_message = "log_err";
            header("Location: index.php?" . $error_message);
            exit;
        }
    } else {
        $error_message = "log_err";
        header("Location: index.php?" . $error_message);
        exit;
    }
}
function checkUsername($conn, $username)
{
    $sql = "SELECT * FROM `users` WHERE username = ? ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}

