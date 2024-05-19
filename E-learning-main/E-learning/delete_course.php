<?php
require 'database.php';
include('auth.php');
include("resources/static/html/header.html");

if(strcmp($user_role['user_role'],"admin")==0){
    if(isset($_POST["checkedIds"])){       
        $checkedIds = explode(',', $_POST["checkedIds"]);
        foreach ($checkedIds as $id) {
            $sql = "DELETE FROM `courses` WHERE course_id = ?";
            $stmt = $conn->prepare($sql);
            $id = intval($id);
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                echo "Delete complete";
            } else {
                echo "Error 404";
            }
        }
    }
}


?>
