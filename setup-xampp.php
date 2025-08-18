<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XAMPP Quick Setup - Email Marketing Tool</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-3xl">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-orange-600 text-white p-6">
                <h1 class="text-3xl font-bold mb-2">XAMPP Quick Setup</h1>
                <p class="text-orange-100">Automated setup for XAMPP development environment</p>
            </div>

            <div class="p-6">
                <div id="setupContent">
                    <!-- XAMPP Detection -->
                    <div class="mb-6">
                        <h2 class="text-xl font-bold mb-4">XAMPP Environment Detection</h2>
                        <div id="xamppStatus" class="p-4 border rounded">
                            <div class="animate-pulse">Detecting XAMPP installation...</div>
                        </div>
                    </div>

                    <!-- Setup Options -->
                    <div id="setupOptions" class="mb-6" style="display: none;">
                        <h2 class="text-xl font-bold mb-4">Setup Configuration</h2>
                        <div class="space-y-4">
                            <div class="p-4 bg-blue-50 border border-blue-200 rounded">
                                <h3 class="font-semibold text-blue-800">Recommended for XAMPP:</h3>
                                <ul class="text-blue-700 mt-2 space-y-1">
                                    <li>• SQLite database (no MySQL setup required)</li>
                                    <li>• Development mode enabled</li>
                                    <li>• Debug mode enabled</li>
                                    <li>• Auto-configured file permissions</li>
                                </ul>
                            </div>

                            <div>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" id="useMySQL" class="rounded">
                                    <span>Use MySQL instead of SQLite (requires XAMPP MySQL to be running)</span>
                                </label>
                            </div>

                            <div id="mysqlConfig" class="p-4 bg-yellow-50 border border-yellow-200 rounded" style="display: none;">
                                <h4 class="font-semibold mb-2">MySQL Configuration</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Database Name</label>
                                        <input type="text" id="dbName" value="bulk_mailer" class="w-full p-2 border rounded">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Username</label>
                                        <input type="text" id="dbUser" value="root" class="w-full p-2 border rounded">
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <label class="block text-sm font-medium mb-1">Password</label>
                                    <input type="password" id="dbPass" placeholder="Leave empty for default XAMPP" class="w-full p-2 border rounded">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Installation Progress -->
                    <div id="installProgress" class="mb-6" style="display: none;">
                        <h2 class="text-xl font-bold mb-4">Installation Progress</h2>
                        <div id="progressSteps" class="space-y-2"></div>
                    </div>

                    <!-- Success -->
                    <div id="setupComplete" class="mb-6" style="display: none;">
                        <h2 class="text-xl font-bold mb-4 text-green-600">Setup Complete!</h2>
                        <div class="space-y-4">
                            <div class="p-4 bg-green-50 border border-green-200 rounded">
                                <h3 class="font-semibold text-green-800">Your email marketing tool is ready for development!</h3>
                                <p class="text-green-700 mt-2">Access your application at the link below.</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="p-4 border border-gray-200 rounded">
                                    <h4 class="font-semibold mb-2">Access Information</h4>
                                    <p class="text-sm text-gray-600">URL: <a href="public/" class="text-blue-600 underline">http://localhost/email-marketing/public/</a></p>
                                    <p class="text-sm text-gray-600">Username: <strong>admin</strong></p>
                                    <p class="text-sm text-gray-600">Password: <strong>admin123</strong></p>
                                </div>

                                <div class="p-4 border border-gray-200 rounded">
                                    <h4 class="font-semibold mb-2">XAMPP Tips</h4>
                                    <ul class="text-sm text-gray-600 space-y-1">
                                        <li>• Files are in htdocs/email-marketing/</li>
                                        <li>• Database stored in storage/database.sqlite</li>
                                        <li>• Logs available in storage/logs/</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="mt-6">
                                <a href="public/" class="bg-orange-600 text-white px-6 py-2 rounded hover:bg-orange-700 inline-block">
                                    Open Application
                                </a>
                                <a href="config-wizard.php" class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700 inline-block ml-2">
                                    Configuration Wizard
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div id="actionButtons" class="pt-6 border-t border-gray-200">
                        <button onclick="startSetup()" id="setupBtn" class="bg-orange-600 text-white px-6 py-2 rounded hover:bg-orange-700" style="display: none;">
                            Start XAMPP Setup
                        </button>
                        <a href="install.php" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                            Back to Main Installer
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- XAMPP Setup Guide -->
        <div class="mt-8 bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">XAMPP Setup Guide</h3>
            <div class="space-y-4">
                <div class="p-4 bg-gray-50 border border-gray-200 rounded">
                    <h4 class="font-semibold mb-2">Step 1: Install XAMPP</h4>
                    <p class="text-sm text-gray-600 mb-2">Download and install XAMPP from <a href="https://www.apachefriends.org/" target="_blank" class="text-blue-600 underline">https://www.apachefriends.org/</a></p>
                </div>

                <div class="p-4 bg-gray-50 border border-gray-200 rounded">
                    <h4 class="font-semibold mb-2">Step 2: Start Apache</h4>
                    <p class="text-sm text-gray-600 mb-2">Open XAMPP Control Panel and start Apache. MySQL is optional (SQLite is used by default).</p>
                </div>

                <div class="p-4 bg-gray-50 border border-gray-200 rounded">
                    <h4 class="font-semibold mb-2">Step 3: Extract Files</h4>
                    <p class="text-sm text-gray-600 mb-2">Extract the email marketing tool to: <code class="bg-gray-200 px-2 py-1 rounded">xampp/htdocs/email-marketing/</code></p>
                </div>

                <div class="p-4 bg-gray-50 border border-gray-200 rounded">
                    <h4 class="font-semibold mb-2">Step 4: Run Setup</h4>
                    <p class="text-sm text-gray-600 mb-2">Access <code class="bg-gray-200 px-2 py-1 rounded">http://localhost/email-marketing/setup-xampp.php</code> to complete the setup.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        let xamppDetected = false;

        // Auto-detect and initialize
        document.addEventListener('DOMContentLoaded', function() {
            detectXAMPP();
        });

        function detectXAMPP() {
            fetch('?action=detect_xampp')
                .then(response => response.json())
                .then(data => {
                    displayXAMPPStatus(data);
                    if (data.is_xampp) {
                        xamppDetected = true;
                        document.getElementById('setupOptions').style.display = 'block';
                        document.getElementById('setupBtn').style.display = 'inline-block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('xamppStatus').innerHTML = 
                        '<div class="text-red-600">Failed to detect environment. Please check manually.</div>';
                });
        }

        function displayXAMPPStatus(data) {
            let html = '<div class="space-y-2">';
            
            if (data.is_xampp) {
                html += '<div class="flex items-center space-x-2"><span class="w-3 h-3 bg-green-500 rounded-full"></span><span class="text-green-700 font-semibold">XAMPP detected!</span></div>';
                html += `<div class="text-sm text-gray-600">Document Root: ${data.document_root}</div>`;
                html += `<div class="text-sm text-gray-600">PHP Version: ${data.php_version}</div>`;
                html += `<div class="text-sm text-gray-600">Web Server: ${data.web_server}</div>`;
            } else {
                html += '<div class="flex items-center space-x-2"><span class="w-3 h-3 bg-yellow-500 rounded-full"></span><span class="text-yellow-700 font-semibold">XAMPP not detected</span></div>';
                html += '<div class="text-sm text-gray-600">This setup is optimized for XAMPP. You may want to use the main installer instead.</div>';
            }
            
            html += '</div>';
            document.getElementById('xamppStatus').innerHTML = html;
        }

        // Toggle MySQL configuration
        document.getElementById('useMySQL').addEventListener('change', function() {
            const mysqlConfig = document.getElementById('mysqlConfig');
            mysqlConfig.style.display = this.checked ? 'block' : 'none';
        });

        function startSetup() {
            if (!xamppDetected) {
                alert('XAMPP was not detected. Please ensure XAMPP is installed and running.');
                return;
            }

            const useMySQL = document.getElementById('useMySQL').checked;
            const config = {
                database_type: useMySQL ? 'mysql' : 'sqlite',
                mysql_config: useMySQL ? {
                    database: document.getElementById('dbName').value,
                    username: document.getElementById('dbUser').value,
                    password: document.getElementById('dbPass').value
                } : null
            };

            // Show progress
            document.getElementById('installProgress').style.display = 'block';
            document.getElementById('setupBtn').disabled = true;
            
            const progressSteps = document.getElementById('progressSteps');
            progressSteps.innerHTML = '<div class="animate-pulse">Starting XAMPP setup...</div>';

            fetch('?action=xampp_setup', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(config)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displaySetupProgress(data.steps);
                    setTimeout(() => {
                        document.getElementById('installProgress').style.display = 'none';
                        document.getElementById('setupComplete').style.display = 'block';
                    }, 2000);
                } else {
                    progressSteps.innerHTML = 
                        '<div class="p-3 bg-red-50 border border-red-200 rounded text-red-800">Setup failed: ' + data.message + '</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                progressSteps.innerHTML = 
                    '<div class="p-3 bg-red-50 border border-red-200 rounded text-red-800">Setup failed: ' + error.message + '</div>';
            });
        }

        function displaySetupProgress(steps) {
            let html = '';
            steps.forEach(step => {
                const icon = step.success ? '✓' : '✗';
                const color = step.success ? 'text-green-600' : 'text-red-600';
                html += `<div class="flex items-center space-x-2">
                    <span class="${color}">${icon}</span>
                    <span>${step.message}</span>
                </div>`;
            });
            document.getElementById('progressSteps').innerHTML = html;
        }
    </script>
