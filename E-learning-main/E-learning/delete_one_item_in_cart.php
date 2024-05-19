<?php
require 'database.php';
include('auth.php');
if (isset($_POST["course_id"])) {
    $username = $_SESSION["username"];
    $course_id = intval($_POST["course_id"]);
    removeOneItem($conn, $course_id, $username);
}



function removeOneItem($conn, $course_id, $username)
{
    $sql = "DELETE FROM cart WHERE course_id = ? AND username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $course_id, $username);
    if($stmt->execute()){
        header("Location: paycheck.php");
        exit;
    }
    else{
        echo "<h1>Error 404</h1>";
    }
}
function removeAllItem($conn, $username)
{
    $sql = "DELETE FROM cart WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
}
