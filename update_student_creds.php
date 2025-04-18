<?php
include 'config.php';

// New student credentials
$students = [
    [
        'id' => 10,
        'username' => 'student1',
        'password' => 'student123',
        'name' => 'Student One'
    ],
    [
        'id' => 11,
        'username' => 'student2',
        'password' => 'student123',
        'name' => 'Student Two'
    ],
    [
        'id' => 12,
        'username' => 'student3',
        'password' => 'student123',
        'name' => 'Student Three'
    ]
];

echo "<h2>Updating Student Credentials</h2>";

foreach ($students as $student) {
    // Hash the password
    $hashed_password = md5($student['password']);

    // Update the credentials
    $update_query = "UPDATE student SET 
        uname = '{$student['username']}', 
        pword = '$hashed_password',
        fname = '{$student['name']}'
        WHERE id = {$student['id']}";

    if (mysqli_query($conn, $update_query)) {
        echo "<div style='margin: 10px; padding: 10px; background: #d4edda; border-radius: 5px;'>";
        echo "Student credentials updated successfully!<br>";
        echo "ID: " . $student['id'] . "<br>";
        echo "Name: " . $student['name'] . "<br>";
        echo "Username: " . $student['username'] . "<br>";
        echo "Password: " . $student['password'] . "<br>";
        echo "</div>";
    } else {
        echo "<div style='margin: 10px; padding: 10px; background: #f8d7da; border-radius: 5px;'>";
        echo "Error updating student " . $student['id'] . ": " . mysqli_error($conn);
        echo "</div>";
    }
}

echo "<br><a href='login_student.php' style='padding: 10px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Go to Student Login</a>";

mysqli_close($conn);
