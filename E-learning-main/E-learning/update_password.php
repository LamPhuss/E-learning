<?php
require "database.php";
include 'validation.php';
require 'redis.php';
if (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["confirm_password"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $enc_password = md5($confirm_password);
    $key = $redis->get($username);
    if (!validation($password, $confirm_password)) {
        header("Location: handle_forgot_password.php?p=" . $username . "&&v=" . $key . "&&special_char");
        exit;
    }
    $password = trimAndCheckNull($password);
    $confirm_password = trimAndCheckNull($confirm_password);
    $username = trimAndCheckNull($username);
    if (!is_null($password) || !is_null($confirm_password) || !is_null($username)) {
        if ($confirm_password === $password) {
            $sql = "UPDATE users SET password = ? WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $password_enc = md5($password);
            $stmt->bind_param("ss", $password_enc, $username);
            if ($stmt->execute()) {
                $error = 0;
                header("Location: index.php?update_pass");
                exit;
            } else {
                echo "<h1>Update error</h1>";
            }
        } else {
            header("Location: handle_forgot_password.php?p=" . $username . "&&v=" . $key . "&&not_match");
            exit;
        }
    } else {
        header("Location: handle_forgot_password.php?p=" . $username . "&&v=" . $key . "&&null");
        exit;
    }
}
function trimAndCheckNull($string)
{
    $trimmedString = trim($string);
    if (is_null($trimmedString) || empty($trimmedString)) {
        return null;
    } else {
        return $trimmedString;
    }
}
function checkValid($conn, $username)
{


    $sql = "SELECT * FROM `users` WHERE username=?";
    $stmt = $conn->prepare($sql);


    $stmt->bind_param("s", $username);


    $stmt->execute();


    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return false;
    } else {
        return true;
    }
}
