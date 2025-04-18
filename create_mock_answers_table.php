<?php
include('config.php');

// Create mock_qstn_ans table if it doesn't exist
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
    echo "Table mock_qstn_ans created successfully or already exists.";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}
?> 