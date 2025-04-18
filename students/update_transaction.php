<?php
// Ensure all errors are caught and not displayed directly
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Start session
session_start();
if (!isset($_SESSION["uname"])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit();
}

// Get the current user's username
$uname = $_SESSION["uname"];

// Include required files
require_once '../config.php';
require_once '../utils/mailer.php';

// Get JSON data
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (!$data || !isset($data['nft_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Invalid request data']);
    exit();
}

// Extract data
$nft_id = intval($data['nft_id']);
$transaction_hash = isset($data['transaction_hash']) ? $data['transaction_hash'] : '';
$token_id = isset($data['token_id']) ? $data['token_id'] : '';
$send_email = isset($data['send_email']) ? (bool)$data['send_email'] : false;
$certificate_image_base64 = isset($data['certificate_image']) ? $data['certificate_image'] : '';

// Verify this NFT record belongs to the logged-in user
$verify_sql = "SELECT * FROM certificate_nfts WHERE id = $nft_id AND uname = '$uname'";
$verify_result = mysqli_query($conn, $verify_sql);

if (mysqli_num_rows($verify_result) == 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit();
}

// Update the transaction hash and token ID
$update_sql = "UPDATE certificate_nfts SET 
               transaction_hash = '" . mysqli_real_escape_string($conn, $transaction_hash) . "',
               token_id = '" . mysqli_real_escape_string($conn, $token_id) . "'
               WHERE id = $nft_id";

$update_success = mysqli_query($conn, $update_sql);

if (!$update_success) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'error' => 'Database error: ' . mysqli_error($conn)
    ]);
    exit();
}

// Get certificate details for email sending
$email_sent = false;
if ($send_email && !empty($certificate_image_base64)) {
    // Get NFT details to get student username and attempt ID
    $nft_sql = "SELECT * FROM certificate_nfts WHERE id = $nft_id";
    $nft_result = mysqli_query($conn, $nft_sql);
    
    if ($nft_result && mysqli_num_rows($nft_result) > 0) {
        $nft_data = mysqli_fetch_assoc($nft_result);
        $uname = $nft_data['uname'];
        $attempt_id = $nft_data['attempt_id'];
        
        // Save certificate image
        try {
            // Ensure certificates directory exists
            $certificates_dir = '../certificates';
            if (!file_exists($certificates_dir)) {
                mkdir($certificates_dir, 0755, true);
            }
            
            // Save the image
            $certificate_path = $certificates_dir . "/certificate_{$uname}_{$attempt_id}.png";
            
            // Process base64 image
            if (strpos($certificate_image_base64, 'base64,') !== false) {
                list(, $base64_data) = explode('base64,', $certificate_image_base64);
                $image_data = base64_decode($base64_data);
                if ($image_data !== false) {
                    if (file_put_contents($certificate_path, $image_data) !== false) {
                        // Send email with certificate
                        $email_sent = send_nft_certificate_email($uname, $attempt_id, $certificate_path);
                        error_log("Email sending " . ($email_sent ? "successful" : "failed") . " for {$uname}");
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Error processing image for email: " . $e->getMessage());
        }
    }
}

// Return success response
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'Transaction updated successfully',
    'data' => [
        'nft_id' => $nft_id,
        'transaction_hash' => $transaction_hash,
        'token_id' => $token_id,
        'email_sent' => $email_sent
    ]
]);
?> 