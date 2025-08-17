<?php
include('includes/db_connect.php');

header('Content-Type: application/json');

$result = $mysqli->query("SELECT * FROM email_logs ORDER BY id DESC LIMIT 50"); // Adjust the limit as necessary
echo json_encode($result->fetch_all(MYSQLI_ASSOC));
?>
