<?php
/**
 * Application Entry Point
 * This is the new entry point for the redesigned email marketing tool
 */

// Include the application bootstrap
require_once __DIR__ . '/../src/App.php';

// Start the application
$app = App::getInstance();
$app->run();