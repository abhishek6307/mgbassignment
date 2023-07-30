<?php
// Replace with your actual database credentials
$db_host = "localhost";
$db_name = "mgbassignment";
$db_user = "root";
$db_password = "";

$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
