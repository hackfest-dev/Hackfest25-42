<?php
// Enable error output for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Analytics Tables Installer</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        .container { max-width: 800px; margin: 0 auto; }
        h1 { color: #2c3e50; }
        h2 { color: #3498db; margin-top: 20px; }
        .success { color: #27ae60; background: #e8f8f5; padding: 10px; border-radius: 4px; }
        .error { color: #c0392b; background: #f9ebea; padding: 10px; border-radius: 4px; }
        .info { color: #2980b9; background: #ebf5fb; padding: 10px; border-radius: 4px; }
        pre { background: #f8f9fa; padding: 10px; overflow: auto; }
        button, .button { 
            background: #3498db; 
            color: white; 
            border: none; 
            padding: 10px 15px; 
            border-radius: 4px; 
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        button:hover, .button:hover { background: #2980b9; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>Analytics Tables Installer and Fixer</h1>";

// Function to run SQL and report results
function run_sql($conn, $sql, $description) {
    echo "<h2>$description</h2>";
    echo "<pre>$sql</pre>";
    
    if (mysqli_query($conn, $sql)) {
        echo "<div class='success'>✅ Operation completed successfully</div>";
        return true;
    } else {
        echo "<div class='error'>❌ Error: " . mysqli_error($conn) . "</div>";
        return false;
    }
}

// 1. Create student_answers table if it doesn't exist
$check_student_answers = mysqli_query($conn, "SHOW TABLES LIKE 'student_answers'");
if (mysqli_num_rows($check_student_answers) == 0) {
    $student_answers_sql = "CREATE TABLE `student_answers` (
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
    
    run_sql($conn, $student_answers_sql, "Creating student_answers table");
} else {
    echo "<div class='info'>✓ student_answers table already exists</div>";
}

// 2. Create question_options table if it doesn't exist
$check_question_options = mysqli_query($conn, "SHOW TABLES LIKE 'question_options'");
if (mysqli_num_rows($check_question_options) == 0) {
    $question_options_sql = "CREATE TABLE `question_options` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `qid` int(11) NOT NULL,
      `option_text` varchar(100) NOT NULL,
      `option_number` int(11) NOT NULL,
      PRIMARY KEY (`id`),
      KEY `qid` (`qid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    run_sql($conn, $question_options_sql, "Creating question_options table");
} else {
    echo "<div class='info'>✓ question_options table already exists</div>";
}

// 3. Populate student_answers with sample data if it's empty
$check_student_answers_data = mysqli_query($conn, "SELECT COUNT(*) as count FROM student_answers");
$student_answers_count = mysqli_fetch_assoc($check_student_answers_data)['count'];

if ($student_answers_count == 0) {
    // Get exams
    $exams_query = mysqli_query($conn, "SELECT exid FROM exm_list LIMIT 5");
    
    if (mysqli_num_rows($exams_query) > 0) {
        echo "<h2>Populating student_answers with sample data</h2>";
        
        $sample_data_added = false;
        
        while ($exam = mysqli_fetch_assoc($exams_query)) {
            $exid = $exam['exid'];
            
            // Get questions for this exam
            $questions_query = mysqli_query($conn, "SELECT qid, qstn_ans FROM qstn_list WHERE exid = $exid");
            
            if (mysqli_num_rows($questions_query) > 0) {
                // Get students who attempted this exam
                $students_query = mysqli_query($conn, "SELECT uname, atmpt_id FROM atmpt_list WHERE exid = $exid LIMIT 5");
                
                if (mysqli_num_rows($students_query) > 0) {
                    while ($student = mysqli_fetch_assoc($students_query)) {
                        $uname = $student['uname'];
                        $atmpt_id = $student['atmpt_id'];
                        
                        // Reset questions result pointer
                        mysqli_data_seek($questions_query, 0);
                        
                        while ($question = mysqli_fetch_assoc($questions_query)) {
                            $qid = $question['qid'];
                            $correct_answer = $question['qstn_ans'];
                            
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
                            
                            // Check if this answer already exists
                            $check_exists = mysqli_query($conn, "SELECT id FROM student_answers WHERE attempt_id = $atmpt_id AND qid = $qid AND uname = '$uname'");
                            
                            if (mysqli_num_rows($check_exists) == 0) {
                                // Insert student answer
                                $insert_query = "INSERT INTO student_answers (attempt_id, exid, qid, uname, selected_option, is_correct) 
                                                VALUES ($atmpt_id, $exid, $qid, '$uname', '$selected_option', $is_correct)";
                                
                                if (mysqli_query($conn, $insert_query)) {
                                    $sample_data_added = true;
                                    echo "<div class='success'>Added sample answer for student $uname, question $qid</div>";
                                } else {
                                    echo "<div class='error'>Error adding sample data: " . mysqli_error($conn) . "</div>";
                                }
                            }
                        }
                    }
                } else {
                    echo "<div class='info'>No students found who attempted exam $exid</div>";
                }
            } else {
                echo "<div class='info'>No questions found for exam $exid</div>";
            }
        }
        
        if (!$sample_data_added) {
            echo "<div class='info'>No sample data was added - you may need to have students take exams first</div>";
        }
    } else {
        echo "<div class='info'>No exams found to populate with sample data</div>";
    }
} else {
    echo "<div class='info'>✓ student_answers table already has data ($student_answers_count records)</div>";
}

// 4. Fix the JSON output issue by creating a simpler analytics endpoint
$simple_analytics_content = '<?php
// Disable all error reporting for production
error_reporting(0);
ini_set("display_errors", 0);

// Start clean output
ob_clean();
if (ob_get_level() == 0) ob_start();

// Set headers for JSON output
header("Content-Type: application/json");
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Include database configuration
include "../config.php";

// Get exam ID
$exam_id = isset($_GET["exam_id"]) ? intval($_GET["exam_id"]) : 0;

if ($exam_id <= 0) {
    // Clear output buffer
    while (ob_get_level() > 0) { ob_end_clean(); }
    echo json_encode(["error" => "Invalid exam ID"]);
    exit;
}

// Get exam info
$exam_query = "SELECT exname, desp, nq FROM exm_list WHERE exid = ?";
$stmt = mysqli_prepare($conn, $exam_query);
mysqli_stmt_bind_param($stmt, "i", $exam_id);
mysqli_stmt_execute($stmt);
$exam_result = mysqli_stmt_get_result($stmt);

if (!$exam_result || mysqli_num_rows($exam_result) == 0) {
    // Clear output buffer
    while (ob_get_level() > 0) { ob_end_clean(); }
    echo json_encode(["error" => "Exam not found"]);
    exit;
}

$exam_data = mysqli_fetch_assoc($exam_result);

// Check if student_answers table exists
$table_check = mysqli_query($conn, "SHOW TABLES LIKE \'student_answers\'");
if (!$table_check || mysqli_num_rows($table_check) == 0) {
    // Clear output buffer
    while (ob_get_level() > 0) { ob_end_clean(); }
    echo json_encode(["error" => "student_answers table does not exist"]);
    exit;
}

// Initialize analytics data
$analytics_data = [
    "exam_id" => $exam_id,
    "exam_name" => $exam_data["exname"],
    "description" => $exam_data["desp"],
    "total_questions" => $exam_data["nq"],
    "questions" => []
];

// Get total students who attempted this exam
$total_students_query = "SELECT COUNT(DISTINCT uname) as total FROM atmpt_list WHERE exid = ?";
$stmt = mysqli_prepare($conn, $total_students_query);
mysqli_stmt_bind_param($stmt, "i", $exam_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$total_students = mysqli_fetch_assoc($result)["total"];
$analytics_data["total_students"] = $total_students;

// Get questions
$questions_query = "SELECT qid, qstn, qstn_o1, qstn_o2, qstn_o3, qstn_o4, qstn_ans 
                   FROM qstn_list WHERE exid = ?";
$stmt = mysqli_prepare($conn, $questions_query);
mysqli_stmt_bind_param($stmt, "i", $exam_id);
mysqli_stmt_execute($stmt);
$questions_result = mysqli_stmt_get_result($stmt);

if (!$questions_result) {
    // Clear output buffer
    while (ob_get_level() > 0) { ob_end_clean(); }
    echo json_encode(["error" => "Error fetching questions"]);
    exit;
}

// Process each question
while ($question = mysqli_fetch_assoc($questions_result)) {
    $qid = $question["qid"];
    
    // Initialize option counts
    $option_counts = [
        "option1" => 0,
        "option2" => 0, 
        "option3" => 0,
        "option4" => 0
    ];
    
    // Get answer counts for this question
    $answers_query = "SELECT selected_option, COUNT(*) as count 
                     FROM student_answers 
                     WHERE qid = ? AND exid = ? 
                     GROUP BY selected_option";
                     
    $stmt = mysqli_prepare($conn, $answers_query);
    mysqli_stmt_bind_param($stmt, "ii", $qid, $exam_id);
    mysqli_stmt_execute($stmt);
    $answers_result = mysqli_stmt_get_result($stmt);
    
    if ($answers_result && mysqli_num_rows($answers_result) > 0) {
        while ($answer = mysqli_fetch_assoc($answers_result)) {
            switch ($answer["selected_option"]) {
                case $question["qstn_o1"]:
                    $option_counts["option1"] = intval($answer["count"]);
                    break;
                case $question["qstn_o2"]:
                    $option_counts["option2"] = intval($answer["count"]);
                    break;
                case $question["qstn_o3"]:
                    $option_counts["option3"] = intval($answer["count"]);
                    break;
                case $question["qstn_o4"]:
                    $option_counts["option4"] = intval($answer["count"]);
                    break;
            }
        }
    }
    
    // Get correct answer count
    $correct_answers_query = "SELECT COUNT(*) as count 
                             FROM student_answers 
                             WHERE qid = ? AND exid = ? AND is_correct = 1";
                             
    $stmt = mysqli_prepare($conn, $correct_answers_query);
    mysqli_stmt_bind_param($stmt, "ii", $qid, $exam_id);
    mysqli_stmt_execute($stmt);
    $correct_result = mysqli_stmt_get_result($stmt);
    $correct_answers = 0;
    
    if ($correct_result && mysqli_num_rows($correct_result) > 0) {
        $correct_answers = intval(mysqli_fetch_assoc($correct_result)["count"]);
    }
    
    // Add question analytics
    $analytics_data["questions"][] = [
        "question_id" => $qid,
        "question_text" => $question["qstn"],
        "options" => [
            [
                "text" => $question["qstn_o1"],
                "count" => $option_counts["option1"],
                "percentage" => $total_students > 0 ? round(($option_counts["option1"] / $total_students) * 100, 1) : 0
            ],
            [
                "text" => $question["qstn_o2"],
                "count" => $option_counts["option2"],
                "percentage" => $total_students > 0 ? round(($option_counts["option2"] / $total_students) * 100, 1) : 0
            ],
            [
                "text" => $question["qstn_o3"],
                "count" => $option_counts["option3"],
                "percentage" => $total_students > 0 ? round(($option_counts["option3"] / $total_students) * 100, 1) : 0
            ],
            [
                "text" => $question["qstn_o4"],
                "count" => $option_counts["option4"],
                "percentage" => $total_students > 0 ? round(($option_counts["option4"] / $total_students) * 100, 1) : 0
            ]
        ],
        "correct_option" => $question["qstn_ans"],
        "correct_responses" => $correct_answers,
        "correct_percentage" => $total_students > 0 ? round(($correct_answers / $total_students) * 100, 1) : 0
    ];
}

// Check if we have any answer data
$has_data = false;
foreach ($analytics_data["questions"] as $question) {
    foreach ($question["options"] as $option) {
        if ($option["count"] > 0) {
            $has_data = true;
            break 2;
        }
    }
}

if (!$has_data) {
    // Clear output buffer
    while (ob_get_level() > 0) { ob_end_clean(); }
    echo json_encode(["error" => "No student answer data available for this exam yet"]);
    exit;
}

// Clear all output buffers
while (ob_get_level() > 0) { ob_end_clean(); }

// Output clean JSON
echo json_encode($analytics_data);
';

// Save the simpler analytics endpoint file
$file_path = 'teachers/simple_analytics.php';
if (file_put_contents($file_path, $simple_analytics_content)) {
    echo "<div class='success'>Created simplified analytics endpoint at $file_path</div>";
} else {
    echo "<div class='error'>Failed to create simplified analytics endpoint</div>";
}

// 5. Update the view_analytics.php file to use the new simple analytics endpoint
$update_view_analytics = false;

$view_analytics_path = 'teachers/view_analytics.php';
$view_analytics_content = file_get_contents($view_analytics_path);

if ($view_analytics_content) {
    // Replace the analytics endpoint
    $old_pattern = '/\$analytics_endpoint = "http:\/\/" \. \$server_name \. \$server_port \. \$directory \. "\/get_exam_analytics\.php\?exam_id=" \. \$exid;/';
    $new_endpoint = '$analytics_endpoint = "http://" . $server_name . $server_port . $directory . "/simple_analytics.php?exam_id=" . $exid;';
    
    $updated_content = preg_replace($old_pattern, $new_endpoint, $view_analytics_content);
    
    if ($updated_content != $view_analytics_content) {
        if (file_put_contents($view_analytics_path, $updated_content)) {
            echo "<div class='success'>Updated view_analytics.php to use the simplified endpoint</div>";
            $update_view_analytics = true;
        } else {
            echo "<div class='error'>Failed to update view_analytics.php</div>";
        }
    } else {
        echo "<div class='info'>No changes needed to view_analytics.php</div>";
    }
} else {
    echo "<div class='error'>Failed to read view_analytics.php</div>";
}

echo "<h2>Next Steps</h2>";
echo "<ol>
    <li>Run this script by visiting: <a href='install_analytics_tables_fix.php' target='_blank'>install_analytics_tables_fix.php</a></li>
    <li>Go to the Teacher's Dashboard and try to view analytics again</li>
    <li>If you still encounter issues, check the logs in the teachers folder for any error messages</li>
</ol>";

if ($update_view_analytics) {
    echo "<div class='info'>The view_analytics.php file has been updated to use a new simplified endpoint.</div>";
}

echo "<div>
    <a href='teachers/results.php' class='button'>Go to Results</a>
</div>";

echo "</div></body></html>";
?> 