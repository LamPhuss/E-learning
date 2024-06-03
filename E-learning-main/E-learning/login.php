<?php
require 'database.php';
include 'validation.php';
require_once 'captcha/Captcha_verify.php';

if (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST['captcha'])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $enc_password = md5($password);
    if (!validation($username, $password)) {
        $error_message = "lspecial_char";
        header("Location: index.php?" . $error_message);
        exit;
    };
    $verify_captcha = json_decode(verifyCaptcha($_POST['captcha']), true);

    if ($verify_captcha['captcha_status'] == 200) {
        if (checkUsername($conn, $username)) {
            $sql = "SELECT username,password FROM users WHERE username =? AND password =?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username, $enc_password);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $_SESSION["username"] = $username;
                $_SESSION['LAST_ACTIVITY'] = time();
                header("Location: start.php");
                exit;
            } else {
                $error_message = "pass_err";
                header("Location: index.php?" . $error_message);
                exit;
            }
        } else {
            $error_message = "uname_err";
            header("Location: index.php?" . $error_message);
            exit;
        }
    } else {
        $error_message = "captcha_err";
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