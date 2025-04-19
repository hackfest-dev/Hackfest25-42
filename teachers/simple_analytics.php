<?php
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
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'student_answers'");
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
