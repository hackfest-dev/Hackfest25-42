<?php
session_start();
if (!isset($_SESSION["uname"])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit();
}

// Include database connection and Web3Helper
include '../config.php';
require_once 'Web3Helper.php';

// Direct blockchain configuration values (hardcoded)
// The nft/.env file is no longer required with this approach
$env_vars = [
    'REACT_APP_PRIVATE_KEY' => 'f4bdb26109469f0210291188519001dc5ff2e7458ea0bd81e77392b9a5bbf535',
    'REACT_APP_SEPOLIA_RPC_URL' => 'https://eth-sepolia.g.alchemy.com/v2/MGXla7xn3bEXIthLSYDJSw24tWE_EWl_',
    'REACT_APP_INFURA_PROJECT_ID' => '932d4a93ba314e2d924642fc81f98a05'
];

// Log the configuration 
error_log("mint_nft.php: Using hardcoded blockchain configuration values");

// Get JSON data from POST request
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (!$data || !isset($data['attempt_id']) || !isset($data['metadata_url'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Invalid request data']);
    exit();
}

$attempt_id = intval($data['attempt_id']);
$metadata_url = $data['metadata_url'];
$image_url = $data['image_url'] ?? '';
$uname = $_SESSION['uname'];

// Verify this attempt belongs to the logged-in user
$verify_sql = "SELECT * FROM atmpt_list WHERE id = $attempt_id AND uname = '$uname'";
$verify_result = mysqli_query($conn, $verify_sql);

if (mysqli_num_rows($verify_result) == 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit();
}

// Check if NFT has already been minted for this certificate
$nft_sql = "SELECT * FROM certificate_nfts WHERE attempt_id = $attempt_id";
$nft_result = mysqli_query($conn, $nft_sql);

if (mysqli_num_rows($nft_result) > 0) {
    $nft_data = mysqli_fetch_assoc($nft_result);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true, 
        'message' => 'NFT already minted', 
        'data' => $nft_data
    ]);
    exit();
}

// Set up blockchain configuration
$privateKey = $env_vars['REACT_APP_PRIVATE_KEY'] ?? '';
$rpcUrl = $env_vars['REACT_APP_SEPOLIA_RPC_URL'] ?? '';
$contractAddress = '0x8cFe8F5395c87522Ce96915c2B492960bd63633E'; // From blockchain.md

if (empty($privateKey) || empty($rpcUrl)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Missing blockchain configuration']);
    exit();
}

// Use our Web3Helper to perform the blockchain transaction
$web3Helper = new Web3Helper($rpcUrl, $privateKey, $contractAddress);
$mintResult = $web3Helper->mintNFT($metadata_url);

if (!$mintResult['success']) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => $mintResult['error']]);
    exit();
}

// For our implementation, we'll use the transaction data for client-side processing
// first ensure the certificate_nfts table exists
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'certificate_nfts'");
if (mysqli_num_rows($table_check) == 0) {
    $create_table = "CREATE TABLE certificate_nfts (
        id INT(11) NOT NULL AUTO_INCREMENT,
        attempt_id INT(11) NOT NULL,
        uname VARCHAR(100) NOT NULL,
        transaction_hash VARCHAR(255) NOT NULL,
        token_id VARCHAR(100) NOT NULL,
        contract_address VARCHAR(255) NOT NULL,
        metadata_url VARCHAR(255) NOT NULL,
        image_url VARCHAR(255) NOT NULL,
        is_demo TINYINT(1) NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY (attempt_id)
    )";
    mysqli_query($conn, $create_table);
}

// Create a temporary transaction hash
$txHash = '0x' . bin2hex(random_bytes(32));
$tokenId = '0'; // To be updated by the client

// Insert initial NFT data
$insert_sql = "INSERT INTO certificate_nfts (attempt_id, uname, transaction_hash, token_id, contract_address, metadata_url, image_url, is_demo, created_at) 
               VALUES ($attempt_id, '$uname', '$txHash', '$tokenId', '$contractAddress', '$metadata_url', '$image_url', 0, NOW())";

if (mysqli_query($conn, $insert_sql)) {
    // Get the ID of the inserted record
    $nft_id = mysqli_insert_id($conn);
    
    // Prepare the data for the client
    $txData = $mintResult['data']['tx_data'] ?? [];
    
    // Return the transaction data for client-side processing
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Transaction prepared for client-side signing',
        'data' => [
            'nft_id' => $nft_id,
            'transaction_hash' => $txHash,
            'token_id' => $tokenId,
            'contract_address' => $contractAddress,
            'metadata_url' => $metadata_url,
            'image_url' => $image_url,
            'rpc_url' => $rpcUrl,
            'private_key' => $privateKey,
            'tx_data' => $txData
        ]
    ]);
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Database error: ' . mysqli_error($conn)]);
}
?> 