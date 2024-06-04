<?php
require 'database.php';
include('auth.php');
include 'validation.php';
include('csrfTokenHandle.php');
if (strcmp($user['user_role'], "admin") == 0) {
    if (
        isset($_POST["course_id"]) && isset($_POST["title"]) && isset($_POST["course_description"])  && isset($_POST["course_detail"]) && isset($_POST["course_slide1"]) && isset($_POST["course_slide2"])
        && isset($_POST["course_slide3"]) && isset($_POST["course_author"]) && isset($_POST["course_date"]) && isset($_POST["course_link"]) && isset($_POST["course_price"])
        && isset($_POST["course_view"]) && isset($_POST["csrfToken"])
    ) {
        $errorMsg = null;
        $error = 0;
        $course_id = intval($_POST["course_id"]);
        $title = trimAndCheckNull($_POST["title"]);
        $course_description = trimAndCheckNull($_POST["course_description"]);
        $course_detail = trimAndCheckNull($_POST["course_detail"]);
        $course_slide1 = trimAndCheckNull($_POST["course_slide1"]);
        $course_slide2 = trimAndCheckNull($_POST["course_slide2"]);
        $course_slide3 = trimAndCheckNull($_POST["course_slide3"]);
        $course_author = trimAndCheckNull($_POST["course_author"]);
        $course_date = trimAndCheckNull($_POST["course_date"]);
        $course_link = trimAndCheckNull($_POST["course_link"]);
        $course_price = trimAndCheckNull($_POST["course_price"]);
        $course_view = intval($_POST["course_view"]);
        $clientToken = $_POST["csrfToken"];
        $admin = $_SESSION["username"];
        if (checkToken($clientToken, $admin, $redis)) {

            if (
                !is_null($course_id) && !is_null($title) && !is_null($course_description) && !is_null($course_detail) && !is_null($course_slide1)
                && !is_null($course_slide2) && !is_null($course_slide3) && !is_null($course_author) && !is_null($course_link)
                && !is_null($course_price)
            ) {
                if (checkDuplicateLink($conn, $course_link, $course_id)) {
                    $sql = "UPDATE courses SET title = ?, description = ?, detail = ?, slide1 = ?, slide2 = ?, slide3 = ?, author = ?, date_created = ?,  download_link = ?,
                 price = ?, view = ? WHERE course_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param(
                        "sssssssssssi",
                        $title,
                        $course_description,
                        $course_detail,
                        $course_slide1,
                        $course_slide2,
                        $course_slide3,
                        $course_author,
                        $course_date,
                        $course_link,
                        $course_price,
                        $course_view,
                        $course_id
                    );
                    if ($stmt->execute()) {
                        $error = 0;
                        if (updateCart($conn, $course_id, $title, $course_slide1, $course_author, $course_price)) {
                            header("Location: course_manage.php");
                            refreshToken($admin, $redis);
                            exit;
                        } else {
                            echo "404";
                        }
                    } else {
                        printf("Error: %s.\n", $stmt->error);
                    }
                } else {
                    $errorMsg = "duplicate";
                    $error = 1;
                }
            } else {
                $errorMsg = "null_var";
                $error = 1;
            }


            if ($error > 0) {
                header("Location:course_manage.php?" . $errorMsg);
                exit;
            }
        } else {
            header("Location:index.php");
            exit;
        }
    }
}
function checkDuplicateLink($conn, $course_link, $course_id)
{
    $sql = "SELECT * FROM `courses` WHERE download_link = ? AND NOT course_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $course_link, $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return false;
    } else {
        return true;
    }
}
function trimAndCheckNull($string)
{
    // Loại bỏ khoảng trắng ở đầu và cuối chuỗi
    $trimmedString = trim($string);

    // Kiểm tra xem chuỗi đã được cắt tỉa có null hay khônga
    if (is_null($trimmedString) || empty($trimmedString)) {
        return null;
    } else {
        // Trả về chuỗi đã được cắt tỉa và loại bỏ null
        return $trimmedString;
    }
}
function updateCart($conn, $course_id, $title, $course_slide1, $course_author, $course_price)
{
    $sql = "UPDATE cart SET course_title = ?, course_img = ?, course_author = ?, course_price = ? WHERE course_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssi",
        $title,
        $course_slide1,
        $course_author,
        $course_price,
        $course_id
    );
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}
