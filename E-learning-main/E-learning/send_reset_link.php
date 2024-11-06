<?php
require 'redis.php';
require "database.php";
require './vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

$time_period = 900;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["username"]) && isset($_POST["email"])) {
        $username = $_POST["username"];
        $email = $_POST["email"];
        if (!validation($username)) {
            header("Location: forgot_password.php?special_char");
            exit;
        }
        $user = checkUsername($conn, $username);
        if (!is_null($user)) {
            $userId = $user['id'];
            if (!$redis->exists($userId)) {
                $key = bin2hex(random_bytes(16));
                $redis->set($userId, $key);
                $redis->expire($userId, $time_period);
            }
            $sql = "SELECT * FROM `users` WHERE username = ? and email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username,$email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $user_detail = $result->fetch_assoc();
                $email = $user_detail['email'];

                require './vendor/phpmailer/phpmailer/src/Exception.php';
                require './vendor/phpmailer/phpmailer/src/PHPMailer.php';
                require './vendor/phpmailer/phpmailer/src/SMTP.php';
                $mail = new PHPMailer(true);


                // SMTP config
                $mail->isSMTP();
                $mail->Host = "";
                $mail->SMTPAuth = true;
                $mail->Username = "api";
                $mail->Password = "";
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                // Sender and recipient settings
                $mail->setFrom('', 'Elearning Web');
                $mail->addReplyTo('', 'Elearning Web');
                $mail->addAddress($email, $username); // Replace with recipient's email and name

                // Setting the email content
                $mail->isHTML(true); // Set email format to plain text
                $mail->Subject = 'Reset password link';
                $mail->Body = "<p>Here is your link to reset passwort: <a href='http://localhost:80/handle_forgot_password.php?p=" . $userId . "&&v=" . $redis->get($userId) . "'>link to reset password</a></p><p>If you have any problems, please contact us via email: elearninghust@gmail.com</p>";
                $mail->send();
                header("Location:forgot_password.php?send_success");
                exit;
            } else {
                header("Location:forgot_password.php?uname_err");
                exit;
            }
        } else {
            header("Location:forgot_password.php?uname_err");
            exit;
        }
    } else {
        header("Location:forgot_password.php?null");
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
        $user = $result->fetch_assoc();
        return $user;
    } else {
        return null;
    }
}
function validation($username)
{
    $check = '/^[A-Za-z0-9]+$/';
    if (!preg_match($check, $username)) {
        return false;
    } else {
        return true;
    }
} ?>
