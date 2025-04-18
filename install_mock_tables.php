<?php
include('config.php');

// Function to check if table exists
function tableExists($tableName, $connection)
{
    $result = mysqli_query($connection, "SHOW TABLES LIKE '$tableName'");
    return mysqli_num_rows($result) > 0;
}

// Create mock_exm_list table if it doesn't exist
if (!tableExists('mock_exm_list', $conn)) {
    $sql = "CREATE TABLE `mock_exm_list` (
        `mock_exid` int(100) NOT NULL AUTO_INCREMENT,
        `original_exid` int(100) NOT NULL,
        `mock_number` int(2) NOT NULL,
        `exname` varchar(100) NOT NULL,
        `nq` int(50) NOT NULL DEFAULT 5,
        `desp` varchar(100) NOT NULL,
        `subt` datetime NOT NULL,
        `extime` datetime NOT NULL,
        `datetime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        `subject` varchar(100) NOT NULL,
        `status` varchar(20) NOT NULL DEFAULT 'pending',
        PRIMARY KEY (`mock_exid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    if (mysqli_query($conn, $sql)) {
        echo "Table mock_exm_list created successfully<br>";
    } else {
        echo "Error creating table mock_exm_list: " . mysqli_error($conn) . "<br>";
    }
}

// Create mock_qstn_list table if it doesn't exist
if (!tableExists('mock_qstn_list', $conn)) {
    $sql = "CREATE TABLE `mock_qstn_list` (
        `mock_qid` int(11) NOT NULL AUTO_INCREMENT,
        `mock_exid` int(11) NOT NULL,
        `qstn` varchar(200) NOT NULL,
        `qstn_o1` varchar(100) NOT NULL,
        `qstn_o2` varchar(100) NOT NULL,
        `qstn_o3` varchar(100) NOT NULL,
        `qstn_o4` varchar(100) NOT NULL,
        `qstn_ans` varchar(100) NOT NULL,
        `sno` int(20) NOT NULL,
        PRIMARY KEY (`mock_qid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    if (mysqli_query($conn, $sql)) {
        echo "Table mock_qstn_list created successfully<br>";
    } else {
        echo "Error creating table mock_qstn_list: " . mysqli_error($conn) . "<br>";
    }
}

// Create mock_atmpt_list table if it doesn't exist
if (!tableExists('mock_atmpt_list', $conn)) {
    $sql = "CREATE TABLE `mock_atmpt_list` (
        `id` int(100) NOT NULL AUTO_INCREMENT,
        `mock_exid` int(100) NOT NULL,
        `uname` varchar(100) NOT NULL,
        `nq` int(100) NOT NULL,
        `cnq` int(100) NOT NULL,
        `ptg` int(100) NOT NULL,
        `status` int(10) NOT NULL,
        `integrity_score` int(3) NOT NULL DEFAULT 100,
        `subtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    if (mysqli_query($conn, $sql)) {
        echo "Table mock_atmpt_list created successfully<br>";
    } else {
        echo "Error creating table mock_atmpt_list: " . mysqli_error($conn) . "<br>";
    }
}

echo "<br>All mock exam tables have been checked/created. You can <a href='index.php'>go back to homepage</a>.";
