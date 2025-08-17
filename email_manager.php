<?php
include('includes/db_connect.php'); // Include database connection

// CSRF Token Generation
session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate CSRF token
}

// Fetch Emails with Pagination
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$rowsPerPage = 50;
$offset = ($page - 1) * $rowsPerPage;

$stmt = $pdo->prepare("SELECT * FROM email_storage ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $rowsPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$emails = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalEmails = $pdo->query("SELECT COUNT(*) FROM email_storage")->fetchColumn();
$totalPages = ceil($totalEmails / $rowsPerPage);

// Handle CSV Export
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'export_only') {
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token mismatch');
    }
    $selectedEmails = $_POST['selectedEmails'] ?? [];
    if (!empty($selectedEmails)) {
        // Fetch data for selected emails
        $placeholders = implode(',', array_fill(0, count($selectedEmails), '?'));
        $stmt = $pdo->prepare("SELECT * FROM email_storage WHERE email IN ($placeholders)");
        $stmt->execute($selectedEmails);
        $emailsToExport = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($emailsToExport)) {
            // Create CSV
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="exported_emails.csv"');
            $output = fopen('php://output', 'w');
            fputcsv($output, ['name', 'email', 'company']);
            foreach ($emailsToExport as $email) {
                fputcsv($output, [$email['name'], $email['email'], $email['company']]);
            }
            fclose($output);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'No data found for selected emails.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No emails selected for export.']);
    }
    exit;
}

// Handle Delete Selected Emails
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_selected') {
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token mismatch');
    }
    $selectedEmails = $_POST['selectedEmails'] ?? [];
    if (!empty($selectedEmails)) {
        // Generate placeholders for email deletion
        $placeholders = implode(',', array_fill(0, count($selectedEmails), '?'));

        // Prepare and execute the DELETE query using email
        $stmt = $pdo->prepare("DELETE FROM email_storage WHERE email IN ($placeholders)");
        $stmt->execute($selectedEmails);
    }
    header("Location: email_manager.php");
    exit;
}

// Handle Delete All Emails
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_all') {
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token mismatch');
    }
    // Prepare and execute the DELETE query to delete all emails
    $stmt = $pdo->prepare("DELETE FROM email_storage");
    $stmt->execute();
    header("Location: email_manager.php");
    exit;
}

// Handle CSV File Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    if ($_FILES['csv_file']['error'] == UPLOAD_ERR_OK) {
        // Check if file is CSV
        $fileType = mime_content_type($_FILES['csv_file']['tmp_name']);
        if ($fileType !== 'text/csv') {
            echo "<script>alert('Please upload a valid CSV file.');</script>";
            exit;
        }

        $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
        $header = fgetcsv($file); // Skip the header row
        while (($row = fgetcsv($file)) !== false) {
            $email = $row[0];
            $name = $row[1];
            $company = $row[2];

            // Ensure email doesn't already exist
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM email_storage WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() == 0) {
                $stmt = $pdo->prepare("INSERT INTO email_storage (email, name, company) VALUES (?, ?, ?)");
                $stmt->execute([$email, $name, $company]);
            }
        }
        fclose($file);
        echo "<script>alert('CSV file uploaded successfully.'); window.location.reload();</script>";
    } else {
        echo "<script>alert('Error uploading CSV file.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Manager</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-800 text-white p-6">
<div class="max-w-5xl mx-auto bg-gray-900 shadow-md rounded-lg p-6">
    <h1 class="text-2xl font-bold mb-4">Email Manager</h1>

    <!-- Actions -->
    <div class="mt-4 flex space-x-4">
        <button id="exportBtn" type="button" class="bg-green-600 text-white px-4 py-2 rounded">
            Export Selected
        </button>
    </div>

    <!-- Delete All Emails Form -->
    <form method="POST" action="email_manager.php" class="mt-4">
        <input type="hidden" name="action" value="delete_all">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded"
                onclick="return confirm('Are you sure you want to delete all emails?');">
            Delete All Emails
        </button>
    </form>

    <!-- CSV Upload Form -->
    <form method="POST" enctype="multipart/form-data" class="mt-4">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <label for="csv_file" class="text-gray-400">Upload CSV:</label>
        <input type="file" name="csv_file" accept=".csv" class="bg-gray-700 text-white p-2 rounded">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded ml-4">
            Upload CSV
        </button>
    </form>

    <!-- Email Table -->
    <form id="deleteSelectedForm" method="POST" action="email_manager.php">
        <input type="hidden" name="action" value="delete_selected">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <table class="w-full border-collapse bg-gray-800 mt-6">
            <thead>
                <tr class="bg-gray-700">
                    <th class="border px-4 py-2"><input type="checkbox" id="selectAll"></th>
                    <th class="border px-4 py-2">Email</th>
                    <th class="border px-4 py-2">Name</th>
                    <th class="border px-4 py-2">Company</th>
                    <th class="border px-4 py-2">Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($emails as $email): ?>
                    <tr class="border-b border-gray-600">
                        <td class="border px-4 py-2 text-center">
                            <input type="checkbox" name="selectedEmails[]" value="<?php echo $email['email']; ?>">
                        </td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($email['email']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($email['name']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($email['company']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($email['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded"
                onclick="return confirm('Are you sure you want to delete the selected emails?');">
            Delete Selected
        </button>
    </form>

    <!-- Pagination -->
    <div class="mt-6 flex justify-between items-center">
        <a href="?page=<?php echo max(1, $page - 1); ?>" class="text-blue-400">&larr; Previous</a>
        <span>Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
        <a href="?page=<?php echo min($totalPages, $page + 1); ?>" class="text-blue-400">Next &rarr;</a>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#selectAll').on('change', function () {
        $('input[name="selectedEmails[]"]').prop('checked', this.checked);
    });

    $('#exportBtn').on('click', function () {
        var selectedEmails = [];
        $('input[name="selectedEmails[]"]:checked').each(function () {
            selectedEmails.push($(this).val());
        });

        if (selectedEmails.length > 0) {
            var form = $('<form>', {
                action: 'email_manager.php',
                method: 'POST'
            });
            form.append('<input type="hidden" name="action" value="export_only">');
            form.append('<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">');
            $.each(selectedEmails, function (index, email) {
                form.append('<input type="hidden" name="selectedEmails[]" value="' + email + '">');
            });
            $('body').append(form);
            form.submit();
        } else {
            alert('Please select at least one email to export.');
        }
    });
});
</script>
</body>
</html>
