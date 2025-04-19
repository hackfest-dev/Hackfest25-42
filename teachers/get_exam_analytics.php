<?php
// Move error handling to file-only logging (no output to browser)
error_reporting(0);
ini_set('display_errors', 0);

// Log access to this file
$log_dir = __DIR__;
$log_file = $log_dir . '/analytics_debug.log';
file_put_contents($log_file, 
  date('Y-m-d H:i:s') . ' - Request received - ' . 
  (isset($_GET['exam_id']) ? 'exam_id: ' . $_GET['exam_id'] : 'no exam_id') . "\n", 
  FILE_APPEND);

// Capture all output using output buffering to prevent unexpected output
ob_start();

try {
    session_start();
    if (!isset($_SESSION["user_id"])) {
        header("Location: ../login_teacher.php");
        exit;
    }

    // Make sure no output has been sent yet
    if (headers_sent($filename, $linenum)) {
        file_put_contents($log_file, 
          date('Y-m-d H:i:s') . " - Headers already sent in $filename on line $linenum\n", 
          FILE_APPEND);
    }

    include '../config.php';
    
    // Clear any output before setting header
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Start fresh output buffer
    ob_start();
    
    // Set appropriate headers
    header('Content-Type: application/json');
    header('Cache-Control: no-cache, must-revalidate');
    header('X-Content-Type-Options: nosniff');

    // Get exam ID from request
    $exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;

    if ($exam_id <= 0) {
        echo json_encode(['error' => 'Invalid exam ID']);
        exit;
    }

    // Log query parameters
    file_put_contents($log_file, 
      date('Y-m-d H:i:s') . ' - Processing exam_id: ' . $exam_id . "\n", 
      FILE_APPEND);

    // Get exam details
    $exam_query = "SELECT exname, desp, nq FROM exm_list WHERE exid = '$exam_id'";
    $exam_result = mysqli_query($conn, $exam_query);

    if (!$exam_result || mysqli_num_rows($exam_result) == 0) {
        $error = 'Exam not found. Query error: ' . mysqli_error($conn);
        file_put_contents($log_file, 
          date('Y-m-d H:i:s') . ' - Error: ' . $error . "\n", 
          FILE_APPEND);
        echo json_encode(['error' => $error]);
        exit;
    }

    $exam_data = mysqli_fetch_assoc($exam_result);

    // Check if student_answers table exists
    $table_check_query = "SHOW TABLES LIKE 'student_answers'";
    $table_check_result = mysqli_query($conn, $table_check_query);
    $table_exists = mysqli_num_rows($table_check_result) > 0;

    if (!$table_exists) {
        $error = 'student_answers table does not exist in the database';
        file_put_contents($log_file, 
          date('Y-m-d H:i:s') . ' - Error: ' . $error . "\n", 
          FILE_APPEND);
        echo json_encode(['error' => $error]);
        exit;
    }

    // Get all questions for this exam
    $questions_query = "SELECT qid, qstn, qstn_o1, qstn_o2, qstn_o3, qstn_o4, qstn_ans FROM qstn_list WHERE exid = '$exam_id'";
    $questions_result = mysqli_query($conn, $questions_query);

    if (!$questions_result) {
        $error = 'Error fetching questions: ' . mysqli_error($conn);
        file_put_contents($log_file, 
          date('Y-m-d H:i:s') . ' - Error: ' . $error . "\n", 
          FILE_APPEND);
        echo json_encode(['error' => $error]);
        exit;
    }

    $analytics_data = [
        'exam_id' => $exam_id,
        'exam_name' => $exam_data['exname'],
        'description' => $exam_data['desp'],
        'total_questions' => $exam_data['nq'],
        'questions' => []
    ];

    // Count total students who attempted this exam
    $total_students_query = "SELECT COUNT(DISTINCT uname) as total FROM atmpt_list WHERE exid = '$exam_id'";
    $total_students_result = mysqli_query($conn, $total_students_query);
    $total_students = mysqli_fetch_assoc($total_students_result)['total'];
    $analytics_data['total_students'] = $total_students;

    while ($question = mysqli_fetch_assoc($questions_result)) {
        $qid = $question['qid'];
        
        // Get counts for each option
        $option_counts = [
            'option1' => 0,
            'option2' => 0, 
            'option3' => 0,
            'option4' => 0
        ];
        
        // Count student answers for this question
        $answers_query = "SELECT selected_option, COUNT(*) as count FROM student_answers 
                         WHERE qid = '$qid' AND exid = '$exam_id' 
                         GROUP BY selected_option";
        $answers_result = mysqli_query($conn, $answers_query);
        
        if ($answers_result && mysqli_num_rows($answers_result) > 0) {
            while ($answer = mysqli_fetch_assoc($answers_result)) {
                switch ($answer['selected_option']) {
                    case $question['qstn_o1']:
                        $option_counts['option1'] = intval($answer['count']);
                        break;
                    case $question['qstn_o2']:
                        $option_counts['option2'] = intval($answer['count']);
                        break;
                    case $question['qstn_o3']:
                        $option_counts['option3'] = intval($answer['count']);
                        break;
                    case $question['qstn_o4']:
                        $option_counts['option4'] = intval($answer['count']);
                        break;
                }
            }
        }
        
        // Count correct answers
        $correct_answers_query = "SELECT COUNT(*) as count FROM student_answers 
                                WHERE qid = '$qid' AND exid = '$exam_id' AND is_correct = 1";
        $correct_answers_result = mysqli_query($conn, $correct_answers_query);
        $correct_answers = 0;
        
        if ($correct_answers_result && mysqli_num_rows($correct_answers_result) > 0) {
            $correct_answers = intval(mysqli_fetch_assoc($correct_answers_result)['count']);
        }
        
        $analytics_data['questions'][] = [
            'question_id' => $qid,
            'question_text' => $question['qstn'],
            'options' => [
                [
                    'text' => $question['qstn_o1'],
                    'count' => $option_counts['option1'],
                    'percentage' => $total_students > 0 ? round(($option_counts['option1'] / $total_students) * 100, 1) : 0
                ],
                [
                    'text' => $question['qstn_o2'],
                    'count' => $option_counts['option2'],
                    'percentage' => $total_students > 0 ? round(($option_counts['option2'] / $total_students) * 100, 1) : 0
                ],
                [
                    'text' => $question['qstn_o3'],
                    'count' => $option_counts['option3'],
                    'percentage' => $total_students > 0 ? round(($option_counts['option3'] / $total_students) * 100, 1) : 0
                ],
                [
                    'text' => $question['qstn_o4'],
                    'count' => $option_counts['option4'],
                    'percentage' => $total_students > 0 ? round(($option_counts['option4'] / $total_students) * 100, 1) : 0
                ]
            ],
            'correct_option' => $question['qstn_ans'],
            'correct_responses' => $correct_answers,
            'correct_percentage' => $total_students > 0 ? round(($correct_answers / $total_students) * 100, 1) : 0
        ];
    }

    // Check if we have any student data
    $has_answer_data = false;
    foreach ($analytics_data['questions'] as $question) {
        foreach ($question['options'] as $option) {
            if ($option['count'] > 0) {
                $has_answer_data = true;
                break 2;
            }
        }
    }

    // If no student has taken the exam yet, return an appropriate error
    if (!$has_answer_data) {
        $message = 'No student answer data available for this exam yet. Analytics will be available once students have taken the exam.';
        file_put_contents($log_file, 
          date('Y-m-d H:i:s') . ' - ' . $message . "\n", 
          FILE_APPEND);
        echo json_encode(['error' => $message]);
        exit;
    }

    // Ensure we only output valid JSON
    $final_output = json_encode($analytics_data);
    
    // End all output buffers and clear them
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Output only our JSON data with appropriate headers
    header('Content-Type: application/json');
    header('Content-Length: ' . strlen($final_output));
    echo $final_output;
    exit;
    
} catch (Exception $e) {
    // Log any exceptions
    file_put_contents($log_file, 
      date('Y-m-d H:i:s') . ' - Exception: ' . $e->getMessage() . "\n", 
      FILE_APPEND);
    
    // Clear output buffer and return error
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Output only our error JSON
    $error_output = json_encode(['error' => 'An error occurred while processing analytics data.']);
    header('Content-Type: application/json');
    header('Content-Length: ' . strlen($error_output));
    echo $error_output;
    exit;
}

// End output buffering
ob_end_flush(); 