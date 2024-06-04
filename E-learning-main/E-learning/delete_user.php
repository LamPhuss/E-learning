<?php
require 'database.php';
include('auth.php');
include('csrfTokenHandle.php');
if (strcmp($user['user_role'], "admin") === 0) {
    if (isset($_POST["checkedIds"]) && isset($_POST["csrfToken"])) {
        $clientToken = $_POST["csrfToken"];
        $admin = $_SESSION["username"];
        if (checkToken($clientToken, $admin, $redis)) {
            $checkedIds = explode(',', $_POST["checkedIds"]);
            foreach ($checkedIds as $id) {
                $sql = "DELETE FROM `users` WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $id = intval($id);
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    echo "Delete complete";
                    refreshToken($admin,$redis);
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

include("resources/static/html/header.html");
