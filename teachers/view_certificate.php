<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login_teacher.php");
    exit();
}

// Include database connection
include '../config.php';

// Get parameters
$attempt_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$attempt_id) {
    die("Certificate ID not provided");
}

// Get attempt data
$verify_sql = "SELECT * FROM atmpt_list WHERE id = $attempt_id";
$verify_result = mysqli_query($conn, $verify_sql);

if (mysqli_num_rows($verify_result) == 0) {
    die("Certificate not found");
}

// Get attempt data
$attempt_data = mysqli_fetch_assoc($verify_result);
$exid = $attempt_data['exid'];
$uname = $attempt_data['uname'];
$score = $attempt_data['cnq'];
$total = $attempt_data['nq'];
$percentage = $attempt_data['ptg'];
$completion_date = date("F j, Y", strtotime($attempt_data['subtime']));
$integrity_score = $attempt_data['integrity_score'] ?? 100;
$integrity_category = $attempt_data['integrity_category'] ?? 'Good';

// Get exam name
$exam_sql = "SELECT exname, subject FROM exm_list WHERE exid = $exid";
$exam_result = mysqli_query($conn, $exam_sql);
$exam_data = mysqli_fetch_assoc($exam_result);
$exam_name = $exam_data['exname'];
$subject = $exam_data['subject'];

// Get student name
$student_sql = "SELECT fname FROM student WHERE uname = '$uname'";
$student_result = mysqli_query($conn, $student_sql);
$student_data = mysqli_fetch_assoc($student_result);
$student_name = $student_data['fname'];

// Check if NFT has been minted for this certificate
$nft_minted = false;
$nft_data = null;

// Check if the table exists
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'certificate_nfts'");
if (mysqli_num_rows($table_check) > 0) {
    // Table exists, now we can query it
    $nft_sql = "SELECT * FROM certificate_nfts WHERE attempt_id = $attempt_id";
    $nft_result = mysqli_query($conn, $nft_sql);

    if ($nft_result && mysqli_num_rows($nft_result) > 0) {
        $nft_data = mysqli_fetch_assoc($nft_result);
        $nft_minted = true;
    }
}

