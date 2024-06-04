<?php
require 'database.php';
require 'redis.php';
session_start();
if (isset($_SESSION["username"]) && isset($_SESSION["password"])) {
    if (isset($_SESSION['LAST_ACTIVITY'])) {
        $timeout = 86400;
        check_timeout($timeout, $_SESSION['LAST_ACTIVITY']); 
        $username = $_SESSION["username"];
        $password = $_SESSION["password"];
        $sql = "SELECT * FROM users WHERE username=? and password=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
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
