<?php
/**
 * Utility function to send emails with attachments using PHPMailer
 */

// Include PHPMailer classes
require_once __DIR__ . '/../vendor/phpmailer/PHPMailer-6.8.1/src/Exception.php';
require_once __DIR__ . '/../vendor/phpmailer/PHPMailer-6.8.1/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/PHPMailer-6.8.1/src/SMTP.php';

// Include email configuration
require_once __DIR__ . '/email_config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

/**
 * Sends an email with an attachment using PHPMailer
 * 
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $message Email body (HTML)
 * @param string $attachment_path Path to file to attach
 * @param string $attachment_name Optional name for the attachment file
 * @return bool True if email sent successfully, false otherwise
 */
function send_email_with_attachment($to, $subject, $message, $attachment_path = null, $attachment_name = null) {
    try {
        // Default sender info
        $from_email = FROM_EMAIL;
        $from_name = FROM_NAME;
        
        // Create a new PHPMailer instance
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->SMTPDebug = SMTP_DEBUG;       // Enable verbose debug output
        $mail->isSMTP();                     // Send using SMTP
        $mail->Host       = SMTP_SERVER;     // Set the SMTP server
        $mail->SMTPAuth   = true;            // Enable SMTP authentication
        $mail->Username   = SMTP_USERNAME;   // SMTP username
        $mail->Password   = SMTP_PASSWORD;   // SMTP password
        $mail->SMTPSecure = SMTP_SECURE;     // Enable TLS encryption
        $mail->Port       = SMTP_PORT;       // TCP port to connect to
        
        // Recipients
        $mail->setFrom($from_email, $from_name);
        $mail->addAddress($to);              // Add a recipient
        $mail->addReplyTo($from_email, $from_name);
        
        // Content
        $mail->isHTML(true);                 // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $message)); // Plain text version
        
        // Attachment
        if ($attachment_path && file_exists($attachment_path)) {
            // Check if file is readable
            if (!is_readable($attachment_path)) {
                error_log("Cannot read attachment file: $attachment_path");
                return false;
            }
            
            // Add attachment
            if (!$attachment_name) {
                $attachment_name = basename($attachment_path);
            }
            
            $mail->addAttachment($attachment_path, $attachment_name);
        }
        
        // Send the mail
        $sent = $mail->send();
        
        // Log status
        error_log("Email to $to " . ($sent ? "sent successfully" : "failed to send"));
        if (!$sent) {
            error_log("PHPMailer Error: " . $mail->ErrorInfo);
        }
        
        return $sent;
    } catch (Exception $e) {
        error_log("Exception in send_email_with_attachment: " . $e->getMessage());
        return false;
    }
}

/**
 * Sends NFT certificate as email attachment
 * 
 * @param string $student_uname Username of the student
 * @param int $attempt_id Attempt ID of the certificate
 * @param string $image_path Path to the NFT certificate image
 * @return bool True if email sent successfully, false otherwise
 */
function send_nft_certificate_email($student_uname, $attempt_id, $image_path) {
    global $conn;
    
    try {
        // Validate inputs
        if (empty($student_uname) || empty($attempt_id) || empty($image_path)) {
            error_log("Missing required parameters in send_nft_certificate_email");
            return false;
        }
        
        // Check if image exists
        if (!file_exists($image_path)) {
            error_log("Certificate image not found at path: $image_path");
            return false;
        }
        
        // Get student email from database
        $student_uname = mysqli_real_escape_string($conn, $student_uname);
        $sql = "SELECT email, fname FROM student WHERE uname = '$student_uname'";
        $result = mysqli_query($conn, $sql);
        
        if (!$result || mysqli_num_rows($result) == 0) {
            error_log("Student with username $student_uname not found");
            return false;
        }
        
        $student_data = mysqli_fetch_assoc($result);
        $student_email = $student_data['email'];
        $student_name = $student_data['fname'];
        
        // Validate email
        if (empty($student_email) || !filter_var($student_email, FILTER_VALIDATE_EMAIL)) {
            error_log("Invalid email address for student: $student_uname");
            return false;
        }
        
        // Get exam name and subject
        $attempt_id = intval($attempt_id);
        $sql = "SELECT e.exname, e.subject 
                FROM atmpt_list a 
                JOIN exm_list e ON a.exid = e.exid 
                WHERE a.id = $attempt_id";
        $result = mysqli_query($conn, $sql);
        
        if (!$result || mysqli_num_rows($result) == 0) {
            error_log("Attempt ID $attempt_id not found");
            return false;
        }
        
        $exam_data = mysqli_fetch_assoc($result);
        $exam_name = $exam_data['exname'];
        $subject = $exam_data['subject'];
        
        // Get NFT details
        $sql = "SELECT * FROM certificate_nfts WHERE attempt_id = $attempt_id AND uname = '$student_uname'";
        $result = mysqli_query($conn, $sql);
        
        $transaction_hash = '';
        $token_id = '';
        $contract_address = '';
        $opensea_link = '';
        $etherscan_link = '';
        
        if ($result && mysqli_num_rows($result) > 0) {
            $nft_data = mysqli_fetch_assoc($result);
            $transaction_hash = $nft_data['transaction_hash'];
            $token_id = $nft_data['token_id'];
            $contract_address = $nft_data['contract_address'];
            
            // Create links
            $opensea_link = "https://testnets.opensea.io/assets/sepolia/{$contract_address}/{$token_id}";
            $etherscan_link = "https://sepolia.etherscan.io/tx/{$transaction_hash}";
        }
        
        // Email content
        $email_subject = "Your NFT Certificate for $exam_name";
        
        $message = "
        <html>
        <head>
            <title>Your NFT Certificate</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #4CAF50; color: white; padding: 10px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
                .blockchain-details { background-color: #f0f8ff; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .link { color: #0066cc; text-decoration: underline; word-break: break-all; }
                .transaction-hash { font-family: monospace; background-color: #f5f5f5; padding: 5px; border-radius: 3px; word-break: break-all; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Congratulations, $student_name!</h2>
                </div>
                <div class='content'>
                    <p>We are pleased to attach your NFT Certificate for completing the $exam_name in $subject.</p>
                    <p>Your certificate has been minted as an NFT on the blockchain, ensuring its authenticity and permanence.</p>
                    <p>The NFT certificate is attached to this email as a PNG file. You can view or print this certificate whenever needed.</p>
                    
                    <div class='blockchain-details'>
                        <h3>Blockchain Details</h3>";
                        
        if (!empty($transaction_hash)) {
            $message .= "
                        <p><strong>Transaction Hash:</strong><br>
                        <span class='transaction-hash'>$transaction_hash</span></p>
                        
                        <p><strong>View Transaction on Etherscan:</strong><br>
                        <a href='$etherscan_link' target='_blank' class='link'>$etherscan_link</a></p>";
        }
                        
        if (!empty($opensea_link)) {
            $message .= "
                        <p><strong>View on OpenSea:</strong><br>
                        <a href='$opensea_link' target='_blank' class='link'>$opensea_link</a></p>";
        }
                        
        $message .= "
                    </div>
                    
                    <p>Thank you for your dedication to learning!</p>
                </div>
                <div class='footer'>
                    <p>This is an automated message. Please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $attachment_name = "NFT_Certificate_{$student_uname}_{$attempt_id}.png";
        
        // Send email with certificate attachment
        return send_email_with_attachment($student_email, $email_subject, $message, $image_path, $attachment_name);
    } catch (Exception $e) {
        error_log("Exception in send_nft_certificate_email: " . $e->getMessage());
        return false;
    }
}
?> 