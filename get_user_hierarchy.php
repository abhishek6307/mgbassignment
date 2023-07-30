<?php
session_start();
require_once 'includes/db_config.php';
require_once 'includes/functions.php';


function getUserHierarchy($current_user_id) {
    global $conn;

    // Replace 'users' with your actual table name
    $query = "SELECT id, name, parent_id FROM users";

    $result = mysqli_query($conn, $query);
    $data = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $user_id = $row['id'];
        $name = $row['name'];
        $parent_id = $row['parent_id'];

        $data[$user_id] = array(
            'name' => $name,
            'children' => array()
        );

        // Check if the user has a sponsor
        if ($parent_id !== null) {
            $data[$parent_id]['children'][] = &$data[$user_id];
        }
    }

    // Find the root node (user without a sponsor)
    $root = $data[$current_user_id];

    return $root;
}

// Assuming you have the currently logged-in user's ID stored in a variable called $current_user_id
$user_hierarchy = getUserHierarchy(8);

// Output the data as JSON
header('Content-Type: application/json');
echo json_encode($user_hierarchy);

?>
