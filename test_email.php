<?php
// Test script for email functionality
include 'config.php';
require_once 'utils/mailer.php';

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["send_test"])) {
    $test_email = $_POST["email"] ?? 'test@example.com';
} else {
    $test_email = '';
}

// Create test image if it doesn't exist
$test_image_path = 'certificates/test_certificate.png';
if (!file_exists($test_image_path)) {
    // Create certificates directory if it doesn't exist
    if (!file_exists('certificates')) {
        mkdir('certificates', 0755, true);
    }

    // Create test image
    $width = 800;
    $height = 600;
    $image = imagecreatetruecolor($width, $height);
    $background_color = imagecolorallocate($image, 255, 255, 255);
    $text_color = imagecolorallocate($image, 0, 0, 0);
    imagefilledrectangle($image, 0, 0, $width, $height, $background_color);
    
    // Try to use a font if available, otherwise use default
    $font_path = 'C:/Windows/Fonts/Arial.ttf';
    if (file_exists($font_path)) {
        imagettftext($image, 20, 0, 100, 300, $text_color, $font_path, 'This is a test certificate');
    } else {
        imagestring($image, 5, 100, 300, 'This is a test certificate', $text_color);
    }
    
    // Save the test image
    imagepng($image, $test_image_path);
    imagedestroy($image);
}

// Test result message
$result_message = '';

// Send test email
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["send_test"])) {
    // Validate email
    if (empty($test_email) || !filter_var($test_email, FILTER_VALIDATE_EMAIL)) {
        $result_message = "<p style='color: red;'>Please enter a valid email address.</p>";
    } else {
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

        $result = send_email_with_attachment($test_email, $subject, $message, $test_image_path);

        if ($result) {
            $result_message = "<p style='color: green;'>Email sent successfully to $test_email</p>";
            $result_message .= "<p>Check your email inbox (and spam folder) for the test email.</p>";
        } else {
            $result_message = "<p style='color: red;'>Email sending failed.</p>";
            $result_message .= "<p>Please check the server's mail configuration.</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Email Functionality</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .card {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1, h2, h3 {
            color: #333;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="email"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        pre {
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
        img {
            max-width: 100%;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <h1>Email Test Tool</h1>
    
    <div class="card">
        <h2>Email Configuration</h2>
        <p>The following email configuration is being used:</p>
        <pre>
SMTP Server: <?php echo SMTP_SERVER; ?>
SMTP Port: <?php echo SMTP_PORT; ?>
SMTP Security: <?php echo SMTP_SECURE; ?>
From Email: <?php echo FROM_EMAIL; ?>
From Name: <?php echo FROM_NAME; ?>
        </pre>
    </div>
    
    <div class="card">
        <h2>Send Test Email</h2>
        <form method="post" action="">
            <label for="email">Enter email address to send test to:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($test_email); ?>" required>
            <button type="submit" name="send_test">Send Test Email</button>
        </form>
        
        <?php if (!empty($result_message)): ?>
            <div style="margin-top: 20px;">
                <h3>Test Result</h3>
                <?php echo $result_message; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="card">
        <h3>Test Certificate Image:</h3>
        <img src="<?php echo $test_image_path; ?>" alt="Test Certificate">
    </div>
    
    <div class="card">
        <h2>Email Troubleshooting</h2>
        <ul>
            <li>If you're using Gmail, make sure you have <a href="https://myaccount.google.com/apppasswords" target="_blank">created an App Password</a> and have enabled 2-Factor Authentication.</li>
            <li>Check PHP error logs for detailed error messages.</li>
            <li>Make sure the SMTP port is not blocked by your firewall.</li>
            <li>If using Gmail, check if "Less secure app access" is enabled (though using App Passwords is recommended).</li>
        </ul>
    </div>
</body>
</html> 