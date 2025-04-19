<?php
// Enable error output for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Add Sample Analytics Data</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        .container { max-width: 800px; margin: 0 auto; }
        h1 { color: #2c3e50; }
        h2 { color: #3498db; margin-top: 20px; }
        .success { color: #27ae60; background: #e8f8f5; padding: 10px; border-radius: 4px; margin-bottom: 10px; }
        .error { color: #c0392b; background: #f9ebea; padding: 10px; border-radius: 4px; margin-bottom: 10px; }
        .info { color: #2980b9; background: #ebf5fb; padding: 10px; border-radius: 4px; margin-bottom: 10px; }
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
        <h1>Add Sample Analytics Data</h1>";

// First, check if tables exist
$student_answers_exists = mysqli_num_rows(mysqli_query($conn, "SHOW TABLES LIKE 'student_answers'")) > 0;

if (!$student_answers_exists) {
    echo "<div class='error'>The student_answers table doesn't exist. Please run <a href='install_analytics_tables_fix.php'>install_analytics_tables_fix.php</a> first.</div>";
    echo "</div></body></html>";
    exit;
}

// Check if we are processing a form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate'])) {
    $exam_id = isset($_POST['exam_id']) ? intval($_POST['exam_id']) : 0;
    $num_students = isset($_POST['num_students']) ? intval($_POST['num_students']) : 5;
    
    if ($exam_id <= 0) {
        echo "<div class='error'>Please select a valid exam.</div>";
    } else {
        // Start the sample data generation process
        echo "<h2>Generating Sample Data for Exam ID: $exam_id</h2>";
        
        // Get questions for this exam
        $questions_query = mysqli_query($conn, "SELECT qid, qstn_ans FROM qstn_list WHERE exid = $exam_id");
        $question_count = mysqli_num_rows($questions_query);
        
        if ($question_count === 0) {
            echo "<div class='error'>No questions found for this exam.</div>";
        } else {
            echo "<div class='info'>Found $question_count questions for this exam.</div>";
            
            // Get students who attempted this exam
            $students_query = mysqli_query($conn, "SELECT uname, atmpt_id FROM atmpt_list WHERE exid = $exam_id LIMIT $num_students");
            $student_count = mysqli_num_rows($students_query);
            
            if ($student_count === 0) {
                echo "<div class='error'>No student attempts found for this exam.</div>";
            } else {
                echo "<div class='info'>Found $student_count students who attempted this exam.</div>";
                
                $records_added = 0;
                
                // Process each student
                while ($student = mysqli_fetch_assoc($students_query)) {
                    $uname = $student['uname'];
                    $atmpt_id = $student['atmpt_id'];
                    
                    // Reset questions result pointer
                    mysqli_data_seek($questions_query, 0);
                    
                    // Process each question for this student
                    while ($question = mysqli_fetch_assoc($questions_query)) {
                        $qid = $question['qid'];
                        $correct_answer = $question['qstn_ans'];
                        
                        // Check if this answer already exists
                        $check_exists = mysqli_query($conn, "SELECT id FROM student_answers WHERE attempt_id = $atmpt_id AND qid = $qid AND uname = '$uname'");
                        
                        if (mysqli_num_rows($check_exists) === 0) {
                            // Randomly decide if student got the answer correct (70% chance)
                            $is_correct = rand(1, 10) <= 7 ? 1 : 0;
                            $selected_option = $is_correct ? $correct_answer : $correct_answer;
                            
                            // For incorrect answers, make sure selected_option is different
                            if (!$is_correct) {
                                // Get question options
                                $options_query = mysqli_query($conn, "SELECT qstn_o1, qstn_o2, qstn_o3, qstn_o4 FROM qstn_list WHERE qid = $qid");
                                $options = mysqli_fetch_assoc($options_query);
                                
                                $all_options = array($options['qstn_o1'], $options['qstn_o2'], $options['qstn_o3'], $options['qstn_o4']);
                                $all_options = array_diff($all_options, array($correct_answer));
                                
                                if (!empty($all_options)) {
                                    $selected_option = $all_options[array_rand($all_options)];
                                }
                            }
                            
                            // Insert student answer
                            $insert_query = "INSERT INTO student_answers (attempt_id, exid, qid, uname, selected_option, is_correct) 
                                           VALUES ($atmpt_id, $exam_id, $qid, '$uname', '$selected_option', $is_correct)";
                            
                            if (mysqli_query($conn, $insert_query)) {
                                $records_added++;
                            } else {
                                echo "<div class='error'>Error adding record: " . mysqli_error($conn) . "</div>";
                            }
                        }
                    }
                }
                
                if ($records_added > 0) {
                    echo "<div class='success'>Successfully added $records_added sample answer records.</div>";
                } else {
                    echo "<div class='info'>No new records were added. Sample data may already exist for these students and questions.</div>";
                }
            }
        }
    }
}

// Show form to select exam
echo "<h2>Select an Exam for Sample Data</h2>";

// Get available exams
$exams_query = mysqli_query($conn, "SELECT exid, exname FROM exm_list ORDER BY exid DESC");
$exam_count = mysqli_num_rows($exams_query);

if ($exam_count === 0) {
    echo "<div class='info'>No exams found in the database. Please create exams first.</div>";
} else {
    echo "<form method='post' action=''>
        <div style='margin-bottom: 15px;'>
            <label for='exam_id' style='display: block; margin-bottom: 5px;'>Select Exam:</label>
            <select name='exam_id' id='exam_id' style='padding: 8px; width: 100%;'>
                <option value=''>-- Select an exam --</option>";
    
    while ($exam = mysqli_fetch_assoc($exams_query)) {
        echo "<option value='{$exam['exid']}'>{$exam['exname']} (ID: {$exam['exid']})</option>";
    }
    
    echo "</select>
        </div>
        
        <div style='margin-bottom: 15px;'>
            <label for='num_students' style='display: block; margin-bottom: 5px;'>Number of Students (max):</label>
            <input type='number' name='num_students' id='num_students' min='1' max='50' value='5' style='padding: 8px; width: 100%;'>
        </div>
        
        <button type='submit' name='generate' class='button'>Generate Sample Data</button>
    </form>";
}

// Add navigation links
echo "<div style='margin-top: 20px;'>
    <a href='check_analytics_tables.php' class='button'>Check Analytics Tables</a>
    <a href='teachers/results.php' class='button'>Go to Results Page</a>
</div>";

echo "</div></body></html>";
?> 