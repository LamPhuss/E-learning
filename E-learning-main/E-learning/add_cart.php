<?php
require 'database.php';
include('auth.php');

if (
    isset($_POST["course_id"])  && isset($_POST["course_title"])
    && isset($_POST["course_img"]) && isset($_POST["course_author"]) && isset($_POST["course_price"])
) {
    $username = $user["username"];
    $course_id = intval($_POST["course_id"]);
    $course_title = $_POST["course_title"];
    $course_img = $_POST["course_img"];
    $course_author = $_POST["course_author"];
    $course_price = floatval($_POST["course_price"]);

    if (checkValidCourse($conn, $course_id, $course_title, $course_img, $course_author, $course_price)) {
        if (checkExistedCoursed($conn, $course_id, $username)) {
            header("Location: learning.php?course_id=" . $course_id. "&not_added");
            exit;
        } else {
            $sql = "INSERT INTO cart(cart_id, course_id, course_title, course_img, course_author, course_price, username) VALUES (NULL, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssds", $course_id, $course_title, $course_img, $course_author, $course_price, $username);
            if ($stmt->execute()) {
                header("Location: learning.php?course_id=" . $course_id);
                exit;
            } else {
                echo "<h1>Error 404</h1>";
            }
        }
    } else {
        echo "<h1>ERROR :UNKNOWN course </h1>";
    }
}
function checkValidCourse($conn, $course_id, $course_title, $course_img, $course_author, $course_price)
{


    $sql = "SELECT * FROM courses WHERE course_id=? AND title=? AND slide1=? AND author=? AND price=? ";
    $stmt = $conn->prepare($sql);

    $stmt->bind_param("isssd", $course_id, $course_title, $course_img, $course_author, $course_price);

    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}
function checkExistedCoursed($conn, $course_id, $username)
{

    $sql = "SELECT * FROM cart WHERE course_id=? AND username=? ";
    $stmt = $conn->prepare($sql);

    $stmt->bind_param("is", $course_id, $username);

    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}
