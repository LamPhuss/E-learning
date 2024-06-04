<?php
require 'database.php';
include('auth.php');
include 'validation.php';
include('csrfTokenHandle.php');
if (
    isset($_POST["user_id"]) && isset($_POST["username"]) && isset($_POST["email"])  && isset($_POST["password"]) && isset($_POST["phone"]) && isset($_POST["address"]) && isset($_POST["csrfToken"])
) {
    $errorMsg = null;
    $error = 0;
    $user_id = intval($_POST["user_id"]);
    $email = trimAndCheckNull($_POST["email"]);
    $admin = $_SESSION["username"];
    $username = trimAndCheckNull($_POST["username"]);
    $password = trimAndCheckNull($_POST["password"]);
    $phone = trimAndCheckNull($_POST["phone"]);
    $address = trimAndCheckNull($_POST["address"]);
    $clientToken = $_POST["csrfToken"];
    if (checkToken($clientToken, $admin, $redis)) {
        if (!is_null($username) && !is_null($email) && !is_null($password)) {
            if (checkDuplicateUsername($conn, $username, $user_id)) {
                if (validation($username, $password)) {
                    $sql = "UPDATE users SET username = ?, email = ?, password = ?, phone = ?, address = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $password_enc = md5($password);
                    $stmt->bind_param("sssssi", $username, $email, $password_enc, $phone, $address, $user_id);
                    if ($stmt->execute()) {
                        $error = 0;
                        refreshToken($admin,$redis);
                        header("Location: user_manage.php");
                        exit;
                    } else {
                        echo "<h1>Update error</h1>";
                    }
                } else {
                    $errorMsg = "special_char";
                    $error = 1;
                }
            } else {
                $errorMsg = "duplicate";
                $error = 1;
            }
        } else {
            $errorMsg = "null_var";
            $error = 1;
        }
    } else {
        header("Location:index.php");
        exit;
    }


    if ($error > 0) {
        header("Location:user_manage.php?" . $errorMsg);
        exit;
    }
}
function checkDuplicateUsername($conn, $username, $user_id)
{
    $sql = "SELECT * FROM `users` WHERE username = ? AND NOT id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $username, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return false;
    } else {
        return true;
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
