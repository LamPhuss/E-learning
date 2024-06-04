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
            session_start();
            $_SESSION["username"] = $username;
            $_SESSION["password"] = $enc_password;
            $_SESSION['LAST_ACTIVITY'] = time();
            $token = bin2hex(random_bytes(16));
            $redis->hSet($username, "csrfToken", $token);
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
