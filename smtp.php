<?php
include('includes/db_connect.php'); // Include your database connection file

// Fetch the current SMTP settings
$stmt = $pdo->query("SELECT * FROM smtp_config LIMIT 1");
$smtpSettings = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the form was submitted to update settings
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $smtpHost = $_POST['host'];
    $smtpPort = $_POST['port'];
    $smtpUser = $_POST['username'];
    $smtpPass = $_POST['password'];
    $smtpSecure = $_POST['encryption'];
    $smtpFromEmail = $_POST['from_email'];
    $smtpFromName = $_POST['from_name'];

    // Update the SMTP settings in the database
    try {
        $stmt = $pdo->prepare("UPDATE smtp_config SET host = ?, port = ?, username = ?, password = ?, encryption = ?, from_email = ?, from_name = ?");
        $stmt->execute([$smtpHost, $smtpPort, $smtpUser, $smtpPass, $smtpSecure, $smtpFromEmail, $smtpFromName]);
        $message = "SMTP settings updated successfully.";
    } catch (Exception $e) {
        $error = "Failed to update SMTP settings: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMTP Settings</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 font-sans">
    <div class="min-h-screen flex items-center justify-center">
        <div class="w-full max-w-md bg-gray-800 rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-bold text-center text-gray-100 mb-4">SMTP Settings</h1>

            <?php if (isset($message)): ?>
                <div class="p-4 mb-4 text-green-800 bg-green-200 rounded-lg">
                    <?php echo $message; ?>
                </div>
            <?php elseif (isset($error)): ?>
                <div class="p-4 mb-4 text-red-800 bg-red-200 rounded-lg">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="space-y-4">
                <div>
                    <label for="host" class="block text-sm font-medium mb-1">SMTP Host</label>
                    <input type="text" name="host" id="host" class="w-full p-2 bg-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo htmlspecialchars($smtpSettings['host'] ?? ''); ?>" required>
                </div>

                <div>
                    <label for="port" class="block text-sm font-medium mb-1">SMTP Port</label>
                    <input type="text" name="port" id="port" class="w-full p-2 bg-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo htmlspecialchars($smtpSettings['port'] ?? ''); ?>" required>
                </div>

                <div>
                    <label for="username" class="block text-sm font-medium mb-1">SMTP Username</label>
                    <input type="text" name="username" id="username" class="w-full p-2 bg-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo htmlspecialchars($smtpSettings['username'] ?? ''); ?>" required>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium mb-1">SMTP Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" class="w-full p-2 bg-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo htmlspecialchars($smtpSettings['password'] ?? ''); ?>" required>
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 flex items-center pr-2 text-gray-400 hover:text-gray-100">
                            <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7a11.949 11.949 0 01-9.542 7c-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label for="encryption" class="block text-sm font-medium mb-1">SMTP Encryption</label>
                    <select name="encryption" id="encryption" class="w-full p-2 bg-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="tls" <?php if (isset($smtpSettings['encryption']) && $smtpSettings['encryption'] == 'tls') echo 'selected'; ?>>TLS</option>
                        <option value="ssl" <?php if (isset($smtpSettings['encryption']) && $smtpSettings['encryption'] == 'ssl') echo 'selected'; ?>>SSL</option>
                    </select>
                </div>

                <div>
                    <label for="from_email" class="block text-sm font-medium mb-1">SMTP From Email</label>
                    <input type="email" name="from_email" id="from_email" class="w-full p-2 bg-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo htmlspecialchars($smtpSettings['from_email'] ?? ''); ?>" required>
                </div>

                <div>
                    <label for="from_name" class="block text-sm font-medium mb-1">SMTP From Name</label>
                    <input type="text" name="from_name" id="from_name" class="w-full p-2 bg-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo htmlspecialchars($smtpSettings['from_name'] ?? ''); ?>" required>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded-md transition">
                    Update Settings
                </button>
            </form>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordField = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.97 10.97 0 0112 19c-4.478 0-8.268-2.943-9.542-7a11.949 11.949 0 019.542-7c1.095 0 2.155.148 3.163.423" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.757 3.757l16.486 16.486" />
                `;
            } else {
                passwordField.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7a11.949 11.949 0 01-9.542 7c-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        }
    </script>
</body>
</html>
