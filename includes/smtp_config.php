<?php
// Include the database connection
include 'db_connect.php';

// Fetch SMTP configuration
$stmt = $pdo->query("SELECT * FROM smtp_config LIMIT 1");
$smtp = $stmt->fetch(PDO::FETCH_ASSOC);

$smtpHost = $smtp['host'];
$smtpPort = $smtp['port'];
$smtpUser = $smtp['username'];
$smtpPass = $smtp['password'];
$smtpSecure = $smtp['encryption']; // 'ssl' or 'tls'
$smtpFromEmail = $smtp['from_email'];
$smtpFromName = $smtp['from_name'];
?>
