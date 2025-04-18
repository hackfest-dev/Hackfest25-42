<?php
// Test script for email functionality
include 'config.php';
require_once 'utils/mailer.php';

// Create test image
$width = 800;
$height = 600;
$image = imagecreatetruecolor($width, $height);
$background_color = imagecolorallocate($image, 255, 255, 255);
$text_color = imagecolorallocate($image, 0, 0, 0);
imagefilledrectangle($image, 0, 0, $width, $height, $background_color);
imagettftext($image, 20, 0, 100, 300, $text_color, 'C:/Windows/Fonts/Arial.ttf', 'This is a test certificate');

// Save the test image
$test_image_path = 'certificates/test_certificate.png';
imagepng($image, $test_image_path);
imagedestroy($image);

// Check if the file was created
if (!file_exists($test_image_path)) {
    die("Failed to create test image");
}

// Test mail parameters
$test_email = 'test@example.com'; // Replace with your email for testing
$subject = 'Test NFT Certificate Email';
$message = '
<html>
<head>
    <title>Test NFT Certificate Email</title>
</head>
<body>
    <p>This is a test email to verify the NFT certificate email functionality.</p>
    <p>If you receive this email with the attached certificate, the system is working correctly.</p>
</body>
</html>
';

// Send test email
$result = send_email_with_attachment($test_email, $subject, $message, $test_image_path);

// Output result
echo "<h2>Email Test Result</h2>";
if ($result) {
    echo "<p style='color: green;'>Email sent successfully to $test_email</p>";
    echo "<p>Check your email inbox (and spam folder) for the test email.</p>";
} else {
    echo "<p style='color: red;'>Email sending failed.</p>";
    echo "<p>Please check the server's mail configuration.</p>";
    
    // Display mail service status
    if (function_exists('mail')) {
        echo "<p>Mail function is available.</p>";
    } else {
        echo "<p>Mail function is not available. Please check PHP configuration.</p>";
    }
    
    // Display PHP mail configuration
    echo "<h3>PHP Mail Configuration:</h3>";
    echo "<pre>";
    echo "SMTP: " . ini_get('SMTP') . "\n";
    echo "smtp_port: " . ini_get('smtp_port') . "\n";
    echo "sendmail_from: " . ini_get('sendmail_from') . "\n";
    echo "mail.add_x_header: " . ini_get('mail.add_x_header') . "\n";
    echo "mail.log: " . ini_get('mail.log') . "\n";
    echo "</pre>";
}

// Display test image
echo "<h3>Test Certificate Image:</h3>";
echo "<img src='$test_image_path' style='max-width: 500px; border: 1px solid #ccc;'>";
?> 