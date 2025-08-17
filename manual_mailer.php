<?php
include('includes/db_connect.php');
include('includes/smtp_config.php');
require 'vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$emailsSent = 0;
$errors = [];

try {
    if (!empty($_POST['manual_email']) && is_array($_POST['manual_email'])) {
        $emailsData = [];

        // Process manual emails
        foreach ($_POST['manual_email'] as $index => $email) {
            $name = $_POST['manual_name'][$index] ?? '';
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emailsData[] = [
                    'name' => $name,
                    'email' => $email
                ];
            } else {
                $errors[] = "Invalid email format: " . htmlspecialchars($email);
            }
        }

        $totalEmails = count($emailsData);

        if ($totalEmails > 0) {
            foreach ($emailsData as $data) {
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = $smtpHost;
                    $mail->SMTPAuth = true;
                    $mail->Username = $smtpUser;
                    $mail->Password = $smtpPass;
                    $mail->SMTPSecure = $smtpSecure;
                    $mail->Port = $smtpPort;

                    $mail->setFrom($smtpFromEmail, $smtpFromName);
                    $mail->addAddress($data['email']);
                    $mail->isHTML(true);

                    // Replace placeholders with data from the manual input
                    $subject = $_POST['subject'];
                    $body = $_POST['body'];
                    foreach ($data as $key => $value) {
                        $placeholder = '{' . $key . '}';
                        $subject = str_replace($placeholder, htmlspecialchars($value), $subject);
                        $body = str_replace($placeholder, nl2br(htmlspecialchars($value)), $body);
                    }
                    $mail->Subject = $subject;
                    $mail->Body = $body;

                    // Attach file if uploaded
                    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                        $fileName = $_FILES['attachment']['name'];
                        $fileTmpPath = $_FILES['attachment']['tmp_name'];
                        $fileType = mime_content_type($fileTmpPath);
                        $mail->addAttachment($fileTmpPath, $fileName, 'base64', $fileType);
                    }

                    if ($mail->send()) {
                        $emailsSent++;
                    } else {
                        $errors[] = "Failed to send email to {$data['email']}: " . $mail->ErrorInfo;
                    }

                    // Send progress update
                    $progressResponse = [
                        'emailsSent' => $emailsSent,
                        'emailsRemaining' => $totalEmails - $emailsSent
                    ];
                    echo json_encode($progressResponse) . "\n";
                    ob_flush();
                    flush();

                    sleep((int)$_POST['delay']);
                } catch (Exception $e) {
                    $errors[] = "Failed to send email to {$data['email']}: " . $e->getMessage();
                }
            }

            // Final response after completion
            $finalResponse = [
                'message' => "Emails Sent: $emailsSent/$totalEmails"
            ];
            echo json_encode($finalResponse);
            ob_flush();
            flush();
        } else {
            $errors[] = "No valid emails found in the manual input.";
        }
    } else {
        $errors[] = "No manual emails provided.";
    }
} catch (Exception $e) {
    $errors[] = "Error occurred: " . $e->getMessage();
}

// Handle errors
if (!empty($errors)) {
    $errorResponse = [
        'errors' => $errors
    ];
    echo json_encode($errorResponse);
    ob_flush();
    flush();
}
?>
