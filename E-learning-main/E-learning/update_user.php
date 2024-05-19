<?php
require 'database.php';
include('auth.php');

if (
    isset($_POST["email"])  && isset($_POST["phone_num"])
    && isset($_POST["address"])
) {
    $username = $_SESSION["username"];
    $email = $_POST["email"];
    $phone = $_POST["phone_num"];
    $address = $_POST["address"];
    $sql = "UPDATE users SET email = ?, phone = ?, address = ? WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $email, $phone, $address, $username);
    if ($stmt->execute()) {
        header("Location:user_profile.php");
        exit;
    } else {
        echo "<h1>Error 404</h1>";
    }
}
?>