<?php
include('includes/db_connect.php');
error_log("Processing row: " . print_r($row, true));

if ($_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
    $csvFile = fopen($_FILES['csv_file']['tmp_name'], 'r');

    // Clear any existing data in the email_queue
    $pdo->query("DELETE FROM email_queue");

    // Skip header row if present
    fgetcsv($csvFile);

    $rowCount = 0; // Counter to track how many rows are processed

    while (($row = fgetcsv($csvFile)) !== FALSE) {
        $email = filter_var(trim($row[0]), FILTER_VALIDATE_EMAIL);

        if ($email) {
            $stmt = $pdo->prepare("INSERT INTO email_queue (email) VALUES (?)");
            $stmt->execute([$email]);
            $rowCount++;
        }
    }

    fclose($csvFile);

    if ($rowCount > 0) {
        header('Location: index.php');
    } else {
        header('Location: index.php?error=No valid emails found in the CSV file.');
    }
} else {
    header('Location: index.php?error=Failed to upload CSV.');
}
