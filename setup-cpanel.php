<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>cPanel Hosting Setup - Email Marketing Tool</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-green-600 text-white p-6">
                <h1 class="text-3xl font-bold mb-2">cPanel Hosting Setup</h1>
                <p class="text-green-100">Optimized setup for cPanel shared hosting environments</p>
            </div>

            <div class="p-6">
                <!-- Environment Detection -->
                <div class="mb-6">
                    <h2 class="text-xl font-bold mb-4">Hosting Environment Detection</h2>
                    <div id="hostingStatus" class="p-4 border rounded">
                        <div class="animate-pulse">Detecting hosting environment...</div>
                    </div>
                </div>

                <!-- Database Setup -->
                <div id="databaseSetup" class="mb-6" style="display: none;">
                    <h2 class="text-xl font-bold mb-4">Database Configuration</h2>
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded mb-4">
                        <h3 class="font-semibold text-blue-800">cPanel Database Setup Instructions:</h3>
                        <ol class="text-blue-700 mt-2 space-y-1 list-decimal list-inside">
                            <li>Login to your cPanel account</li>
                            <li>Go to "MySQL Databases" section</li>
                            <li>Create a new database (e.g., "bulk_mailer")</li>
                            <li>Create a new MySQL user</li>
                            <li>Assign the user to the database with "All Privileges"</li>
                            <li>Enter the database details below</li>
                        </ol>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Database Host</label>
                                <input type="text" id="dbHost" value="localhost" class="w-full p-2 border border-gray-300 rounded">
                                <p class="text-xs text-gray-500 mt-1">Usually "localhost" for shared hosting</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Database Port</label>
                                <input type="text" id="dbPort" value="3306" class="w-full p-2 border border-gray-300 rounded">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Database Name</label>
                            <input type="text" id="dbName" placeholder="e.g., username_bulk_mailer" class="w-full p-2 border border-gray-300 rounded">
                            <p class="text-xs text-gray-500 mt-1">Often prefixed with your username (e.g., username_bulk_mailer)</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Database Username</label>
                                <input type="text" id="dbUsername" placeholder="e.g., username_dbuser" class="w-full p-2 border border-gray-300 rounded">
                                <p class="text-xs text-gray-500 mt-1">Often prefixed with your username</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Database Password</label>
                                <input type="password" id="dbPassword" class="w-full p-2 border border-gray-300 rounded">
                            </div>
                        </div>

                        <div class="mt-4">
                            <button onclick="testConnection()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mr-2">
                                Test Connection
                            </button>
                            <div id="connectionResult" class="inline-block ml-4"></div>
                        </div>
                    </div>
                </div>

                <!-- File Structure Setup -->
                <div id="fileStructure" class="mb-6" style="display: none;">
                    <h2 class="text-xl font-bold mb-4">File Structure Configuration</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Installation Location</label>
                            <select id="installLocation" onchange="updateInstallPath()" class="w-full p-2 border border-gray-300 rounded">
                                <option value="main_domain">Main Domain (public_html/)</option>
                                <option value="subdomain">Subdomain (subdomain.yourdomain.com)</option>
                                <option value="subfolder">Subfolder (yourdomain.com/email-marketing/)</option>
                            </select>
                        </div>

                        <div id="customPath" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Custom Path</label>
                            <input type="text" id="customPathInput" placeholder="e.g., email-marketing" class="w-full p-2 border border-gray-300 rounded">
                        </div>

                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded">
                            <h3 class="font-semibold text-yellow-800">Installation Path:</h3>
                            <p id="installPathDisplay" class="text-yellow-700 mt-1 font-mono"></p>
                        </div>
                    </div>
                </div>

                <!-- Email Configuration -->
                <div id="emailConfig" class="mb-6" style="display: none;">
                    <h2 class="text-xl font-bold mb-4">Email Configuration</h2>
                    <div class="p-4 bg-green-50 border border-green-200 rounded mb-4">
                        <h3 class="font-semibold text-green-800">SMTP Configuration:</h3>
                        <p class="text-green-700 mt-2">You can configure SMTP later through the application settings.</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" id="configureSmtpNow" onchange="toggleSmtpConfig()">
                                <span>Configure SMTP settings now (optional)</span>
                            </label>
                        </div>

                        <div id="smtpFields" class="space-y-4" style="display: none;">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Host</label>
                                    <input type="text" id="smtpHost" placeholder="e.g., mail.yourdomain.com" class="w-full p-2 border border-gray-300 rounded">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Port</label>
                                    <select id="smtpPort" class="w-full p-2 border border-gray-300 rounded">
                                        <option value="587">587 (TLS)</option>
                                        <option value="465">465 (SSL)</option>
                                        <option value="25">25 (Unsecured)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                    <input type="email" id="smtpEmail" placeholder="noreply@yourdomain.com" class="w-full p-2 border border-gray-300 rounded">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Password</label>
                                    <input type="password" id="smtpPassword" class="w-full p-2 border border-gray-300 rounded">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">From Name</label>
                                <input type="text" id="smtpFromName" placeholder="Your Company Name" class="w-full p-2 border border-gray-300 rounded">
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
                            <h3 class="font-semibold text-green-800">Your email marketing tool is ready for production!</h3>
                            <p class="text-green-700 mt-2">Your application has been successfully deployed to your hosting account.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-4 border border-gray-200 rounded">
                                <h4 class="font-semibold mb-2">Access Information</h4>
                                <p class="text-sm text-gray-600">URL: <span id="appUrl" class="text-blue-600 font-mono"></span></p>
                                <p class="text-sm text-gray-600">Username: <strong>admin</strong></p>
                                <p class="text-sm text-gray-600">Password: <strong>admin123</strong></p>
                            </div>

                            <div class="p-4 border border-gray-200 rounded">
                                <h4 class="font-semibold mb-2">Security Recommendations</h4>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li>• Change admin password immediately</li>
                                    <li>• Configure SSL certificate</li>
                                    <li>• Set up regular backups</li>
                                    <li>• Configure SMTP for email sending</li>
                                </ul>
                            </div>
                        </div>

                        <div class="mt-6">
                            <a id="openAppBtn" href="#" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 inline-block" target="_blank">
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
                    <button onclick="startSetup()" id="setupBtn" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700" style="display: none;">
                        Start cPanel Setup
                    </button>
                    <a href="install.php" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                        Back to Main Installer
                    </a>
                </div>
            </div>
        </div>

        <!-- cPanel Setup Guide -->
        <div class="mt-8 bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">cPanel Deployment Guide</h3>
            <div class="space-y-4">
                <div class="p-4 bg-gray-50 border border-gray-200 rounded">
                    <h4 class="font-semibold mb-2">Before You Start:</h4>
                    <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                        <li>Ensure you have cPanel access to your hosting account</li>
                        <li>PHP 7.4+ should be available on your hosting</li>
                        <li>You should have MySQL database access</li>
                        <li>File upload/extraction capabilities in File Manager</li>
                    </ul>
                </div>

                <div class="p-4 bg-gray-50 border border-gray-200 rounded">
                    <h4 class="font-semibold mb-2">File Upload Methods:</h4>
                    <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                        <li><strong>Method 1:</strong> Upload ZIP file via cPanel File Manager and extract</li>
                        <li><strong>Method 2:</strong> Upload files via FTP client (FileZilla, etc.)</li>
                        <li><strong>Method 3:</strong> Use Git if available on your hosting</li>
                    </ul>
                </div>

                <div class="p-4 bg-gray-50 border border-gray-200 rounded">
                    <h4 class="font-semibold mb-2">Post-Installation:</h4>
                    <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                        <li>Configure SSL certificate for HTTPS</li>
                        <li>Set up regular database backups</li>
                        <li>Configure email sending (SMTP)</li>
                        <li>Test all functionality</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        let environmentData = {};

        // Auto-detect and initialize
        document.addEventListener('DOMContentLoaded', function() {
            detectEnvironment();
        });

        function detectEnvironment() {
            fetch('?action=detect_cpanel')
                .then(response => response.json())
                .then(data => {
                    environmentData = data;
                    displayEnvironmentStatus(data);
                    
                    if (data.is_cpanel || !data.is_localhost) {
                        document.getElementById('databaseSetup').style.display = 'block';
                        document.getElementById('fileStructure').style.display = 'block';
                        document.getElementById('emailConfig').style.display = 'block';
                        document.getElementById('setupBtn').style.display = 'inline-block';
                        updateInstallPath();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('hostingStatus').innerHTML = 
                        '<div class="text-red-600">Failed to detect environment. Manual configuration required.</div>';
                });
        }

        function displayEnvironmentStatus(data) {
            let html = '<div class="space-y-2">';
            
            if (data.is_cpanel) {
                html += '<div class="flex items-center space-x-2"><span class="w-3 h-3 bg-green-500 rounded-full"></span><span class="text-green-700 font-semibold">cPanel hosting detected!</span></div>';
            } else if (!data.is_localhost) {
                html += '<div class="flex items-center space-x-2"><span class="w-3 h-3 bg-blue-500 rounded-full"></span><span class="text-blue-700 font-semibold">Production hosting environment detected</span></div>';
            } else {
                html += '<div class="flex items-center space-x-2"><span class="w-3 h-3 bg-yellow-500 rounded-full"></span><span class="text-yellow-700 font-semibold">Local environment detected</span></div>';
                html += '<div class="text-sm text-gray-600">This setup is optimized for production hosting. Consider using XAMPP setup for local development.</div>';
            }
            
            html += `<div class="text-sm text-gray-600">PHP Version: ${data.php_version}</div>`;
            html += `<div class="text-sm text-gray-600">Web Server: ${data.web_server}</div>`;
            html += `<div class="text-sm text-gray-600">MySQL Support: ${data.has_mysql ? 'Available' : 'Not Available'}</div>`;
            
            html += '</div>';
            document.getElementById('hostingStatus').innerHTML = html;
        }

        function testConnection() {
            const config = {
                host: document.getElementById('dbHost').value,
                port: document.getElementById('dbPort').value,
                database: document.getElementById('dbName').value,
                username: document.getElementById('dbUsername').value,
                password: document.getElementById('dbPassword').value
            };

            const resultDiv = document.getElementById('connectionResult');
            resultDiv.innerHTML = '<span class="text-blue-600">Testing...</span>';

            fetch('?action=test_connection', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(config)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = '<span class="text-green-600">✓ Connection successful!</span>';
                } else {
                    resultDiv.innerHTML = '<span class="text-red-600">✗ Connection failed: ' + data.message + '</span>';
                }
            })
            .catch(error => {
                resultDiv.innerHTML = '<span class="text-red-600">✗ Test failed</span>';
            });
        }

        function updateInstallPath() {
            const location = document.getElementById('installLocation').value;
            const customPath = document.getElementById('customPath');
            const pathDisplay = document.getElementById('installPathDisplay');
            
            if (location === 'subfolder') {
                customPath.style.display = 'block';
                pathDisplay.textContent = 'public_html/' + (document.getElementById('customPathInput').value || 'email-marketing') + '/';
            } else {
                customPath.style.display = 'none';
                if (location === 'main_domain') {
                    pathDisplay.textContent = 'public_html/';
                } else if (location === 'subdomain') {
                    pathDisplay.textContent = 'public_html/subdomain/';
                }
            }
        }

        function toggleSmtpConfig() {
            const smtpFields = document.getElementById('smtpFields');
            const checkbox = document.getElementById('configureSmtpNow');
            smtpFields.style.display = checkbox.checked ? 'block' : 'none';
        }

        function startSetup() {
            const config = {
                database: {
                    host: document.getElementById('dbHost').value,
                    port: document.getElementById('dbPort').value,
                    database: document.getElementById('dbName').value,
                    username: document.getElementById('dbUsername').value,
                    password: document.getElementById('dbPassword').value
                },
                installation: {
                    location: document.getElementById('installLocation').value,
                    custom_path: document.getElementById('customPathInput').value
                }
            };

            // Add SMTP config if enabled
            if (document.getElementById('configureSmtpNow').checked) {
                config.smtp = {
                    host: document.getElementById('smtpHost').value,
                    port: document.getElementById('smtpPort').value,
                    email: document.getElementById('smtpEmail').value,
                    password: document.getElementById('smtpPassword').value,
                    from_name: document.getElementById('smtpFromName').value
                };
            }

            // Show progress
            document.getElementById('installProgress').style.display = 'block';
            document.getElementById('setupBtn').disabled = true;
            
            const progressSteps = document.getElementById('progressSteps');
            progressSteps.innerHTML = '<div class="animate-pulse">Starting cPanel setup...</div>';

            fetch('?action=cpanel_setup', {
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
                        
                        // Update app URL
                        const appUrl = data.app_url || (window.location.origin + '/public/');
                        document.getElementById('appUrl').textContent = appUrl;
                        document.getElementById('openAppBtn').href = appUrl;
                    }, 2000);
                } else {
                    progressSteps.innerHTML = 
                        '<div class="p-3 bg-red-50 border border-red-200 rounded text-red-800">Setup failed: ' + data.message + '</div>';
                    document.getElementById('setupBtn').disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                progressSteps.innerHTML = 
                    '<div class="p-3 bg-red-50 border border-red-200 rounded text-red-800">Setup failed: ' + error.message + '</div>';
                document.getElementById('setupBtn').disabled = false;
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

        // Auto-update path when custom path changes
        document.getElementById('customPathInput').addEventListener('input', updateInstallPath);
    </script>
</body>
</html>

<?php
require_once 'includes/installer-utils.php';

if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'detect_cpanel':
            $environment = InstallerUtils::detectEnvironment();
            echo json_encode($environment);
            break;
            
        case 'test_connection':
            $input = json_decode(file_get_contents('php://input'), true);
            $config = [
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $input['host'],
                'DB_PORT' => $input['port'],
                'DB_DATABASE' => $input['database'],
                'DB_USERNAME' => $input['username'],
                'DB_PASSWORD' => $input['password']
            ];
            echo json_encode(InstallerUtils::testDatabaseConnection($config));
            break;
            
        case 'cpanel_setup':
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
                $mysql_config = [
                    'host' => $input['database']['host'],
                    'port' => $input['database']['port'],
                    'database' => $input['database']['database'],
                    'username' => $input['database']['username'],
                    'password' => $input['database']['password']
                ];
                
                $environment = InstallerUtils::detectEnvironment();
                $config = InstallerUtils::createDatabaseConfig($environment, $mysql_config);
                $config['APP_DEBUG'] = 'false';
                $config['APP_ENV'] = 'production';
                
                // Add SMTP configuration if provided
                if (isset($input['smtp'])) {
                    $config['SMTP_HOST'] = $input['smtp']['host'];
                    $config['SMTP_PORT'] = $input['smtp']['port'];
                    $config['SMTP_USERNAME'] = $input['smtp']['email'];
                    $config['SMTP_PASSWORD'] = $input['smtp']['password'];
                    $config['SMTP_FROM_EMAIL'] = $input['smtp']['email'];
                    $config['SMTP_FROM_NAME'] = $input['smtp']['from_name'];
                    $config['SMTP_ENCRYPTION'] = $input['smtp']['port'] == '587' ? 'tls' : 'ssl';
                }
                
                $steps[] = [
                    'message' => 'Creating configuration file...',
                    'success' => InstallerUtils::createEnvFile($config)
                ];
                
                // Step 4: Create .htaccess files optimized for shared hosting
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
                
                // Determine app URL based on installation location
                $app_url = $_SERVER['HTTP_HOST'];
                if ($input['installation']['location'] === 'subfolder') {
                    $path = $input['installation']['custom_path'] ?: 'email-marketing';
                    $app_url .= '/' . $path;
                }
                $app_url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $app_url . '/public/';
                
                echo json_encode([
                    'success' => true,
                    'steps' => $steps,
                    'app_url' => $app_url,
                    'message' => 'cPanel setup completed successfully'
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