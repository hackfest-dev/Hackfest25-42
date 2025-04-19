<?php
session_start();
include '../config.php';

// Set headers to prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log function
function debug_log($message, $type = 'info')
{
    $log_data = [
        'timestamp' => date('Y-m-d H:i:s'),
        'type' => $type,
        'message' => $message
    ];

    file_put_contents(
        '../debug_mock_integrity.log',
        json_encode($log_data) . "\n",
        FILE_APPEND
    );
}

debug_log("Script started");

// Ensure the user is logged in
if (!isset($_SESSION["uname"])) {
    debug_log("User not logged in", "error");
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}
debug_log("User authenticated: " . $_SESSION["uname"]);

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    debug_log("Invalid request method: " . $_SERVER['REQUEST_METHOD'], "error");
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Get request data
$raw_input = file_get_contents('php://input');
debug_log("Raw input: " . $raw_input);
$data = json_decode($raw_input, true);
debug_log("Parsed data: " . json_encode($data));

if (!isset($data['mock_exam_id']) || !isset($data['violation_type'])) {
    debug_log("Missing required fields", "error");
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

$student_username = $_SESSION["uname"];
$mock_exam_id = $data['mock_exam_id'];
$violation_type = $data['violation_type'];

debug_log("Processing violation for user: $student_username, exam: $mock_exam_id, type: $violation_type");

// Get the current violation count
try {
    $sql = "SELECT COUNT(*) as count FROM mock_cheat_violations 
            WHERE student_username = ? AND mock_exam_id = ? AND violation_type = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sis", $student_username, $mock_exam_id, $violation_type);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $occurrence = $row['count'] + 1;
    debug_log("Occurrence: $occurrence");
} catch (Exception $e) {
    debug_log("Error getting violation count: " . $e->getMessage(), "error");
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    exit;
}

// Determine penalty based on violation type and occurrence
switch ($violation_type) {
    case 'tab_switch':
        // Tab switching penalties
        if ($occurrence == 1) {
            $penalty = 3;
        } else if ($occurrence == 2) {
            $penalty = 5;
        } else if ($occurrence == 3) {
            $penalty = 8;
        } else if ($occurrence == 4) {
            $penalty = 12;
        } else {
            $penalty = 15; // 5th and subsequent occurrences
        }
        break;

    case 'window_blur':
        // Window blur penalties
        if ($occurrence == 1) {
            $penalty = 2;
        } else if ($occurrence == 2) {
            $penalty = 4;
        } else if ($occurrence == 3) {
            $penalty = 6;
        } else {
            $penalty = 8; // 4th and subsequent occurrences
        }
        break;

    case 'combined':
        // Combined violation penalties
        if ($occurrence == 1) {
            $penalty = 10;
        } else if ($occurrence == 2) {
            $penalty = 15;
        } else {
            $penalty = 20; // 3rd and subsequent occurrences
        }
        break;

    case 'exit_fullscreen':
        // Exit fullscreen penalties
        if ($occurrence == 1) {
            $penalty = 5;
        } else if ($occurrence == 2) {
            $penalty = 10;
        } else {
            $penalty = 15;
        }
        break;

    default:
        $penalty = 0;
}

debug_log("Penalty assigned: $penalty");

// Create the table if it doesn't exist
try {
    $create_table_sql = "CREATE TABLE IF NOT EXISTS mock_cheat_violations (
        id INT(11) NOT NULL AUTO_INCREMENT,
        student_username VARCHAR(50) NOT NULL,
        mock_exam_id INT(11) NOT NULL,
        violation_type VARCHAR(50) NOT NULL,
        occurrence INT(11) NOT NULL,
        penalty INT(11) NOT NULL,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    )";

    if ($conn->query($create_table_sql)) {
        debug_log("Table mock_cheat_violations exists or was created successfully");
    } else {
        debug_log("Error creating table: " . $conn->error, "error");
    }
} catch (Exception $e) {
    debug_log("Error creating table: " . $e->getMessage(), "error");
}

// Insert the violation record
try {
    $sql = "INSERT INTO mock_cheat_violations (student_username, mock_exam_id, violation_type, occurrence, penalty) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisii", $student_username, $mock_exam_id, $violation_type, $occurrence, $penalty);

    if ($stmt->execute()) {
        debug_log("Violation record inserted successfully");

        // Calculate total penalties for this student in this exam
        $sql = "SELECT SUM(penalty) as total_penalty FROM mock_cheat_violations 
                WHERE student_username = ? AND mock_exam_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $student_username, $mock_exam_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total_penalty = $row['total_penalty'];

        debug_log("Total penalty: $total_penalty");

        // Calculate integrity score (starting from 100)
        $integrity_score = max(0, 100 - $total_penalty);
        debug_log("Integrity score: $integrity_score");

        // Determine the integrity category
        if ($integrity_score >= 90) {
            $integrity_category = 'Excellent';
        } else if ($integrity_score >= 80) {
            $integrity_category = 'Good';
        } else if ($integrity_score >= 70) {
            $integrity_category = 'Fair';
        } else if ($integrity_score >= 60) {
            $integrity_category = 'Poor';
        } else {
            $integrity_category = 'Very Poor';
        }
        debug_log("Integrity category: $integrity_category");

        // Return the response
        $response = [
            'status' => 'success',
            'occurrence' => $occurrence,
            'penalty' => $penalty,
            'total_penalty' => $total_penalty,
            'integrity_score' => $integrity_score,
            'integrity_category' => $integrity_category,
            'message' => "Violation recorded. Current penalty: -$penalty points."
        ];

        debug_log("Sending response: " . json_encode($response));
        echo json_encode($response);
    } else {
        debug_log("Failed to record violation: " . $stmt->error, "error");
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to record violation: ' . $stmt->error]);
    }
} catch (Exception $e) {
    debug_log("Exception while recording violation: " . $e->getMessage(), "error");
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Exception: ' . $e->getMessage()]);
}
