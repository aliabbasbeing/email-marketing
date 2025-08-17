<?php
include('includes/db_connect.php'); // Include database connection

// Pagination and Export Logic
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$rowsPerPage = isset($_GET['rowsPerPage']) ? (int)$_GET['rowsPerPage'] : 10;
$offset = ($page - 1) * $rowsPerPage;

// Fetch emails with pagination
$stmt = $pdo->prepare("SELECT * FROM progress_tracker ORDER BY timestamp DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $rowsPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$sentEmails = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total emails count for pagination
$totalEmails = $pdo->query("SELECT COUNT(*) FROM progress_tracker")->fetchColumn();
$totalPages = ceil($totalEmails / $rowsPerPage);

// Export to CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="campaign_results.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Email', 'Name', 'Status', 'Opened', 'Timestamp']);
    foreach ($sentEmails as $email) {
        $csvData = isset($email['csv_data']) ? json_decode($email['csv_data'], true) : [];
        fputcsv($output, [
            $email['email'],
            $csvData['name'] ?? 'N/A',
            $email['status'],
            $email['is_opened'] ? 'Yes' : 'No',
            date('Y-m-d H:i:s', strtotime($email['timestamp']))
        ]);
    }
    fclose($output);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campaign Results</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="script.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-900 text-gray-100 font-sans p-6">
<?php include('header.php'); ?>

<div class="bg-gray-800 rounded-lg shadow-lg p-6">
    <h2 class="text-2xl font-semibold text-indigo-300 mb-4">Email Campaign Data</h2>
    <div class="flex justify-between items-center mb-4">
        <input type="text" id="searchInput" placeholder="Search by email or status..." 
               class="w-1/3 p-2 rounded bg-gray-700 text-gray-200 border border-gray-600">
        <span class="text-gray-400">Total Emails: <?php echo $totalEmails; ?></span>
    </div>

    <div class="flex justify-end mb-4">
        <label for="rowsPerPage" class="text-gray-400 mr-2">Rows per page:</label>
        <select id="rowsPerPage" class="bg-gray-700 text-gray-200 p-2 rounded">
            <option value="10" <?php if ($rowsPerPage == 10) echo 'selected'; ?>>10</option>
            <option value="25" <?php if ($rowsPerPage == 25) echo 'selected'; ?>>25</option>
            <option value="50" <?php if ($rowsPerPage == 50) echo 'selected'; ?>>50</option>
        </select>
    </div>

    <div class="overflow-x-auto">
        <table id="campaignResultsTable" class="w-full border-collapse bg-gray-800 rounded-lg overflow-hidden shadow-lg">
            <thead>
                <tr class="bg-indigo-700 text-white">
                    <th class="border border-gray-700 px-4 py-2">Email</th>
                    <th class="border border-gray-700 px-4 py-2">Name</th>
                    <th class="border border-gray-700 px-4 py-2">Status</th>
                    <th class="border border-gray-700 px-4 py-2">Opened</th>
                    <th class="border border-gray-700 px-4 py-2">Timestamp</th>
                </tr>
            </thead>
            <tbody id="resultsTableBody">
                <?php if (count($sentEmails) > 0): ?>
                    <?php foreach ($sentEmails as $email): ?>
                        <tr>
                            <td class="border border-gray-700 px-4 py-2 text-indigo-200">
                                <?php echo htmlspecialchars($email['email']); ?>
                            </td>
                            <td class="border border-gray-700 px-4 py-2 text-indigo-200">
                                <?php echo isset($email['csv_data']) ? htmlspecialchars(json_decode($email['csv_data'])->name ?? 'N/A') : 'N/A'; ?>
                            </td>
                            <td class="border border-gray-700 px-4 py-2 <?php echo $email['status'] === 'sent' ? 'text-green-500' : 'text-red-500'; ?>">
                                <?php echo htmlspecialchars($email['status']); ?>
                            </td>
                            <td class="border border-gray-700 px-4 py-2 <?php echo $email['is_opened'] ? 'text-green-500' : 'text-red-500'; ?>">
                                <?php echo $email['is_opened'] ? 'Yes' : 'No'; ?>
                            </td>
                            <td class="border border-gray-700 px-4 py-2 text-indigo-200">
                                <?php echo date('Y-m-d H:i:s', strtotime($email['timestamp'])); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-gray-400 py-4">No emails have been sent yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div id="paginationWrapper" class="mt-4 flex justify-center">
        <a href="?page=<?php echo max(1, $page - 1); ?>&rowsPerPage=<?php echo $rowsPerPage; ?>" 
           class="bg-indigo-600 hover:bg-indigo-700 text-white py-1 px-3 rounded mr-2 <?php if ($page == 1) echo 'opacity-50 cursor-not-allowed'; ?>">
            Previous
        </a>
        <span class="text-gray-300">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
        <a href="?page=<?php echo min($totalPages, $page + 1); ?>&rowsPerPage=<?php echo $rowsPerPage; ?>" 
           class="bg-indigo-600 hover:bg-indigo-700 text-white py-1 px-3 rounded ml-2 <?php if ($page == $totalPages) echo 'opacity-50 cursor-not-allowed'; ?>">
            Next
        </a>
    </div>

    <div class="mt-6 flex justify-between">
        <a href="?export=csv" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded shadow-lg">
            <i class="fas fa-file-csv"></i> Export to CSV
        </a>
        <button id="saveCampaignBtn" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded shadow-lg">
            <i class="fas fa-save"></i> Save Campaign
        </button>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#rowsPerPage').change(function () {
            const rowsPerPage = $(this).val();
            window.location.href = `?rowsPerPage=${rowsPerPage}&page=1`;
        });

        $('#saveCampaignBtn').click(function () {
            const campaignName = prompt('Enter a unique campaign name:');
            if (!campaignName || campaignName.trim() === '') {
                alert('Campaign name is required to save.');
                return;
            }

            const campaignData = [];
            $('#resultsTableBody tr').each(function () {
                const cells = $(this).find('td');
                campaignData.push({
                    email: cells.eq(0).text().trim(),
                    name: cells.eq(1).text().trim(),
                    status: cells.eq(2).text().trim(),
                    opened: cells.eq(3).text().trim(),
                    timestamp: cells.eq(4).text().trim()
                });
            });

            if (campaignData.length === 0) {
                alert('No data available to save.');
                return;
            }

            $.ajax({
                url: 'save_campaign.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ campaignName: campaignName.trim(), campaignData }),
                success: function (response) {
                    if (response.success) {
                        alert('Campaign saved successfully!');
                    } else {
                        alert('Failed to save campaign: ' + (response.message || 'Unknown error.'));
                    }
                },
                error: function () {
                    alert('An error occurred while saving the campaign.');
                }
            });
        });
    });
</script>
</body>
</html>
