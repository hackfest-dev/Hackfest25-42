# NFT Certificate Email Functionality

This documentation outlines the automatic email functionality for NFT certificates in the EduCertify platform.

## Overview

After an NFT certificate is generated, the system automatically sends an email to the student's registered email address with the certificate attached as a PNG file. This process happens entirely in the backend, with no additional actions required from the user after hitting the submit button.

## Implementation Details

### Components

1. **Mail Utility (`utils/mailer.php`)**
   - `send_email_with_attachment()`: Generic function for sending emails with attachments
   - `send_nft_certificate_email()`: Specific function for sending NFT certificates

2. **NFT Minting Integration (`students/mint_nft.php`)**
   - Modified to accept certificate image data
   - Saves the certificate image to the server
   - Calls the email utility to send the certificate

3. **Certificate Generation (`students/generate_certificate.php`)**
   - Modified to pass the certificate image data to the minting process
   - Converts the canvas to a base64 image
   - Displays email sending status to user

4. **PHP Mail Configuration (`php.ini`)**
   - Custom configuration for sending emails from localhost

### Flow

1. User clicks "Generate Certificate" or "Mint NFT"
2. The certificate is rendered as HTML and converted to an image
3. The image is sent to the blockchain as metadata
4. The image is also saved locally for email attachment
5. The system retrieves the student's email from the database
6. An email with the certificate attached is sent automatically
7. Success/failure status is logged and optionally displayed to the user

## Localhost Mail Configuration

For local development, the following mail settings are used:

```ini
[mail function]
SMTP = localhost
smtp_port = 25
sendmail_from = noreply@educertify.com
mail.add_x_header = On
mail.log = "C:/xampp/php/logs/mail.log"
```

## Testing

To test the email functionality without going through the entire NFT minting process, use the `test_mail.php` script. This creates a test certificate image and attempts to send it to the specified email address.

## Troubleshooting

If emails are not being sent:

1. Check the PHP mail log at `C:/xampp/php/logs/mail.log`
2. Verify that the SMTP server is running on localhost
3. Ensure the student has a valid email address in the database
4. Check if the certificate image is being generated correctly

## Production Deployment

For production deployment, consider the following:

1. Configure a proper SMTP server with authentication
2. Use a service like SendGrid, Mailgun, or Amazon SES
3. Implement email queue for better performance
4. Add email templates for customization 