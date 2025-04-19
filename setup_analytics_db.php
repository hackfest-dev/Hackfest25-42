<?php
include 'config.php';

// Create student_answers table
$student_answers_sql = "CREATE TABLE IF NOT EXISTS `student_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attempt_id` int(100) NOT NULL,
  `exid` int(100) NOT NULL,
  `qid` int(11) NOT NULL,
  `uname` varchar(100) NOT NULL,
  `selected_option` varchar(100) NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `answer_time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `exid` (`exid`),
  KEY `qid` (`qid`),
  KEY `attempt_id` (`attempt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (mysqli_query($conn, $student_answers_sql)) {
    echo "<p>✅ student_answers table created successfully</p>";
} else {
    echo "<p>❌ Error creating student_answers table: " . mysqli_error($conn) . "</p>";
}

echo "<p><a href='teachers/results.php'>Go to Results</a></p>";
?> 