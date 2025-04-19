<?php
include('config.php');

// Function to check if table exists
function tableExists($tableName, $connection)
{
    $result = mysqli_query($connection, "SHOW TABLES LIKE '$tableName'");
    return mysqli_num_rows($result) > 0;
}

// Create read_messages table if it doesn't exist
if (!tableExists('read_messages', $conn)) {
    $sql = "CREATE TABLE `read_messages` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `message_id` int(11) NOT NULL,
        `uname` varchar(100) NOT NULL,
        `read_date` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `message_user` (`message_id`,`uname`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    if (mysqli_query($conn, $sql)) {
        echo "Table read_messages created successfully<br>";
    } else {
        echo "Error creating table read_messages: " . mysqli_error($conn) . "<br>";
    }
}

// Add message_id column to message table if it doesn't exist
$check_column = mysqli_query($conn, "SHOW COLUMNS FROM message LIKE 'id'");
if (mysqli_num_rows($check_column) == 0) {
    $sql = "ALTER TABLE message ADD COLUMN id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
    if (mysqli_query($conn, $sql)) {
        echo "Column 'id' added to message table<br>";
    } else {
        echo "Error adding column to message table: " . mysqli_error($conn) . "<br>";
    }
}

echo "<br>Read messages tracking system is now set up. <a href='index.php'>Return to homepage</a>";
?> 