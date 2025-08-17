<?php
// Include PHPMailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure Composer autoload file is included

// SMTP Configuration
$smtpHost = 'smtp.gmail.com';
$smtpUsername = 'beastsmm98@gmail.com'; // Your Gmail email address
$smtpPassword = 'gxla shts aclr ypkp'; // Generated App Password
$smtpPort = 587; // TLS Port
$smtpSecure = 'tls'; // Secure connection

// Recipient email for testing
$recipientEmail = 'aliabbaszounr213@gmail.com';

try {
    $mail = new PHPMailer(true);

    // SMTP configuration
    $mail->isSMTP();
    $mail->Host = $smtpHost;
    $mail->SMTPAuth = true;
    $mail->Username = $smtpUsername;
    $mail->Password = $smtpPassword;
    $mail->SMTPSecure = $smtpSecure;
    $mail->Port = $smtpPort;

    // Sender and recipient details
    $mail->setFrom($smtpUsername, 'Your Name');
    $mail->addAddress($recipientEmail, 'Test Recipient');

    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'SMTP Configuration Test';
    $mail->Body = '<h1>SMTP Test Successful</h1><p>This email was sent using Gmail SMTP configuration.</p>';
    $mail->AltBody = 'SMTP Test Successful: This email was sent using Gmail SMTP configuration.';

    // Send email
    if ($mail->send()) {
        echo 'Test email sent successfully to ' . $recipientEmail;
    } else {
        echo 'Failed to send email. Error: ' . $mail->ErrorInfo;
    }
} catch (Exception $e) {
    echo 'PHPMailer Error: ' . $e->getMessage();
}
