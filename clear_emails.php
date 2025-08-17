<?php
include('includes/db_connect.php');

// Clear all emails from the email_queue
$pdo->query("DELETE FROM email_queue");

header('Location: index.php?cleared=true');
