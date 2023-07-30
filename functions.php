<?php
function sanitize_input($input)
{
    global $conn;
    return mysqli_real_escape_string($conn, trim($input));
}
?>
