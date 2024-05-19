<?php
$servername = "db";
$username = "admin";
$password = "password";
$database = "e-learning";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}



// ... (rest of your code)
?>