<?php
/**
 * Email Configuration
 * 
 * IMPORTANT: For Gmail, you need to:
 * 1. Enable 2-Factor Authentication on your Google account
 * 2. Create an App Password: https://myaccount.google.com/apppasswords
 * 3. Use that App Password below (not your regular Gmail password)
 */

// SMTP server settings
define('SMTP_SERVER', 'smtp.gmail.com');  // SMTP server (e.g., smtp.gmail.com for Gmail)
define('SMTP_PORT', 587);                 // SMTP port (587 for TLS, 465 for SSL)
define('SMTP_SECURE', 'tls');             // 'tls' or 'ssl'

// SMTP authentication
define('SMTP_USERNAME', 'apexzzz26@gmail.com');  // Your email address
define('SMTP_PASSWORD', 'dyvo hcfr fkqf bxoj');     // Your app password (NOT your regular password)

// Sender information
define('FROM_EMAIL', SMTP_USERNAME);       // Usually same as SMTP_USERNAME
define('FROM_NAME', 'EduCertify System');  // Name shown in the "From" field

// Debug level (0-4)
// 0 = No output
// 1 = Client commands
// 2 = Client commands and server responses
// 3 = As 2, plus connection status
// 4 = Low-level data output
define('SMTP_DEBUG', 0);

?> 