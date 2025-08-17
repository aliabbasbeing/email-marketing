<?php
session_start();
$progressFile = 'tmp/progress_' . session_id() . '.json';

if (file_exists($progressFile)) {
    echo file_get_contents($progressFile);
} else {
    echo json_encode(['error' => 'No progress available']);
}
?>
