<?php
session_start();
require_once 'includes/db_config.php';
require_once 'includes/functions.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$login_error = "";

if (isset($_POST['login'])) {
    $username = sanitize_input($_POST['username']);
    $password = sanitize_input($_POST['password']);

    if (preg_match('/^\d{10}$/', $username)) {
        // It's a mobile number
        $where = "mobile_no = '$username'";
    } else {
        // It's a sponsor ID
        $where = "sponsor_id = '$username'";
    }

    $sql = "SELECT * FROM users WHERE $where";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $user_data = mysqli_fetch_assoc($result);
        $hashed_password = $user_data['password'];

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_data['parent_id'];
            $_SESSION['username'] = $user_data['name'];
            $_SESSION['id'] = $user_data['id'];
            // Assuming 'name' is the column storing the user's name
            // Add other relevant data to the session
            header("Location: dashboard.php");
            exit();
        } else {
            $login_error = "Invalid password";
        }
    } else {
        $login_error = "Invalid mobile number/sponsor ID or password";
    }
}
?>



<!DOCTYPE html>
<html>

<head>
    <title>Login Page</title>
   
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <div class="container">

        <div class="row justify-content-center">
     <div class="col-8 mt-3">
           <?php
                        if (!empty($login_error)) {
                            echo '<div class="alert alert-danger">' . $login_error . '</div>';
                        }
                        ?>
     </div>
            <div class="col-md-6 mt-5">
                <form action="" method="post" class="form-group">
                    <h2 class="text-center">Login</h2>
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                    <button type="submit" name="login" class="btn btn-primary btn-block mt-3">Login</button>
                </form>
                <p class="text-center">Don't have an account? <a href="signup.php">Sign Up</a></p>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>
