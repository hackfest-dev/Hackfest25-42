<?php
session_start();
include '../config.php';

// Ensure the user is logged in
if (!isset($_SESSION["uname"])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Get request data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['mock_exam_id']) || !isset($data['violation_type'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

$student_username = $_SESSION["uname"];
$mock_exam_id = $data['mock_exam_id'];
$violation_type = $data['violation_type'];

// Get the current violation count
$sql = "SELECT COUNT(*) as count FROM mock_cheat_violations 
        WHERE student_username = ? AND mock_exam_id = ? AND violation_type = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sis", $student_username, $mock_exam_id, $violation_type);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$occurrence = $row['count'] + 1;

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
        } else {
            $penalty = 15; // 4th and subsequent occurrences
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
        // Combined violation penalties (Phase 3)
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
            $penalty = 15; // 3rd and subsequent occurrences
        }
        break;

    default:
        $penalty = 0;
}

// Create the table if it doesn't exist
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
$conn->query($create_table_sql);

// Insert the violation record
$sql = "INSERT INTO mock_cheat_violations (student_username, mock_exam_id, violation_type, occurrence, penalty) 
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sisii", $student_username, $mock_exam_id, $violation_type, $occurrence, $penalty);

if ($stmt->execute()) {
    // Calculate total penalties for this student in this exam
    $sql = "SELECT SUM(penalty) as total_penalty FROM mock_cheat_violations 
            WHERE student_username = ? AND mock_exam_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $student_username, $mock_exam_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $total_penalty = $row['total_penalty'];

    // Calculate integrity score (starting from 100)
    $integrity_score = max(0, 100 - $total_penalty);

    // Determine the integrity category
    $integrity_category = '';
    if ($integrity_score >= 75) {
        $integrity_category = 'Good';
    } else if ($integrity_score >= 50) {
        $integrity_category = 'At-Risk';
    } else {
        $integrity_category = 'Cheating Suspicion';
    }

    // Return the response
    echo json_encode([
        'status' => 'success',
        'occurrence' => $occurrence,
        'penalty' => $penalty,
        'total_penalty' => $total_penalty,
        'integrity_score' => $integrity_score,
        'integrity_category' => $integrity_category,
        'message' => "Violation recorded. Current penalty: -$penalty points."
    ]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to record violation']);
} 