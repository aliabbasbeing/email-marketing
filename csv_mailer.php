<?php
session_start(); // Start session to store subject, body, delay, and control campaign
include('includes/db_connect.php'); // Database connection
include('includes/smtp_config.php'); // SMTP configuration
require 'vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Handle "stop" signal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'stop') {
    $_SESSION['stop_campaign'] = true; // Set the stop signal in session
    echo json_encode(['message' => 'Campaign stopped successfully.']);
    exit;
}

// Handle uploading CSV file and initializing campaign
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $csvFile = fopen($_FILES['csv_file']['tmp_name'], 'r');

    // Read the header row
    $headers = fgetcsv($csvFile);
    $emailsData = [];

    // Process rows
    while (($row = fgetcsv($csvFile)) !== false) {
        $rowData = array_combine($headers, $row);
        if (isset($rowData['email']) && filter_var($rowData['email'], FILTER_VALIDATE_EMAIL)) {
            $emailsData[] = $rowData;
        }
    }
    fclose($csvFile);

    // Save subject, body, and delay to session
    $_SESSION['subject'] = $_POST['subject'];
    $_SESSION['body'] = $_POST['body'];
    $_SESSION['delay'] = isset($_POST['delay']) ? (int)$_POST['delay'] : 1; // Default to 1 second
    $_SESSION['stop_campaign'] = false; // Reset stop signal for a new campaign

    // Save emails to progress_tracker, including CSV data
    $pdo->query("DELETE FROM progress_tracker"); // Clear old progress
    foreach ($emailsData as $data) {
        $stmt = $pdo->prepare("INSERT INTO progress_tracker (email, status, csv_data) VALUES (:email, 'pending', :csv_data)");
        $stmt->execute([
            ':email' => $data['email'],
            ':csv_data' => json_encode($data) // Store CSV row data as JSON
        ]);
    }

    echo json_encode(['totalEmails' => count($emailsData)]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check for stop signal
    if (isset($_SESSION['stop_campaign']) && $_SESSION['stop_campaign']) {
        echo json_encode(['message' => 'Campaign stopped.', 'progress' => null]);
        exit;
    }

    // Fetch pending email
    $stmt = $pdo->query("SELECT * FROM progress_tracker WHERE status = 'pending' LIMIT 1");
    $emailData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($emailData) {
        $csvData = json_decode($emailData['csv_data'], true); // Decode JSON data
        $email = $emailData['email'];
        $status = 'pending';
        $errorMessage = null;

        try {
            $mail = new PHPMailer(true);

            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host = $smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $smtpUser;
            $mail->Password = $smtpPass;
            $mail->SMTPSecure = $smtpSecure;
            $mail->Port = $smtpPort;

            $mail->setFrom($smtpFromEmail, $smtpFromName);
            $mail->addAddress($email); // Dynamic email from CSV data
            $mail->isHTML(true);

            // Retrieve subject and body from session
            $subject = $_SESSION['subject'];
            $body = $_SESSION['body'];

            // Replace placeholders in subject and body with CSV data
            foreach ($csvData as $key => $value) {
                $placeholder = '{' . $key . '}';
                $subject = str_replace($placeholder, htmlspecialchars($value), $subject);
                $body = str_replace($placeholder, nl2br(htmlspecialchars($value)), $body);
            }

            // Add tracking link to email body using the email column
            $trackingLink = "https://beastsmm.xyz/track.php?email=" . urlencode($email);
            $body = str_replace('{tracking_link}', $trackingLink, $body);

            $mail->Subject = $subject; // Dynamic subject
            $mail->Body = $body;       // Dynamic body

            // Check for stop signal before sending
            if (isset($_SESSION['stop_campaign']) && $_SESSION['stop_campaign']) {
                echo json_encode(['message' => 'Campaign stopped.', 'progress' => null]);
                exit;
            }

            // Send the email
            $mail->send();
            $status = 'sent';

            // Apply the delay before processing the next email
            sleep($_SESSION['delay']); // Delay in seconds
        } catch (Exception $e) {
            $status = 'failed';
            $errorMessage = $e->getMessage();
        }

        // Update progress_tracker
        $stmt = $pdo->prepare("UPDATE progress_tracker SET status = :status, error_message = :error_message WHERE email = :email");
        $stmt->execute([
            ':status' => $status,
            ':error_message' => $errorMessage,
            ':email' => $email
        ]);

        // Send progress update
        $totalEmails = $pdo->query("SELECT COUNT(*) FROM progress_tracker")->fetchColumn();
        $emailsSent = $pdo->query("SELECT COUNT(*) FROM progress_tracker WHERE status = 'sent'")->fetchColumn();
        $progress = round(($emailsSent / $totalEmails) * 100, 2);

        echo json_encode([
            'email' => $email,
            'status' => $status,
            'progress' => $progress
        ]);
    } else {
        echo json_encode(['message' => 'All emails sent.', 'progress' => 100]);
    }
    exit;
}
