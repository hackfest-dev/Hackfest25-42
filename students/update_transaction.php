<?php
session_start();
if (!isset($_SESSION["uname"])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit();
}

// Include database connection
include '../config.php';

// Get JSON data from POST request
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (!$data || !isset($data['nft_id']) || !isset($data['transaction_hash'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Invalid request data']);
    exit();
}

$nft_id = intval($data['nft_id']);
$transaction_hash = $data['transaction_hash'];
$token_id = $data['token_id'] ?? '';
$uname = $_SESSION['uname'];

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
               transaction_hash = '$transaction_hash'";
               
if (!empty($token_id)) {
    $update_sql .= ", token_id = '$token_id'";
}

$update_sql .= " WHERE id = $nft_id";

if (mysqli_query($conn, $update_sql)) {
    // Get the updated record
    $get_sql = "SELECT * FROM certificate_nfts WHERE id = $nft_id";
    $result = mysqli_query($conn, $get_sql);
    $nft_data = mysqli_fetch_assoc($result);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Transaction hash updated successfully',
        'data' => $nft_data
    ]);
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Database error: ' . mysqli_error($conn)]);
}
?> 