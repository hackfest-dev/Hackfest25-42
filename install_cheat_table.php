<?php
// install_cheat_table.php - Updated with correct database name

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";

// First connect without specifying a database
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("<div class='error'>Connection failed: " . $conn->connect_error . "</div>");
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS db_eval";
if ($conn->query($sql) === TRUE) {
    echo "<div class='success'>Database 'db_eval' created or already exists.</div>";
} else {
    die("<div class='error'>Error creating database: " . $conn->error . "</div>");
}

// Close the initial connection
$conn->close();

// Connect to the database
$dbname = "db_eval";
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("<div class='error'>Connection failed: " . $conn->connect_error . "</div>");
}

// SQL to create table
$sql = "CREATE TABLE IF NOT EXISTS cheat_violations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_username VARCHAR(100) NOT NULL,
    exam_id INT NOT NULL,
    violation_type VARCHAR(50) NOT NULL,
    occurrence INT NOT NULL,
    penalty INT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// Execute query
if ($conn->query($sql) === TRUE) {
    echo "<div class='success'>Table 'cheat_violations' created successfully!</div>";
} else {
    echo "<div class='error'>Error creating table: " . $conn->error . "</div>";
}

// Close connection
$conn->close();
?>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 40px;
        line-height: 1.6;
    }
    .success {
        padding: 15px;
        background-color: #d4edda;
        color: #155724;
        border-radius: 4px;
        margin-bottom: 15px;
    }
    .error {
        padding: 15px;
        background-color: #f8d7da;
        color: #721c24;
        border-radius: 4px;
        margin-bottom: 15px;
    }
</style>