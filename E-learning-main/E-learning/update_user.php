<?php
require 'database.php';
include('auth.php');
include('csrfTokenHandle.php');
if (
    isset($_POST["email"])  && isset($_POST["phone_num"])
    && isset($_POST["address"]) && isset($_POST["csrfToken"])
) {
    $username = $user["username"];
    $email = $_POST["email"];
    $phone = $_POST["phone_num"];
    $address = $_POST["address"];
    $tokens = $redis->hGetAll($username);
    $clientToken = $_POST["csrfToken"];
    if (isset($phone)) {
        if (!phoneValidation($phone)) {
            header("Location:user_profile_edit.php?phoneNum_err");
            exit;
        }
    }
    if (checkToken($clientToken, $username,$redis)) {
        $sql = "UPDATE users SET email = ?, phone = ?, address = ? WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $email, $phone, $address, $username);
        if ($stmt->execute()) {
            refreshToken($username,$redis);
            header("Location:user_profile.php");
            exit;
        } else {
            echo "<h1>Error 404</h1>";
        }
    } else {
        header("Location:index.php");
    }
}
function phoneValidation($phone)
{
    $check = '/^[0-9]+$/';
    if (preg_match($check, $phone)) {
        return true;
    } else {
        return false;
    }
}