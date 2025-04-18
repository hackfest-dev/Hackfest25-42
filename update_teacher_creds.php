<?php
include 'config.php';

// New credentials
$new_username = "teacher";
$new_password = "teacher123"; // This will be hashed

// Hash the new password
$hashed_password = md5($new_password);

// Update the credentials
$update_query = "UPDATE teacher SET uname = '$new_username', pword = '$hashed_password' WHERE id = 1";

if (mysqli_query($conn, $update_query)) {
    echo "Teacher credentials updated successfully!<br>";
    echo "New username: " . $new_username . "<br>";
    echo "New password: " . $new_password . "<br>";
    echo "<a href='login_teacher.php'>Go to login page</a>";
} else {
    echo "Error updating credentials: " . mysqli_error($conn);
}

mysqli_close($conn);
