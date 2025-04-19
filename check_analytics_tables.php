<?php
// Enable error output for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Analytics Tables Status</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        .container { max-width: 800px; margin: 0 auto; }
        h1 { color: #2c3e50; }
        h2 { color: #3498db; margin-top: 20px; }
        .success { color: #27ae60; background: #e8f8f5; padding: 10px; border-radius: 4px; margin-bottom: 10px; }
        .error { color: #c0392b; background: #f9ebea; padding: 10px; border-radius: 4px; margin-bottom: 10px; }
        .info { color: #2980b9; background: #ebf5fb; padding: 10px; border-radius: 4px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 10px; text-align: left; }
        th { background-color: #f8f9fa; }
        .button { 
            background: #3498db; 
            color: white; 
            border: none; 
            padding: 10px 15px; 
            border-radius: 4px; 
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            margin-right: 10px;
        }
        .button:hover { background: #2980b9; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>Analytics Tables Status</h1>";

// Check for student_answers table
$check_student_answers = mysqli_query($conn, "SHOW TABLES LIKE 'student_answers'");
$student_answers_exists = mysqli_num_rows($check_student_answers) > 0;

// Check for question_options table
$check_question_options = mysqli_query($conn, "SHOW TABLES LIKE 'question_options'");
$question_options_exists = mysqli_num_rows($check_question_options) > 0;

echo "<h2>Database Tables Status</h2>";
echo "<table>
    <tr>
        <th>Table Name</th>
        <th>Status</th>
    </tr>
    <tr>
        <td>student_answers</td>
        <td>" . ($student_answers_exists ? 
            "<span class='success'>✅ Exists</span>" : 
            "<span class='error'>❌ Missing</span>") . "</td>
    </tr>
    <tr>
        <td>question_options</td>
        <td>" . ($question_options_exists ? 
            "<span class='success'>✅ Exists</span>" : 
            "<span class='error'>❌ Missing</span>") . "</td>
    </tr>
</table>";

// Check for data in student_answers if it exists
if ($student_answers_exists) {
    $data_check = mysqli_query($conn, "SELECT COUNT(*) as count FROM student_answers");
    $data_count = mysqli_fetch_assoc($data_check)['count'];
    
    echo "<h2>Data Status</h2>";
    echo "<div class='" . ($data_count > 0 ? "success" : "info") . "'>";
    echo $data_count > 0 ? 
        "✅ The student_answers table contains $data_count records." : 
        "ℹ️ The student_answers table is empty. Analytics will be available once students have taken exams or sample data is added.";
    echo "</div>";
    
    // Sample exams and students info
    $exams_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM exm_list");
    $exams_count = mysqli_fetch_assoc($exams_query)['count'];
    
    $students_query = mysqli_query($conn, "SELECT COUNT(DISTINCT uname) as count FROM atmpt_list");
    $students_count = mysqli_fetch_assoc($students_query)['count'];
    
    echo "<div class='info'>
        ℹ️ Your database has $exams_count exams and $students_count students who have attempted exams.
    </div>";
}

echo "<h2>Actions</h2>";
echo "<div>";
echo "<a href='install_analytics_tables_fix.php' class='button'>Install/Fix Analytics Tables</a>";
echo "<a href='teachers/results.php' class='button'>Go to Results Page</a>";
echo "</div>";

echo "<h2>Troubleshooting</h2>";
echo "<div class='info'>
<p><strong>If you're seeing a blank screen or error in analytics:</strong></p>
<ol>
    <li>Make sure you have run the 'Install/Fix Analytics Tables' script</li>
    <li>Check that students have taken exams or sample data has been added</li>
    <li>Verify that the file 'teachers/simple_analytics.php' exists and is accessible</li>
    <li>Check your browser console for any JavaScript errors</li>
    <li>Look at the log files in the teachers directory for more details</li>
</ol>
</div>";

echo "</div></body></html>";
?> 