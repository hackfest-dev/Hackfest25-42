<?php
include('config.php');

echo "<h1>Updating Mock Exam Tables for Integrity Tracking</h1>";

// Check if the mock_atmpt_list table exists
$table_check = "SHOW TABLES LIKE 'mock_atmpt_list'";
$table_exists = mysqli_query($conn, $table_check);

if (mysqli_num_rows($table_exists) == 0) {
    // Create the mock_atmpt_list table
    $create_table = "CREATE TABLE mock_atmpt_list (
        id INT(11) NOT NULL AUTO_INCREMENT,
        mock_exid INT(11) NOT NULL,
        uname VARCHAR(50) NOT NULL,
        nq INT(11) NOT NULL,
        cnq INT(11) NOT NULL,
        ptg DECIMAL(5,2) NOT NULL,
        status INT(11) NOT NULL DEFAULT 1,
        time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        integrity_score INT(11) DEFAULT 100,
        integrity_category VARCHAR(50) DEFAULT 'Good',
        PRIMARY KEY (id)
    )";
    
    if (mysqli_query($conn, $create_table)) {
        echo "<p>Created mock_atmpt_list table with integrity tracking columns.</p>";
    } else {
        echo "<p>Error creating mock_atmpt_list table: " . mysqli_error($conn) . "</p>";
    }
} else {
    // Check if the integrity_score column exists
    $column_check = "SHOW COLUMNS FROM mock_atmpt_list LIKE 'integrity_score'";
    $column_exists = mysqli_query($conn, $column_check);
    
    if (mysqli_num_rows($column_exists) == 0) {
        // Add the integrity_score column
        $add_column = "ALTER TABLE mock_atmpt_list ADD COLUMN integrity_score INT(11) DEFAULT 100";
        
        if (mysqli_query($conn, $add_column)) {
            echo "<p>Added integrity_score column to mock_atmpt_list table.</p>";
        } else {
            echo "<p>Error adding integrity_score column: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<p>integrity_score column already exists in mock_atmpt_list table.</p>";
    }
    
    // Check if the integrity_category column exists
    $category_check = "SHOW COLUMNS FROM mock_atmpt_list LIKE 'integrity_category'";
    $category_exists = mysqli_query($conn, $category_check);
    
    if (mysqli_num_rows($category_exists) == 0) {
        // Add the integrity_category column
        $add_category = "ALTER TABLE mock_atmpt_list ADD COLUMN integrity_category VARCHAR(50) DEFAULT 'Good'";
        
        if (mysqli_query($conn, $add_category)) {
            echo "<p>Added integrity_category column to mock_atmpt_list table.</p>";
        } else {
            echo "<p>Error adding integrity_category column: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<p>integrity_category column already exists in mock_atmpt_list table.</p>";
    }
}

// Create mock_cheat_violations table if it doesn't exist
$violations_check = "SHOW TABLES LIKE 'mock_cheat_violations'";
$violations_exists = mysqli_query($conn, $violations_check);

if (mysqli_num_rows($violations_exists) == 0) {
    // Create the mock_cheat_violations table
    $create_violations = "CREATE TABLE mock_cheat_violations (
        id INT(11) NOT NULL AUTO_INCREMENT,
        student_username VARCHAR(50) NOT NULL,
        mock_exam_id INT(11) NOT NULL,
        violation_type VARCHAR(50) NOT NULL,
        occurrence INT(11) NOT NULL,
        penalty INT(11) NOT NULL,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    )";
    
    if (mysqli_query($conn, $create_violations)) {
        echo "<p>Created mock_cheat_violations table for tracking integrity violations.</p>";
    } else {
        echo "<p>Error creating mock_cheat_violations table: " . mysqli_error($conn) . "</p>";
    }
} else {
    echo "<p>mock_cheat_violations table already exists.</p>";
}

echo "<p>Table updates completed. The mock exam system now supports integrity score tracking, fullscreen mode, and violation logging like the real exam system.</p>";
echo "<p><a href='index.php'>Return to Homepage</a></p>";
?> 