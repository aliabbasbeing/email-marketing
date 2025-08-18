<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Marketing Tool - One-Click Installer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .step { display: none; }
        .step.active { display: block; }
        .progress-bar { transition: width 0.3s ease; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-blue-600 text-white p-6">
                <h1 class="text-3xl font-bold mb-2">Email Marketing Tool</h1>
                <p class="text-blue-100">One-Click Installation & Setup</p>
            </div>

            <!-- Progress Bar -->
            <div class="bg-gray-200 h-2">
                <div id="progressBar" class="progress-bar bg-blue-600 h-full" style="width: 0%"></div>
            </div>

            <div class="p-6">
                <!-- Step 1: Environment Detection -->
                <div id="step1" class="step active">
                    <h2 class="text-2xl font-bold mb-4">Environment Detection</h2>
                    <div id="environmentInfo" class="space-y-4">
                        <div class="animate-pulse">
                            <div class="h-4 bg-gray-300 rounded w-3/4 mb-2"></div>
                            <div class="h-4 bg-gray-300 rounded w-1/2"></div>
                        </div>
                    </div>
                    <div class="mt-6">
                        <button onclick="detectEnvironment()" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                            Detect Environment
                        </button>
                    </div>
                </div>

                <!-- Step 2: Requirements Check -->
                <div id="step2" class="step">
                    <h2 class="text-2xl font-bold mb-4">System Requirements</h2>
                    <div id="requirementsInfo" class="space-y-4"></div>
                    <div class="mt-6">
                        <button onclick="checkRequirements()" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                            Check Requirements
                        </button>
                    </div>
                </div>

                <!-- Step 3: Database Configuration -->
                <div id="step3" class="step">
                    <h2 class="text-2xl font-bold mb-4">Database Configuration</h2>
                    <div id="databaseRecommendation" class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded"></div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Database Type</label>
                            <select id="dbType" onchange="toggleDatabaseFields()" class="w-full p-2 border border-gray-300 rounded">
                                <option value="sqlite">SQLite (Recommended for development)</option>
                                <option value="mysql">MySQL (Production)</option>
                            </select>
                        </div>

                        <div id="mysqlFields" class="space-y-4" style="display: none;">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Host</label>
                                <input type="text" id="dbHost" value="localhost" class="w-full p-2 border border-gray-300 rounded">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Port</label>
                                <input type="text" id="dbPort" value="3306" class="w-full p-2 border border-gray-300 rounded">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Database Name</label>
                                <input type="text" id="dbName" value="bulk_mailer" class="w-full p-2 border border-gray-300 rounded">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                                <input type="text" id="dbUsername" value="root" class="w-full p-2 border border-gray-300 rounded">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                                <input type="password" id="dbPassword" class="w-full p-2 border border-gray-300 rounded">
                            </div>
                        </div>

                        <div class="mt-6">
                            <button onclick="testDatabase()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 mr-2">
                                Test Connection
                            </button>
                            <button onclick="configureDatabase()" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                                Configure Database
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Installation -->
                <div id="step4" class="step">
                    <h2 class="text-2xl font-bold mb-4">Installation</h2>
                    <div id="installationProgress" class="space-y-4"></div>
                    <div class="mt-6">
                        <button onclick="startInstallation()" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
                            Start Installation
                        </button>
                    </div>
                </div>

                <!-- Step 5: Complete -->
                <div id="step5" class="step">
                    <h2 class="text-2xl font-bold mb-4 text-green-600">Installation Complete!</h2>
                    <div class="space-y-4">
                        <div class="p-4 bg-green-50 border border-green-200 rounded">
                            <h3 class="font-semibold text-green-800">Your email marketing tool is ready!</h3>
                            <p class="text-green-700 mt-2">You can now access your application and start creating email campaigns.</p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-4 border border-gray-200 rounded">
                                <h4 class="font-semibold mb-2">Default Login</h4>
                                <p class="text-sm text-gray-600">Username: <strong>admin</strong></p>
                                <p class="text-sm text-gray-600">Password: <strong>admin123</strong></p>
                            </div>
                            
                            <div class="p-4 border border-gray-200 rounded">
                                <h4 class="font-semibold mb-2">Next Steps</h4>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li>• Configure SMTP settings</li>
                                    <li>• Import email contacts</li>
                                    <li>• Create email templates</li>
                                </ul>
                            </div>
                        </div>

                        <div class="mt-6">
                            <a href="public/" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 inline-block">
                                Access Application
                            </a>
                            <a href="config-wizard.php" class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700 inline-block ml-2">
                                Configuration Wizard
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="mt-8 pt-6 border-t border-gray-200 flex justify-between">
                    <button id="prevBtn" onclick="previousStep()" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700" style="display: none;">
                        Previous
                    </button>
                    <button id="nextBtn" onclick="nextStep()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700" style="display: none;">
                        Next
                    </button>
                </div>
            </div>
        </div>

        <!-- Environment-specific installation shortcuts -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-bold mb-4 text-orange-600">XAMPP Quick Setup</h3>
                <p class="text-gray-600 mb-4">Optimized installation for XAMPP development environment.</p>
                <a href="setup-xampp.php" class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700 inline-block">
                    XAMPP Setup
                </a>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-bold mb-4 text-green-600">cPanel Hosting Setup</h3>
                <p class="text-gray-600 mb-4">Optimized installation for cPanel shared hosting.</p>
                <a href="setup-cpanel.php" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 inline-block">
                    cPanel Setup
                </a>
            </div>
        </div>
    </div>

    <script>
        let currentStep = 1;
        let environmentData = {};
        let requirementsData = {};

        function updateProgress() {
            const progress = (currentStep - 1) * 25;
            document.getElementById('progressBar').style.width = progress + '%';
        }

        function showStep(step) {
            // Hide all steps
            document.querySelectorAll('.step').forEach(el => el.classList.remove('active'));
            
            // Show current step
            document.getElementById('step' + step).classList.add('active');
            
            // Update navigation
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            
            prevBtn.style.display = step > 1 ? 'block' : 'none';
            nextBtn.style.display = step < 5 && step !== 4 ? 'block' : 'none';
            
            updateProgress();
        }

        function nextStep() {
            if (currentStep < 5) {
                currentStep++;
                showStep(currentStep);
            }
        }

        function previousStep() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        }

        function detectEnvironment() {
            fetch('?action=detect_environment')
                .then(response => response.json())
                .then(data => {
                    environmentData = data;
                    displayEnvironmentInfo(data);
                    
                    // Auto-advance after detection
                    setTimeout(() => {
                        currentStep = 2;
                        showStep(currentStep);
                    }, 1500);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to detect environment. Please try again.');
                });
        }

        function displayEnvironmentInfo(data) {
            const html = `
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                        <span>Environment: <strong>${data.type.toUpperCase()}</strong></span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="w-3 h-3 ${data.is_localhost ? 'bg-green-500' : 'bg-red-500'} rounded-full"></span>
                        <span>Localhost: ${data.is_localhost ? 'Yes' : 'No'}</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                        <span>Web Server: <strong>${data.web_server}</strong></span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="w-3 h-3 bg-purple-500 rounded-full"></span>
                        <span>PHP Version: <strong>${data.php_version}</strong></span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="w-3 h-3 ${data.can_write_files ? 'bg-green-500' : 'bg-red-500'} rounded-full"></span>
                        <span>File Permissions: ${data.can_write_files ? 'OK' : 'Needs Attention'}</span>
                    </div>
                </div>
            `;
            document.getElementById('environmentInfo').innerHTML = html;
        }

        function checkRequirements() {
            fetch('?action=check_requirements')
                .then(response => response.json())
                .then(data => {
                    requirementsData = data;
                    displayRequirementsInfo(data);
                    
                    if (data.passed) {
                        setTimeout(() => {
                            currentStep = 3;
                            showStep(currentStep);
                            setupDatabaseRecommendation();
                        }, 1500);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to check requirements. Please try again.');
                });
        }

        function displayRequirementsInfo(data) {
            let html = '<div class="space-y-3">';
            
            for (const [key, req] of Object.entries(data.requirements)) {
                const statusIcon = req.status ? 
                    '<span class="w-4 h-4 bg-green-500 rounded-full inline-block"></span>' :
                    '<span class="w-4 h-4 bg-red-500 rounded-full inline-block"></span>';
                
                html += `
                    <div class="flex items-center justify-between p-3 border rounded">
                        <div class="flex items-center space-x-3">
                            ${statusIcon}
                            <span class="font-medium">${key.replace('_', ' ').toUpperCase()}</span>
                        </div>
                        <span class="text-sm text-gray-600">
                            ${req.current === true ? 'Available' : req.current === false ? 'Not Available' : req.current}
                        </span>
                    </div>
                `;
            }
            
            html += '</div>';
            
            if (data.passed) {
                html += '<div class="mt-4 p-4 bg-green-50 border border-green-200 rounded"><span class="text-green-800 font-semibold">✓ All requirements met!</span></div>';
            } else {
                html += '<div class="mt-4 p-4 bg-red-50 border border-red-200 rounded"><span class="text-red-800 font-semibold">⚠ Some requirements are not met. Please install missing extensions.</span></div>';
            }
            
            document.getElementById('requirementsInfo').innerHTML = html;
        }

        function setupDatabaseRecommendation() {
            let recommendation = '';
            
            if (environmentData.is_xampp) {
                recommendation = '<strong>Recommendation:</strong> SQLite is recommended for XAMPP development. It requires no additional setup.';
            } else if (environmentData.is_cpanel) {
                recommendation = '<strong>Recommendation:</strong> MySQL is recommended for cPanel hosting. Please enter your database credentials below.';
            } else {
                recommendation = '<strong>Recommendation:</strong> SQLite is recommended for development, MySQL for production.';
            }
            
            document.getElementById('databaseRecommendation').innerHTML = recommendation;
            
            // Auto-select recommended database type
            if (environmentData.is_xampp || environmentData.is_localhost) {
                document.getElementById('dbType').value = 'sqlite';
            } else {
                document.getElementById('dbType').value = 'mysql';
            }
            
            toggleDatabaseFields();
        }

        function toggleDatabaseFields() {
            const dbType = document.getElementById('dbType').value;
            const mysqlFields = document.getElementById('mysqlFields');
            
            if (dbType === 'mysql') {
                mysqlFields.style.display = 'block';
            } else {
                mysqlFields.style.display = 'none';
            }
        }

        function testDatabase() {
            const config = getDatabaseConfig();
            
            fetch('?action=test_database', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(config)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✓ Database connection successful!');
                } else {
                    alert('✗ Database connection failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to test database connection.');
            });
        }

        function configureDatabase() {
            const config = getDatabaseConfig();
            
            fetch('?action=configure_database', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(config)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentStep = 4;
                    showStep(currentStep);
                } else {
                    alert('Database configuration failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to configure database.');
            });
        }

        function getDatabaseConfig() {
            const dbType = document.getElementById('dbType').value;
            
            if (dbType === 'sqlite') {
                return {
                    DB_CONNECTION: 'sqlite',
                    DB_DATABASE: './storage/database.sqlite'
                };
            } else {
                return {
                    DB_CONNECTION: 'mysql',
                    DB_HOST: document.getElementById('dbHost').value,
                    DB_PORT: document.getElementById('dbPort').value,
                    DB_DATABASE: document.getElementById('dbName').value,
                    DB_USERNAME: document.getElementById('dbUsername').value,
                    DB_PASSWORD: document.getElementById('dbPassword').value
                };
            }
        }

        function startInstallation() {
            document.getElementById('installationProgress').innerHTML = '<div class="animate-pulse">Installing...</div>';
            
            fetch('?action=install')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        currentStep = 5;
                        showStep(currentStep);
                    } else {
                        document.getElementById('installationProgress').innerHTML = 
                            '<div class="p-4 bg-red-50 border border-red-200 rounded text-red-800">Installation failed: ' + data.message + '</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('installationProgress').innerHTML = 
                        '<div class="p-4 bg-red-50 border border-red-200 rounded text-red-800">Installation failed: ' + error.message + '</div>';
                });
        }

        // Initialize
        updateProgress();
    </script>