// Create HTML certificate instead of GD image
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blockchain Certificate</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Cormorant+Garamond:wght@400;700&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .back-btn {
            align-self: flex-start;
            margin-bottom: 20px;
            background-color: #0A2558;
            color: white;
            border: none;
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 4px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .back-btn:hover {
            background-color: #0d3a80;
        }

        .back-btn svg {
            margin-right: 5px;
        }

        /* Certificate styles */
        .certificate-container {
            background-color: #fefef9;
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23d0b15e' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
            width: 800px;
            height: 600px;
            padding: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            border: 2px solid #d4af37;
            position: relative;
            margin-bottom: 30px;
            overflow: hidden;
        }

        /* Fancy border with corner decorations */
        .fancy-border {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border: 2px solid rgba(212, 175, 55, 0.5);
            margin: 10px;
            pointer-events: none;
            z-index: 1;
            box-shadow: inset 0 0 15px rgba(212, 175, 55, 0.15);
        }

        .corner {
            position: absolute;
            width: 40px;
            height: 40px;
            border-color: #d4af37;
            z-index: 2;
        }

        .corner-top-left {
            top: 10px;
            left: 10px;
            border-top: 4px solid;
            border-left: 4px solid;
        }

        .corner-top-right {
            top: 10px;
            right: 10px;
            border-top: 4px solid;
            border-right: 4px solid;
        }

        .corner-bottom-left {
            bottom: 10px;
            left: 10px;
            border-bottom: 4px solid;
            border-left: 4px solid;
        }

        .corner-bottom-right {
            bottom: 10px;
            right: 10px;
            border-bottom: 4px solid;
            border-right: 4px solid;
        }

        /* Certificate seal */
        .certificate-seal {
            position: absolute;
            top: 20px;
            right: 30px;
            width: 110px;
            height: 110px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.7) 0%, rgba(212, 175, 55, 0.1) 70%);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #7a5c00;
            font-size: 14px;
            text-align: center;
            font-weight: bold;
            font-family: 'Playfair Display', serif;
            transform: rotate(10deg);
            text-transform: uppercase;
            line-height: 1.3;
            padding: 10px;
            z-index: 3;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border: 1px dashed #7a5c00;
        }

        .certificate-seal::before {
            content: '';
            position: absolute;
            width: 102px;
            height: 102px;
            border: 1px dashed #7a5c00;
            border-radius: 50%;
        }

        .certificate-header {
            text-align: center;
            margin-bottom: 15px;
            position: relative;
            z-index: 2;
            padding-bottom: 8px;
            border-bottom: 2px solid rgba(212, 175, 55, 0.3);
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }

        .certificate-title {
            color: #0A2558;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
            font-family: 'Playfair Display', serif;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 25px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        .certificate-subtitle {
            color: #0A2558;
            font-size: 20px;
            font-weight: bold;
            font-family: 'Cormorant Garamond', serif;
            letter-spacing: 1px;
        }

        .certificate-content {
            padding: 10px 40px;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .certificate-text {
            font-size: 16px;
            margin: 6px 0;
            line-height: 1.4;
            font-family: 'Cormorant Garamond', serif;
            font-weight: 500;
        }

        .student-name-cert {
            font-size: 24px;
            font-weight: bold;
            color: #0A2558;
            margin: 8px 0;
            font-family: 'Playfair Display', serif;
            border-bottom: 1px solid #d4af37;
            display: inline-block;
            padding: 0 20px 3px;
            text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.05);
            letter-spacing: 0.5px;
        }

        .certificate-details {
            margin: 15px 0;
            text-align: center;
            position: relative;
            z-index: 2;
            background-color: rgba(255, 255, 255, 0.7);
            padding: 12px;
            border-radius: 8px;
            width: 90%;
            margin-left: auto;
            margin-right: auto;
            border: 1px solid rgba(212, 175, 55, 0.3);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        .certificate-detail {
            font-size: 15px;
            margin: 8px 0;
            font-family: 'Montserrat', sans-serif;
            line-height: 1.3;
        }

        .certificate-footer {
            position: absolute;
            bottom: 15px;
            width: calc(100% - 40px);
            text-align: center;
            z-index: 2;
        }

        .signature-line {
            width: 200px;
            height: 1px;
            background-color: #000;
            margin: 30px auto 5px auto;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        /* Ribbon decoration */
        .ribbon {
            position: absolute;
            bottom: 70px;
            left: 30px;
            width: 80px;
            height: 80px;
            z-index: 3;
            opacity: 0.9;
        }

        .ribbon-circle {
            position: absolute;
            width: 60px;
            height: 60px;
            background: radial-gradient(circle, #d4af37 0%, #bb9632 100%);
            border-radius: 50%;
            border: 1px solid #946e00;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ribbon-circle::after {
            content: '';
            position: absolute;
            width: 50px;
            height: 50px;
            border: 1px dashed rgba(255, 255, 255, 0.5);
            border-radius: 50%;
        }

        .ribbon-tail {
            position: absolute;
            top: 45px;
            left: 15px;
            width: 15px;
            height: 40px;
            background: linear-gradient(to right, #d4af37, #bb9632);
            transform: rotate(-35deg);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .ribbon-tail:nth-child(2) {
            left: 35px;
            transform: rotate(35deg);
        }

        .integrity-score-good {
            color: #28a745;
            font-weight: bold;
            padding: 2px 6px;
            background-color: rgba(40, 167, 69, 0.1);
            border-radius: 4px;
            border: 1px solid rgba(40, 167, 69, 0.2);
        }

        .integrity-score-at-risk {
            color: #ffc107;
            font-weight: bold;
            padding: 2px 6px;
            background-color: rgba(255, 193, 7, 0.1);
            border-radius: 4px;
            border: 1px solid rgba(255, 193, 7, 0.2);
        }

        .integrity-score-cheating {
            color: #dc3545;
            font-weight: bold;
            padding: 2px 6px;
            background-color: rgba(220, 53, 69, 0.1);
            border-radius: 4px;
            border: 1px solid rgba(220, 53, 69, 0.2);
        }

        .nft-container {
            background-color: white;
            width: 800px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-bottom: 20px;
        }

        /* Add some spacing between certificate and NFT details */
        .section-title {
            text-align: center;
            margin: 30px 0;
            color: #0A2558;
            font-size: 24px;
            font-weight: 600;
            font-family: 'Playfair Display', serif;
        }

        .nft-container h1 {
            color: #0A2558;
            text-align: center;
            font-family: 'Playfair Display', serif;
            margin-bottom: 30px;
        }

        .student-info {
            margin-bottom: 30px;
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .student-name {
            font-size: 24px;
            font-weight: bold;
            color: #0A2558;
            margin: 10px 0;
        }

        .exam-name {
            font-size: 18px;
            color: #555;
            margin: 5px 0;
        }

        .score-info {
            font-size: 16px;
            color: #666;
            margin: 5px 0;
        }

        .nft-details {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            gap: 30px;
        }

        .nft-meta {
            flex: 1;
            min-width: 300px;
        }

        .nft-field {
            margin-bottom: 20px;
            font-family: 'Montserrat', sans-serif;
        }

        .nft-field .label {
            font-weight: bold;
            color: #555;
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .nft-field .value {
            font-family: monospace;
            padding: 12px;
            background: #f5f7fa;
            border-radius: 4px;
            display: inline-block;
            border: 1px solid #e1e4e8;
            word-break: break-all;
            font-size: 14px;
        }

        .nft-actions {
            margin-top: 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .nft-btn {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            background-color: #0A2558;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .opensea-btn {
            background-color: #2081E2;
        }

        .opensea-btn:hover {
            background-color: #1868b7;
        }

        .etherscan-btn {
            background-color: #21325b;
        }

        .etherscan-btn:hover {
            background-color: #344776;
        }

        .metadata-btn {
            background-color: #19875b;
        }

        .metadata-btn:hover {
            background-color: #1ea36e;
        }

        .nft-btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .nft-btn svg {
            margin-right: 8px;
        }

        .nft-image {
            flex: 1;
            min-width: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .nft-image img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            border: 1px solid #d4af37;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .no-nft {
            text-align: center;
            padding: 40px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            max-width: 600px;
        }

        .no-nft h2 {
            color: #555;
            margin-bottom: 20px;
        }

        .no-nft p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        /* CSS for integrity score classes */
        .integrity-good,
        .integrity-excellent {
            color: #28a745;
            font-weight: bold;
        }

        .integrity-at-risk,
        .integrity-fair {
            color: #ffc107;
            font-weight: bold;
        }

        .integrity-cheating,
        .integrity-cheating-suspicion,
        .integrity-poor,
        .integrity-very-poor {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <a href="viewresults.php" class="back-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
        </svg>
        Back to Results
    </a>

    <?php if ($nft_minted && $nft_data): ?>
        <!-- NFT Certificate Information -->
        <div class="nft-container">
            <h1>Blockchain Certificate (NFT)</h1>

            <div class="student-info">
                <div class="student-name"><?php echo htmlspecialchars($student_name); ?></div>
                <div class="exam-name"><?php echo htmlspecialchars($exam_name); ?> - <?php echo htmlspecialchars($subject); ?></div>
                <div class="score-info">Score: <?php echo $score; ?>/<?php echo $total; ?> (<?php echo $percentage; ?>%)</div>
                <div class="score-info">Integrity Score: <span class="integrity-<?php echo strtolower($integrity_category); ?>"><?php echo $integrity_score; ?>/100</span> (<?php echo $integrity_category; ?>)</div>
                <div class="score-info">Completed on: <?php echo $completion_date; ?></div>
            </div>

            <div class="nft-details">
                <div class="nft-meta">
                    <div class="nft-field">
                        <span class="label">Token ID:</span>
                        <span class="value"><?php echo $nft_data['token_id']; ?></span>
                    </div>
                    <div class="nft-field">
                        <span class="label">Contract Address:</span>
                        <span class="value"><?php echo $nft_data['contract_address']; ?></span>
                    </div>
                    <div class="nft-field">
                        <span class="label">Transaction Hash:</span>
                        <span class="value"><?php echo $nft_data['transaction_hash']; ?></span>
                    </div>
                    <div class="nft-field">
                        <span class="label">Minted On:</span>
                        <span class="value"><?php echo date('F j, Y', strtotime($nft_data['created_at'])); ?></span>
                    </div>
                    <div class="nft-actions">
                        <a href="<?php echo $nft_data['metadata_url']; ?>" target="_blank" class="nft-btn metadata-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z" />
                                <path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z" />
                            </svg>
                            View Metadata
                        </a>
                        <?php
                        // Determine the blockchain explorer to use
                        $tx_link = "";
                        if (strpos($nft_data['contract_address'], '0x') !== false) {
                            // Ethereum or compatible network
                            $explorer_base = "https://sepolia.etherscan.io"; // Default to Sepolia testnet
                            $tx_link = $explorer_base . "/tx/" . $nft_data['transaction_hash'];
                        }
                        if ($tx_link): ?>
                            <a href="<?php echo $tx_link; ?>" target="_blank" class="nft-btn etherscan-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5z" />
                                    <path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0v-5z" />
                                </svg>
                                View on Blockchain
                            </a>
                        <?php endif; ?>

                        <?php
                        // OpenSea link for the NFT
                        $opensea_link = "";
                        if (isset($nft_data['token_id']) && isset($nft_data['contract_address'])) {
                            // For Sepolia testnet
                            $opensea_link = "https://testnets.opensea.io/assets/sepolia/" . $nft_data['contract_address'] . "/" . $nft_data['token_id'];
                        }
                        if ($opensea_link): ?>
                            <a href="<?php echo $opensea_link; ?>" target="_blank" class="nft-btn opensea-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 90 90">
                                    <path d="M45 0C20.2 0 0 20.2 0 45s20.2 45 45 45 45-20.2 45-45S69.8 0 45 0zM22.1 46.2l.2-.3 9.3-14.5c.2-.3.6-.3.8 0 2.2 3.6 4.2 8.3 3.2 11.2-.4 1.2-1.5 2.9-2.7 4.4-.2.2-.3.5-.5.7-.1.1-.2.2-.3.2h-9.7c-.5 0-.7-.5-.3-.8v-.9zm59 13.3c0 .3-.2.6-.5.7-1.9.8-8.3 3.8-11.1 7.6-.2.4-.8.2-.8-.2v-14.5c0-.3.1-.5.3-.6.9-.5 4.2-2.4 8.2-2.4 1.2 0 2.3.2 3.3.5.3.1.5.3.5.6v8.3h.1zm1.8-15.5c-.1.3-.4.5-.7.5-4.4.2-8.7 2.2-11.9 4.5-.3.2-.6 0-.6-.3v-17.6c0-.3.2-.5.4-.6 1.2-.5 5-1.9 10.5-1.9 4.2 0 6.9 1.8 7.4 2.1.2.1.3.3.3.5v6c0 .3 0 .5-.3.7-1.5 1-3.6 2.5-4.8 5.4-.1.3-.4.4-.7.4h-.7c-.3 0-.5-.2-.6-.5-.3-1.3-.9-3.2-2.8-5.8-.1-.1 0-.3.1-.3.6-.2 1.5-.7 3.8-1.5.3-.1.6-.5.6-.9V33c0-.3-.2-.6-.5-.6-.9-.3-2.6-.6-4.1-.6-1.6 0-3.2.3-4.1.6-.3.1-.5.3-.5.6v3.9c0 .4.3.7.6.9 2.3.8 3.2 1.3 3.8 1.5.2.1.2.3.1.3-1.9 2.6-2.5 4.5-2.8 5.8-.1.3-.3.5-.6.5h-.7c-.3 0-.6-.2-.7-.4-1.2-2.9-3.3-4.3-4.8-5.4-.2-.2-.3-.4-.3-.7v-6c0-.2.1-.4.3-.5.5-.3 3.2-2.1 7.4-2.1 5.5 0 9.3 1.4 10.5 1.9.3.1.4.3.4.6v17.6c0 .3-.3.5-.6.3-3.2-2.3-7.4-4.3-11.9-4.5-.3 0-.6-.2-.7-.5-1.6-4.2-3.5-6.6-4.8-8.1-.1-.1-.1-.3 0-.5.2-.3.7-.9 1.4-1.8.2-.3.1-.7-.2-.9-.8-.3-1.4-.5-2.5-.7-.3 0-.5-.2-.6-.5v-5.3c0-.3.2-.6.5-.6 1-.3 3.2-.7 5.9-.7s5 .4 5.9.7c.3.1.5.3.5.6v5.3c0 .3-.2.5-.5.5-1.1.1-1.8.3-2.5.7-.3.1-.3.6-.2.9.7.9 1.2 1.5 1.5 1.8.1.1.1.3 0 .5-1.4 1.4-3.3 3.9-4.8 8.1z" />
                                </svg>
                                View on OpenSea
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
                // Check if image URL exists, or use the one provided in the query if this is a test
                $has_image = isset($nft_data['image_url']) && !empty($nft_data['image_url']);
                $test_image = "ipfs://bafybeictwq6pmi6ml2arc2gmfls6ytwwjtv3jj3tpbdcjcvemtyqcgx6aa";

                if ($has_image || isset($_GET['test_image'])):
                ?>
                    <div class="nft-image">
                        <?php
                        // Convert IPFS URI to gateway URL if needed
                        $image_url = $has_image ? $nft_data['image_url'] : $test_image;

                        if (strpos($image_url, 'ipfs://') === 0) {
                            // Extract the CID (Content Identifier)
                            $cid = str_replace('ipfs://', '', $image_url);
                            // Use multiple public IPFS gateways with fallbacks
                            $image_url = "https://gateway.pinata.cloud/ipfs/{$cid}";
                        }
                        ?>
                        <img src="<?php echo $image_url; ?>" alt="NFT Certificate Image" style="max-width: 100%; border: 1px solid #d4af37; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-radius: 8px;">
                        <?php if (isset($_GET['test_image'])): ?>
                            <p class="status-note" style="text-align: center; margin-top: 10px;"><i>Demo image shown for testing purposes</i></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="no-nft">
            <h2>No Blockchain Certificate Found</h2>
            <p>This student has not yet minted an NFT certificate for this exam.</p>
            <p>Once they complete the minting process, the blockchain certificate details will be available here.</p>
            <p>Students can mint their certificates as NFTs from their results page.</p>

            <?php if (isset($_GET['test_image'])): ?>
                <div style="margin-top: 30px; text-align: center;">
                    <h3>Sample NFT Certificate Preview</h3>
                    <div style="max-width: 500px; margin: 20px auto; border: 1px solid #d4af37; padding: 10px; border-radius: 10px; background-color: #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                        <?php
                        $test_image = "ipfs://bafybeictwq6pmi6ml2arc2gmfls6ytwwjtv3jj3tpbdcjcvemtyqcgx6aa";
                        $cid = str_replace('ipfs://', '', $test_image);
                        $image_url = "https://gateway.pinata.cloud/ipfs/{$cid}";
                        ?>
                        <img src="<?php echo $image_url; ?>" alt="Sample NFT Certificate Image" style="max-width: 100%; border-radius: 8px;">
                        <p class="status-note" style="text-align: center; margin-top: 10px;"><i>Demo NFT image shown for preview purposes</i></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <script src="../js/script.js"></script>
</body>

</html>
<?php
// End the script to prevent any additional output
exit();
?>