</body>
</html>

<?php
require_once 'includes/installer-utils.php';

if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'detect_xampp':
            $environment = InstallerUtils::detectEnvironment();
            echo json_encode($environment);
            break;
            
        case 'xampp_setup':
            $input = json_decode(file_get_contents('php://input'), true);
            
            try {
                $steps = [];
                
                // Step 1: Create directories
                $steps[] = [
                    'message' => 'Creating storage directories...',
                    'success' => InstallerUtils::createDirectories()
                ];
                
                // Step 2: Set permissions
                $steps[] = [
                    'message' => 'Setting file permissions...',
                    'success' => InstallerUtils::setPermissions()
                ];
                
                // Step 3: Configure database
                $environment = InstallerUtils::detectEnvironment();
                $mysql_config = null;
                
                if ($input['database_type'] === 'mysql') {
                    $mysql_config = [
                        'host' => 'localhost',
                        'port' => '3306',
                        'database' => $input['mysql_config']['database'],
                        'username' => $input['mysql_config']['username'],
                        'password' => $input['mysql_config']['password']
                    ];
                    
                    // Test MySQL connection
                    $test_result = InstallerUtils::testDatabaseConnection([
                        'DB_CONNECTION' => 'mysql',
                        'DB_HOST' => 'localhost',
                        'DB_PORT' => '3306',
                        'DB_DATABASE' => $mysql_config['database'],
                        'DB_USERNAME' => $mysql_config['username'],
                        'DB_PASSWORD' => $mysql_config['password']
                    ]);
                    
                    $steps[] = [
                        'message' => 'Testing MySQL connection...',
                        'success' => $test_result['success']
                    ];
                    
                    if (!$test_result['success']) {
                        throw new Exception('MySQL connection failed: ' . $test_result['message']);
                    }
                }
                
                $config = InstallerUtils::createDatabaseConfig($environment, $mysql_config);
                $config['APP_DEBUG'] = 'true';
                $config['APP_ENV'] = 'development';
                
                $steps[] = [
                    'message' => 'Creating configuration file...',
                    'success' => InstallerUtils::createEnvFile($config)
                ];
                
                // Step 4: Create .htaccess files
                $steps[] = [
                    'message' => 'Creating .htaccess files...',
                    'success' => InstallerUtils::createHtaccessFiles()
                ];
                
                // Step 5: Run migrations
                $migration_result = InstallerUtils::runMigrations();
                $steps[] = [
                    'message' => 'Setting up database...',
                    'success' => $migration_result['success']
                ];
                
                if (!$migration_result['success']) {
                    throw new Exception('Migration failed: ' . $migration_result['message']);
                }
                
                echo json_encode([
                    'success' => true,
                    'steps' => $steps,
                    'message' => 'XAMPP setup completed successfully'
                ]);
                
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'steps' => $steps ?? []
                ]);
            }
            break;
            
        default:
            echo json_encode(['error' => 'Unknown action']);
    }
    exit;
}
?>