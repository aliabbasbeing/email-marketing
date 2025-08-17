<?php
include('includes/db_connect.php');

// Fetch the results from the email_logs table
$stmt = $pdo->query("SELECT * FROM email_logs ORDER BY id DESC");
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campaign Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1e1e2f;
            color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #c5c6c7;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #282a36;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #44475a;
        }
        th {
            background-color: #6272a4;
            color: #f5f5f5;
        }
        tr:hover {
            background-color: #44475a;
        }
        .back-button {
            background-color: #6272a4;
            color: #f5f5f5;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
            display: block;
            text-align: center;
        }
        .back-button:hover {
            background-color: #50fa7b;
        }
    </style>
</head>
<body>
    <h1>Campaign Results</h1>
    <table>
        <thead>
            <tr>
                <th>Email</th>
                <th>Status</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?php echo htmlspecialchars($log['email']); ?></td>
                    <td><?php echo htmlspecialchars($log['status']); ?></td>
                    <td><?php echo htmlspecialchars($log['timestamp']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="index.php" class="back-button">Back to Main Page</a>
</body>
</html>
