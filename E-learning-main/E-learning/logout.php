<?php
include('auth.php');

if (isset($_SESSION["admin"])) {
    header("Location: admin.php");
} else {
    header("Location: index.php");
}

session_destroy();

exit;
?>
