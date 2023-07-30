<?php
// ajax_check_sponsor.php
require_once 'includes/db_config.php';

if (isset($_GET['parent_sponsor_id'])) {
    $parent_id = $_GET['parent_sponsor_id'];
    // Validate the sponsor ID
    $sql = "SELECT name FROM users WHERE sponsor_id = '$parent_id'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $sponsor_name = $row['name'];
        echo $sponsor_name;
    }
}
?>
