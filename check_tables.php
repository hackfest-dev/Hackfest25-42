<?php
include('config.php');

echo "<h1>Database Table Verification</h1>";

// Check if mock_qstn_ans table exists and has correct structure
$result = mysqli_query($conn, "SHOW TABLES LIKE 'mock_qstn_ans'");
if (mysqli_num_rows($result) == 0) {
    echo "<p style='color: red;'>Error: mock_qstn_ans table does not exist!</p>";
    
    // Create the table
    $sql = "CREATE TABLE IF NOT EXISTS mock_qstn_ans (
        id INT(11) NOT NULL AUTO_INCREMENT,
        mock_exid INT(11) NOT NULL,
        uname VARCHAR(50) NOT NULL,
        sno INT(11) NOT NULL,
        ans VARCHAR(50) NOT NULL,
        datetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    )";
    
    if (mysqli_query($conn, $sql)) {
        echo "<p style='color: green;'>Fixed: mock_qstn_ans table created successfully.</p>";
    } else {
        echo "<p style='color: red;'>Failed to create mock_qstn_ans table: " . mysqli_error($conn) . "</p>";
    }
} else {
    echo "<p style='color: green;'>mock_qstn_ans table exists.</p>";
    
    // Check if table has correct structure
    $result = mysqli_query($conn, "DESCRIBE mock_qstn_ans");
    $columns = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $columns[$row['Field']] = $row;
    }
    
    $required_columns = ['id', 'mock_exid', 'uname', 'sno', 'ans', 'datetime'];
    $missing_columns = [];
    
    foreach ($required_columns as $column) {
        if (!isset($columns[$column])) {
            $missing_columns[] = $column;
        }
    }
    
    if (count($missing_columns) > 0) {
        echo "<p style='color: red;'>Error: The following columns are missing from mock_qstn_ans table: " . implode(', ', $missing_columns) . "</p>";
    } else {
        echo "<p style='color: green;'>mock_qstn_ans table has correct structure.</p>";
    }
}

// Check if mock_atmpt_list table exists
$result = mysqli_query($conn, "SHOW TABLES LIKE 'mock_atmpt_list'");
if (mysqli_num_rows($result) == 0) {
    echo "<p style='color: red;'>Error: mock_atmpt_list table does not exist!</p>";
} else {
    echo "<p style='color: green;'>mock_atmpt_list table exists.</p>";
    
    $result = mysqli_query($conn, "DESCRIBE mock_atmpt_list");
    $columns = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $columns[$row['Field']] = $row;
    }
    
    // Check for required columns in mock_atmpt_list
    $required_columns = ['id', 'mock_exid', 'uname', 'nq', 'cnq', 'ptg', 'status', 'integrity_score'];
    $missing_columns = [];
    
    foreach ($required_columns as $column) {
        if (!isset($columns[$column])) {
            $missing_columns[] = $column;
        }
    }
    
    if (count($missing_columns) > 0) {
        echo "<p style='color: red;'>Error: The following columns are missing from mock_atmpt_list table: " . implode(', ', $missing_columns) . "</p>";
    } else {
        echo "<p style='color: green;'>mock_atmpt_list table has correct structure.</p>";
    }
}

// Check if mock_qstn_list table exists
$result = mysqli_query($conn, "SHOW TABLES LIKE 'mock_qstn_list'");
if (mysqli_num_rows($result) == 0) {
    echo "<p style='color: red;'>Error: mock_qstn_list table does not exist!</p>";
} else {
    echo "<p style='color: green;'>mock_qstn_list table exists.</p>";
}

// Check for existing records in the tables
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM mock_qstn_list");
$row = mysqli_fetch_assoc($result);
echo "<p>mock_qstn_list has " . $row['count'] . " questions.</p>";

$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM mock_atmpt_list");
$row = mysqli_fetch_assoc($result);
echo "<p>mock_atmpt_list has " . $row['count'] . " attempts.</p>";

$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM mock_qstn_ans");
$row = mysqli_fetch_assoc($result);
echo "<p>mock_qstn_ans has " . $row['count'] . " answers.</p>";

echo "<p><a href='index.php'>Return to home</a></p>";
?> 