<?php
include('auth.php');
require 'redis.php';
$redis->hSet($username, "sessionid", null);
session_destroy();
setcookie('PHPSESSID', '', -1, '/'); 
header("Location: index.php");
exit;
?>
