<?php
// bounce_handler.php

// Read incoming POST data (bounce notification from email service)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Example: Expected format may vary based on your email provider (Mailgun, SendGrid, etc.)
    $bounceData = json_decode(file_get_contents('php://input'), true);
    
    // Check if it's a bounce notification
    if (isset($bounceData['event']) && $bounceData['event'] === 'bounce') {
        // Extract data from the webhook payload
        $email = $bounceData['recipient'];  // Email address that bounced
        $bounceType = $bounceData['bounce']['type'];  // Hard or soft bounce
        $bounceReason = $bounceData['bounce']['reason'];  // Bounce reason
        
        // Connect to the database
        include('includes/db_connect.php');
        
        // Update the bounce status and message in the database
        $stmt = $pdo->prepare("UPDATE progress_tracker SET status = :status, bounce_message = :bounce_message WHERE email = :email");
        $stmt->execute([
            ':status' => 'bounced',  // Mark as bounced
            ':bounce_message' => $bounceReason,  // Store the bounce reason
            ':email' => $email
        ]);

        echo json_encode(['status' => 'success']);
        exit;
    }

    // If not a bounce event, do nothing
    echo json_encode(['status' => 'not a bounce event']);
    exit;
}
?>