<?php
require "database.php";
include 'validation.php';
require_once 'captcha/Captcha_verify.php';
if (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["email"]) && isset($_POST["confirm_password"]) && isset($_POST['captcha'])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $enc_password = md5($confirm_password);
    $email = $_POST["email"];
    $verify_captcha = json_decode(verifyCaptcha($_POST['captcha']), true);
    if (!validation($username, $confirm_password)) {
        $error_message = "rspecial_char";
        header("Location: index.php?" . $error_message);
        exit;
    }
    if ($verify_captcha['captcha_status'] == 200) {
        if (checkValid($conn, $username)) {
            $password = trimAndCheckNull($password);
            $confirm_password = trimAndCheckNull($confirm_password);
            $username = trimAndCheckNull($username);
            if (!is_null($password) || !is_null($confirm_password) || !is_null($username)) {
                if (strlen($password) >= 8) {
                    if ($confirm_password === $password) {
                        $sql = "INSERT INTO users(id,username, password, email) VALUES (NULL, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);

                        $stmt->bind_param("sss", $username, $enc_password, $email);

                        if ($stmt->execute()) {
                            header("Location: index.php?success");
                            exit;
                        } else {
                            echo "Error 404";
                        }
                    } else {
                        $error_message = "not_match";
                        header("Location: index.php?" . $error_message);
                        exit;
                    }
                } else {
                    $error_message = "too_small";
                    header("Location: index.php?" . $error_message);
                    exit;
                }
            } else {
                $error_message = "null";
                header("Location: index.php?" . $error_message);
                exit;
            }
        } else {
            $error_message = "duplicate";
            header("Location: index.php?" . $error_message);
            exit;
        }
    } else {
        $error_message = "captcha_err";
        header("Location: index.php?" . $error_message);
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
