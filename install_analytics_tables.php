<?php
include 'config.php';

echo "<h2>Installing Analytics Tables</h2>";

// Create student_answers table
$student_answers_sql = "CREATE TABLE IF NOT EXISTS `student_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attempt_id` int(100) NOT NULL,
  `exid` int(100) NOT NULL,
  `qid` int(11) NOT NULL,
  `uname` varchar(100) NOT NULL,
  `selected_option` varchar(100) NOT NULL,
  `is_correct` tinyint(1) NOT NULL,
  `answer_time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `exid` (`exid`),
  KEY `qid` (`qid`),
  KEY `attempt_id` (`attempt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

// Create question_options table
$question_options_sql = "CREATE TABLE IF NOT EXISTS `question_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `qid` int(11) NOT NULL,
  `option_text` varchar(100) NOT NULL,
  `option_number` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `qid` (`qid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

// Execute the SQL statements
if (mysqli_query($conn, $student_answers_sql)) {
    echo "<p>✅ student_answers table created successfully</p>";
} else {
    echo "<p>❌ Error creating student_answers table: " . mysqli_error($conn) . "</p>";
}

if (mysqli_query($conn, $question_options_sql)) {
    echo "<p>✅ question_options table created successfully</p>";
} else {
    echo "<p>❌ Error creating question_options table: " . mysqli_error($conn) . "</p>";
}

// Modify atmpt_list table to add integrity columns if needed
$alter_atmpt_list_sql = "
ALTER TABLE `atmpt_list` 
ADD COLUMN IF NOT EXISTS `integrity_score` int(3) NOT NULL DEFAULT 100,
ADD COLUMN IF NOT EXISTS `integrity_category` varchar(50) DEFAULT 'Good'";

if (mysqli_query($conn, $alter_atmpt_list_sql)) {
    echo "<p>✅ atmpt_list table modified successfully</p>";
} else {
    echo "<p>❌ Error modifying atmpt_list table: " . mysqli_error($conn) . "</p>";
}

echo "<p>Analytics database setup complete! <a href='teachers/results.php'>Go to Results</a></p>";
?> 