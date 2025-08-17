<?php
include('includes/db_connect.php');

// Validate and fetch campaign details
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid campaign ID.");
}

$campaignId = (int)$_GET['id'];

// Fetch campaign metadata
$stmt = $pdo->prepare("SELECT * FROM campaigns WHERE id = :id");
$stmt->execute([':id' => $campaignId]);
$campaign = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$campaign) {
    die("Campaign not found.");
}

// Fetch campaign details
$stmt = $pdo->prepare("SELECT * FROM campaign_details WHERE campaign_id = :id");
$stmt->execute([':id' => $campaignId]);
$campaignDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($campaign['name']); ?> - Campaign Details</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-900 text-gray-100 font-sans p-6">
    <header class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-semibold text-blue-400"><?php echo htmlspecialchars($campaign['name']); ?></h1>
        <a href="javascript:void(0)" 
   onclick="window.close(); window.opener.location.href='dashboard.php';" 
   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
    <i class="fas fa-arrow-left"></i> Back to Dashboard
</a>

    </header>

    <div class="bg-gray-800 rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-semibold text-indigo-300 mb-4">Campaign Details</h2>
        <?php if (count($campaignDetails) > 0): ?>
            <table class="w-full border-collapse bg-gray-800 rounded-lg overflow-hidden shadow-lg">
                <thead>
                    <tr class="bg-indigo-700 text-white">
                        <th class="border border-gray-700 px-4 py-2">Email</th>
                        <th class="border border-gray-700 px-4 py-2">Name</th>
                        <th class="border border-gray-700 px-4 py-2">Status</th>
                        <th class="border border-gray-700 px-4 py-2">Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($campaignDetails as $detail): ?>
                        <tr class="hover:bg-gray-700">
                            <td class="border border-gray-700 px-4 py-2 text-indigo-200">
                                <?php echo htmlspecialchars($detail['email']); ?>
                            </td>
                            <td class="border border-gray-700 px-4 py-2 text-indigo-200">
                                <?php echo htmlspecialchars($detail['name']); ?>
                            </td>
                            <td class="border border-gray-700 px-4 py-2 <?php echo $detail['status'] === 'sent' ? 'text-green-500' : 'text-red-500'; ?>">
                                <?php echo htmlspecialchars($detail['status']); ?>
                            </td>
                            <td class="border border-gray-700 px-4 py-2 text-indigo-200">
                                <?php echo htmlspecialchars($detail['timestamp']); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-gray-400 text-center py-4">No details available for this campaign.</p>
        <?php endif; ?>
    </div>
</body>
</html>
