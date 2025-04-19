<?php
include('config.php');

echo "<h1>Adding Missing Column to mock_atmpt_list Table</h1>";

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

echo "<p>Database update completed.</p>";
echo "<p><a href='students/mock_exams.php'>Return to Mock Exams</a></p>";
