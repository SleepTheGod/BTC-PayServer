<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Update user info in the database (add your database connection and logic here)
    // Example: 
    // $update_query = "UPDATE users SET username='$username', email='$email', password=SHA('$password') WHERE user_id='$_SESSION['user_id']'";
    // Run the update query...

    echo "Account settings updated successfully.";
} else {
    echo "Invalid request method.";
}
?>
