<?php
include('includes/db_connect.php');

$campaignId = $_GET['campaign_id'] ?? null;

if ($campaignId) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status = 'Sent' THEN 1 ELSE 0 END) as sent FROM email_logs WHERE campaign_id = ?");
    $stmt->execute([$campaignId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $emailsSent = $result['sent'];
    $totalEmails = $result['total'];
    $emailsRemaining = $totalEmails - $emailsSent;

    echo json_encode([
        'emailsSent' => $emailsSent,
        'emailsRemaining' => $emailsRemaining,
        'complete' => $emailsRemaining === 0
    ]);
} else {
    echo json_encode([
        'emailsSent' => 0,
        'emailsRemaining' => 0,
        'complete' => true
    ]);
}
?>
