<?php
require 'database.php';
session_start();
/*
if (!isset($_SESSION["username"]))
    die(header("Location: index.php"));*/
if (isset($_SESSION["username"])) {
    $username = $_SESSION["username"];
    $sql = "SELECT user_role FROM users WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
    } else {
        echo "<h1>404</h1>";
    }
} else {
    die(header("Location: index.php"));
}
