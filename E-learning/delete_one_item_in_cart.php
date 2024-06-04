<?php
require 'database.php';
include('auth.php');
include('csrfTokenHandle.php');
if (isset($_POST["course_id"]) || isset($_POST["csrfToken"])) {
    $clientToken = $_POST["csrfToken"];
    $userToken = $_SESSION["username"];
    if (checkToken($clientToken, $userToken, $redis)) {
        $username = $user["username"];
        $course_id = intval($_POST["course_id"]);
        removeOneItem($conn, $course_id, $username,$userToken,$redis);
    
    } else {
        header("Location:index.php");
        exit;
    }
}



function removeOneItem($conn, $course_id, $username,$userToken,$redis)
{
    $sql = "DELETE FROM cart WHERE course_id = ? AND username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $course_id, $username);
    if ($stmt->execute()) {
        refreshToken($userToken, $redis);
        header("Location: paycheck.php");
        exit;
    } else {
        echo "<h1>Error 404</h1>";
    }
}
