<?php
session_start();
session_unset();
session_destroy();

$base_url = '';
if ($_SERVER['SERVER_NAME'] == 'localhost') {
    // Local server
    $base_url = 'http://localhost/e-marketing/';
} else {
    // Online (production) server
    $base_url = 'https://alijoy.site/';
}

// Correct usage of variable in header function
header("Location: $base_url");
exit;
?>
