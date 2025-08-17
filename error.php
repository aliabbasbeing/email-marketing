<?php
$error_message = $_GET['error'] ?? 'An unknown error occurred.';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
</head>
<body>
    <h1>Error</h1>
    <p style="color:red;"><?= htmlspecialchars($error_message) ?></p>
    <a href="index.php">Go Back</a>
</body>
</html>
