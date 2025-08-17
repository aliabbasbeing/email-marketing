<?php
include('includes/db_connect.php'); // Include database connection

header('Content-Type: application/json');

// Read the POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (empty($data['campaignName']) || empty($data['campaignData'])) {
    echo json_encode(['success' => false, 'message' => 'Campaign name and data are required.']);
    exit;
}

$campaignName = trim($data['campaignName']);
$campaignData = $data['campaignData'];

try {
    // Insert campaign into the database
    $pdo->beginTransaction();

    // Save campaign meta (name and timestamp)
    $stmt = $pdo->prepare("INSERT INTO campaigns (name, created_at) VALUES (:name, NOW())");
    $stmt->execute([':name' => $campaignName]);
    $campaignId = $pdo->lastInsertId();

    // Save campaign details
    $stmt = $pdo->prepare("INSERT INTO campaign_details (campaign_id, email, name, status, timestamp) VALUES (:campaign_id, :email, :name, :status, :timestamp)");

    foreach ($campaignData as $entry) {
        $stmt->execute([
            ':campaign_id' => $campaignId,
            ':email' => $entry['email'],
            ':name' => $entry['name'],
            ':status' => $entry['status'],
            ':timestamp' => $entry['timestamp']
        ]);
    }

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
