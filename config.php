<?php
// Define the base URL based on the environment (local or online server)
$base_url = '';

if ($_SERVER['SERVER_NAME'] == 'localhost') {
    // Local server
    $base_url = 'http://localhost/e-marketing';
} else {
    // Online (production) server
    $base_url = 'https://alijoy.site/';

    // Online (production) server
    $base_url = 'https://aliabbas.pk/';
}
?>