</body>
</html>

<?php
// PHP Backend for installer
require_once 'includes/installer-utils.php';

// Handle API requests first, before any HTML output
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'detect_environment':
            echo json_encode(InstallerUtils::detectEnvironment());
            break;
            
        case 'check_requirements':
            echo json_encode(InstallerUtils::checkRequirements());
            break;
            
        case 'test_database':
            $input = json_decode(file_get_contents('php://input'), true);
            echo json_encode(InstallerUtils::testDatabaseConnection($input));
            break;
            
        case 'configure_database':
            $input = json_decode(file_get_contents('php://input'), true);
            $environment = InstallerUtils::detectEnvironment();
            
            $mysql_config = null;
            if ($input['DB_CONNECTION'] === 'mysql') {
                $mysql_config = [
                    'host' => $input['DB_HOST'],
                    'port' => $input['DB_PORT'],
                    'database' => $input['DB_DATABASE'],
                    'username' => $input['DB_USERNAME'],
                    'password' => $input['DB_PASSWORD']
                ];
            }
            
            $config = InstallerUtils::createDatabaseConfig($environment, $mysql_config);
            $config['APP_DEBUG'] = $environment['type'] === 'localhost' || $environment['is_xampp'] ? 'true' : 'false';
            $config['APP_ENV'] = $environment['type'] === 'localhost' || $environment['is_xampp'] ? 'development' : 'production';
            
            $success = InstallerUtils::createEnvFile($config);
            echo json_encode(['success' => $success]);
            break;
            
        case 'install':
            try {
                // Create directories
                InstallerUtils::createDirectories();
                
                // Set permissions
                InstallerUtils::setPermissions();
                
                // Create .htaccess files
                InstallerUtils::createHtaccessFiles();
                
                // Run migrations
                $migration_result = InstallerUtils::runMigrations();
                
                if (!$migration_result['success']) {
                    throw new Exception($migration_result['message']);
                }
                
                echo json_encode(['success' => true, 'message' => 'Installation completed successfully']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;
            
        default:
            echo json_encode(['error' => 'Unknown action']);
    }
    exit;
}
?>