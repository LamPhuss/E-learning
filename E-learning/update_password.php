<?php
require "database.php";
include 'validation.php';
require 'redis.php';
if (isset($_POST["userId"]) && isset($_POST["password"]) && isset($_POST["confirm_password"])) {
    $userId = $_POST["userId"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $enc_password = md5($confirm_password);
    $key = $redis->get($userId);
    if (!validation($password, $confirm_password)) {
        header("Location: handle_forgot_password.php?p=" . $userId . "&&v=" . $key . "&&special_char");
        exit;
    }
    $password = trimAndCheckNull($password);
    $confirm_password = trimAndCheckNull($confirm_password);
    $userId = $userId;
    if (!is_null($password) && !is_null($confirm_password) && $userId>0) {
        if ($confirm_password === $password) {
            $sql = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $password_enc = md5($password);
            $stmt->bind_param("si", $password_enc, $userId);
            if ($stmt->execute()) {
                $error = 0;
                header("Location: index.php?update_pass");
                $redis->del($userId);
                exit;
            } else {
                echo "<h1>Update error</h1>";
            }
        } else {
            header("Location: handle_forgot_password.php?p=" . $userId . "&&v=" . $key . "&&not_match");
            exit;
        }
    } else {
        header("Location: handle_forgot_password.php?p=" . $userId . "&&v=" . $key . "&&null");
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
function checkValid($conn, $userId)
{


    $sql = "SELECT * FROM `users` WHERE id=?";
    $stmt = $conn->prepare($sql);


    $stmt->bind_param("i", $userId);


    $stmt->execute();


    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return false;
    } else {
        return true;
    }
}
