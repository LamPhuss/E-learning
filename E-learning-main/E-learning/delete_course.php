<?php
require 'database.php';
include('auth.php');
include('csrfTokenHandle.php');

if (strcmp($user['user_role'], "admin") == 0) {
    if (isset($_POST["checkedIds"]) && isset($_POST["csrfToken"])) {
        $clientToken = $_POST["csrfToken"];
        $admin = $_SESSION["username"];
        if (checkToken($clientToken, $admin, $redis)) {

            $checkedIds = explode(',', $_POST["checkedIds"]);
            foreach ($checkedIds as $id) {
                $sql = "DELETE FROM `courses` WHERE course_id = ?";
                $stmt = $conn->prepare($sql);
                $id = intval($id);
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    if (deleteCartCourse($conn, $id)) {
                        echo "Delete complete";
                        refreshToken($admin, $redis);
                    } else {
                        echo "Error 404";
                    }
                } else {
                    echo "Error 404";
                }
            }
        } else {
            header("Location:index.php");
            exit;
        }
    }
}

function deleteCartCourse($conn, $course_id)
{
    $sql = "DELETE FROM `cart` WHERE course_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $course_id);
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}
