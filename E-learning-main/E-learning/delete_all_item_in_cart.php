<?php
require 'database.php';
include('auth.php');

$username = $_SESSION["username"];

removeAllItem($conn, $username);



function removeAllItem($conn, $username)
{
    $sql = "DELETE FROM cart WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
}
?>