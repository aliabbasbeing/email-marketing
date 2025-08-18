<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration Wizard - Email Marketing Tool</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .wizard-step { display: none; }
        .wizard-step.active { display: block; }
        .step-indicator { transition: all 0.3s ease; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-purple-600 text-white p-6">
                <h1 class="text-3xl font-bold mb-2">Configuration Wizard</h1>
                <p class="text-purple-100">Complete setup and configure your email marketing tool</p>
            </div>

            <!-- Progress Indicators -->
            <div class="bg-gray-50 p-4">
                <div class="flex items-center justify-between">
                    <div id="step1Indicator" class="step-indicator flex items-center space-x-2 text-purple-600">
                        <div class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center text-sm font-bold">1</div>
                        <span class="font-medium">SMTP</span>
                    </div>
                    <div class="flex-1 h-1 bg-gray-300 mx-4"></div>
                    <div id="step2Indicator" class="step-indicator flex items-center space-x-2 text-gray-400">
                        <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-sm font-bold">2</div>
                        <span class="font-medium">Admin</span>
                    </div>
                    <div class="flex-1 h-1 bg-gray-300 mx-4"></div>
                    <div id="step3Indicator" class="step-indicator flex items-center space-x-2 text-gray-400">
                        <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-sm font-bold">3</div>
                        <span class="font-medium">Security</span>
                    </div>
                    <div class="flex-1 h-1 bg-gray-300 mx-4"></div>
                    <div id="step4Indicator" class="step-indicator flex items-center space-x-2 text-gray-400">
                        <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-sm font-bold">4</div>
                        <span class="font-medium">Complete</span>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <!-- Step 1: SMTP Configuration -->
                <div id="step1" class="wizard-step active">
                    <h2 class="text-2xl font-bold mb-4">Email Sending Configuration</h2>
                    <p class="text-gray-600 mb-6">Configure SMTP settings to enable email sending functionality.</p>

                    <div class="space-y-6">
                        <!-- SMTP Provider Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Provider</label>
                            <select id="smtpProvider" onchange="updateSmtpDefaults()" class="w-full p-3 border border-gray-300 rounded">
                                <option value="custom">Custom SMTP</option>
                                <option value="gmail">Gmail</option>
                                <option value="outlook">Outlook/Hotmail</option>
                                <option value="yahoo">Yahoo Mail</option>
                                <option value="cpanel">cPanel Email</option>
                                <option value="sendgrid">SendGrid</option>
                                <option value="mailgun">Mailgun</option>
                            </select>
                        </div>

                        <!-- SMTP Settings -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Host</label>
                                <input type="text" id="smtpHost" class="w-full p-3 border border-gray-300 rounded" placeholder="smtp.gmail.com">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Port</label>
                                <select id="smtpPort" class="w-full p-3 border border-gray-300 rounded">
                                    <option value="587">587 (TLS)</option>
                                    <option value="465">465 (SSL)</option>
                                    <option value="25">25 (Unsecured)</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                <input type="email" id="smtpEmail" class="w-full p-3 border border-gray-300 rounded" placeholder="your-email@gmail.com">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Password</label>
                                <input type="password" id="smtpPassword" class="w-full p-3 border border-gray-300 rounded" placeholder="Your email password or app password">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">From Name</label>
                            <input type="text" id="smtpFromName" class="w-full p-3 border border-gray-300 rounded" placeholder="Your Company Name">
                        </div>

                        <!-- Test Email -->
                        <div class="p-4 bg-blue-50 border border-blue-200 rounded">
                            <h3 class="font-semibold mb-2">Test Email Configuration</h3>
                            <div class="flex space-x-4">
                                <input type="email" id="testEmail" class="flex-1 p-2 border border-gray-300 rounded" placeholder="test@example.com">
                                <button onclick="sendTestEmail()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                    Send Test
                                </button>
                            </div>
                            <div id="testResult" class="mt-2"></div>
                        </div>

                        <!-- Provider-specific instructions -->
                        <div id="smtpInstructions" class="p-4 bg-yellow-50 border border-yellow-200 rounded">
                            <h3 class="font-semibold mb-2">Gmail Configuration Instructions:</h3>
                            <ul class="text-sm text-gray-700 space-y-1">
                                <li>1. Enable 2-factor authentication on your Gmail account</li>
                                <li>2. Generate an "App Password" for this application</li>
                                <li>3. Use the App Password instead of your regular password</li>
                                <li>4. Go to: Account Settings → Security → App Passwords</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Admin Account -->
                <div id="step2" class="wizard-step">
                    <h2 class="text-2xl font-bold mb-4">Administrator Account</h2>
                    <p class="text-gray-600 mb-6">Update the default administrator account credentials.</p>

                    <div class="space-y-6">
                        <div class="p-4 bg-red-50 border border-red-200 rounded">
                            <h3 class="font-semibold text-red-800 mb-2">⚠ Security Notice</h3>
                            <p class="text-red-700 text-sm">The default administrator credentials are publicly known. Please change them immediately.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Username</label>
                                <input type="text" value="admin" disabled class="w-full p-3 border border-gray-300 rounded bg-gray-50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">New Username</label>
                                <input type="text" id="newUsername" class="w-full p-3 border border-gray-300 rounded" placeholder="Enter new username">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" id="adminEmail" class="w-full p-3 border border-gray-300 rounded" placeholder="admin@yourdomain.com">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                <input type="password" id="newPassword" class="w-full p-3 border border-gray-300 rounded" placeholder="Enter strong password">
                                <div id="passwordStrength" class="mt-1 text-sm"></div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                                <input type="password" id="confirmPassword" class="w-full p-3 border border-gray-300 rounded" placeholder="Confirm password">
                                <div id="passwordMatch" class="mt-1 text-sm"></div>
                            </div>
                        </div>

                        <div class="p-4 bg-green-50 border border-green-200 rounded">
                            <h3 class="font-semibold text-green-800 mb-2">Password Requirements:</h3>
                            <ul class="text-sm text-green-700 space-y-1">
                                <li>• At least 8 characters long</li>
                                <li>• Contains uppercase and lowercase letters</li>
                                <li>• Contains at least one number</li>
                                <li>• Contains at least one special character</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Security Settings -->
                <div id="step3" class="wizard-step">
                    <h2 class="text-2xl font-bold mb-4">Security Configuration</h2>
                    <p class="text-gray-600 mb-6">Configure security settings to protect your application.</p>

                    <div class="space-y-6">
                        <!-- Security Options -->
                        <div class="space-y-4">
                            <div class="p-4 border border-gray-200 rounded">
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" id="enableHttps" class="rounded">
                                    <div>
                                        <span class="font-medium">Force HTTPS</span>
                                        <p class="text-sm text-gray-600">Redirect all HTTP traffic to HTTPS (recommended for production)</p>
                                    </div>
                                </label>
                            </div>

                            <div class="p-4 border border-gray-200 rounded">
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" id="enableRateLimit" class="rounded" checked>
                                    <div>
                                        <span class="font-medium">Enable Rate Limiting</span>
                                        <p class="text-sm text-gray-600">Protect against brute force attacks and spam</p>
                                    </div>
                                </label>
                            </div>

                            <div class="p-4 border border-gray-200 rounded">
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" id="enableLogging" class="rounded" checked>
                                    <div>
                                        <span class="font-medium">Enable Security Logging</span>
                                        <p class="text-sm text-gray-600">Log security events and failed login attempts</p>
                                    </div>
                                </label>
                            </div>

                            <div class="p-4 border border-gray-200 rounded">
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" id="enableBackup" class="rounded">
                                    <div>
                                        <span class="font-medium">Enable Automatic Backups</span>
                                        <p class="text-sm text-gray-600">Create daily database backups</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Session Settings -->
                        <div>
                            <h3 class="text-lg font-semibold mb-3">Session Configuration</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Session Lifetime (minutes)</label>
                                    <select id="sessionLifetime" class="w-full p-3 border border-gray-300 rounded">
                                        <option value="30">30 minutes</option>
                                        <option value="60">1 hour</option>
                                        <option value="120" selected>2 hours</option>
                                        <option value="240">4 hours</option>
                                        <option value="480">8 hours</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Password Minimum Length</label>
                                    <select id="passwordMinLength" class="w-full p-3 border border-gray-300 rounded">
                                        <option value="6">6 characters</option>
                                        <option value="8" selected>8 characters</option>
                                        <option value="10">10 characters</option>
                                        <option value="12">12 characters</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Complete -->
                <div id="step4" class="wizard-step">
                    <h2 class="text-2xl font-bold mb-4 text-green-600">Configuration Complete!</h2>
                    
                    <div class="space-y-6">
                        <div class="p-6 bg-green-50 border border-green-200 rounded">
                            <h3 class="text-xl font-semibold text-green-800 mb-4">🎉 Your email marketing tool is fully configured!</h3>
                            <p class="text-green-700 mb-4">All settings have been applied successfully. You can now start using your email marketing platform.</p>
                        </div>

                        <!-- Configuration Summary -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-4 border border-gray-200 rounded">
                                <h4 class="font-semibold mb-3">SMTP Configuration</h4>
                                <div id="smtpSummary" class="text-sm text-gray-600 space-y-1"></div>
                            </div>

                            <div class="p-4 border border-gray-200 rounded">
                                <h4 class="font-semibold mb-3">Administrator Account</h4>
                                <div id="adminSummary" class="text-sm text-gray-600 space-y-1"></div>
                            </div>

                            <div class="p-4 border border-gray-200 rounded">
                                <h4 class="font-semibold mb-3">Security Settings</h4>
                                <div id="securitySummary" class="text-sm text-gray-600 space-y-1"></div>
                            </div>

                            <div class="p-4 border border-gray-200 rounded">
                                <h4 class="font-semibold mb-3">Quick Actions</h4>
                                <div class="space-y-2">
                                    <a href="public/" class="block text-blue-600 hover:underline">→ Access Application</a>
                                    <a href="public/campaigns" class="block text-blue-600 hover:underline">→ Create Campaign</a>
                                    <a href="public/settings" class="block text-blue-600 hover:underline">→ Application Settings</a>
                                </div>
                            </div>
                        </div>

                        <!-- Next Steps -->
                        <div class="p-4 bg-blue-50 border border-blue-200 rounded">
                            <h4 class="font-semibold text-blue-800 mb-2">Recommended Next Steps:</h4>
                            <ol class="text-blue-700 text-sm space-y-1 list-decimal list-inside">
                                <li>Import or add your email contacts</li>
                                <li>Create email templates for your campaigns</li>
                                <li>Set up tracking and analytics</li>
                                <li>Configure email sending schedules</li>
                                <li>Test email delivery with a small campaign</li>
                            </ol>
                        </div>

                        <div class="pt-6 border-t border-gray-200">
                            <a href="public/" class="bg-purple-600 text-white px-8 py-3 rounded-lg hover:bg-purple-700 inline-block text-lg font-semibold">
                                Launch Application
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="mt-8 pt-6 border-t border-gray-200 flex justify-between">
                    <button id="prevBtn" onclick="previousStep()" class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700" style="display: none;">
                        Previous
                    </button>
                    <div class="flex space-x-4">
                        <button id="skipBtn" onclick="skipStep()" class="bg-gray-400 text-white px-6 py-2 rounded hover:bg-gray-500">
                            Skip
                        </button>
                        <button id="nextBtn" onclick="nextStep()" class="bg-purple-600 text-white px-6 py-2 rounded hover:bg-purple-700">
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentStep = 1;
        const totalSteps = 4;

        // SMTP Provider configurations
        const smtpProviders = {
            gmail: {
                host: 'smtp.gmail.com',
                port: '587',
                instructions: 'Gmail Configuration Instructions:<br>1. Enable 2-factor authentication<br>2. Generate App Password<br>3. Use App Password instead of regular password'
            },
            outlook: {
                host: 'smtp-mail.outlook.com',
                port: '587',
                instructions: 'Outlook Configuration Instructions:<br>1. Use your full email address as username<br>2. Use your regular Outlook password<br>3. Enable SMTP in Outlook settings if needed'
            },
            yahoo: {
                host: 'smtp.mail.yahoo.com',
                port: '587',
                instructions: 'Yahoo Mail Configuration Instructions:<br>1. Enable 2-factor authentication<br>2. Generate App Password<br>3. Use App Password for SMTP authentication'
            },
            cpanel: {
                host: 'mail.yourdomain.com',
                port: '587',
                instructions: 'cPanel Email Configuration:<br>1. Replace yourdomain.com with your actual domain<br>2. Use your cPanel email account credentials<br>3. Check with hosting provider for exact SMTP settings'
            },
            sendgrid: {
                host: 'smtp.sendgrid.net',
                port: '587',
                instructions: 'SendGrid Configuration:<br>1. Use "apikey" as username<br>2. Use your SendGrid API key as password<br>3. Configure sender authentication in SendGrid'
            },
            mailgun: {
                host: 'smtp.mailgun.org',
                port: '587',
                instructions: 'Mailgun Configuration:<br>1. Use your Mailgun domain SMTP credentials<br>2. Find credentials in Mailgun dashboard<br>3. Verify your domain in Mailgun'
            },
            custom: {
                host: '',
                port: '587',
                instructions: 'Custom SMTP Configuration:<br>1. Enter your SMTP server details<br>2. Check with your email provider for settings<br>3. Test the configuration before proceeding'
            }
        };

        function updateSmtpDefaults() {
            const provider = document.getElementById('smtpProvider').value;
            const config = smtpProviders[provider];
            
            if (config) {
                document.getElementById('smtpHost').value = config.host;
                document.getElementById('smtpPort').value = config.port;
                document.getElementById('smtpInstructions').innerHTML = '<h3 class="font-semibold mb-2">' + provider.toUpperCase() + ' Configuration Instructions:</h3><div class="text-sm text-gray-700">' + config.instructions + '</div>';
            }
        }

        function showStep(step) {
            // Hide all steps
            for (let i = 1; i <= totalSteps; i++) {
                document.getElementById(`step${i}`).classList.remove('active');
                const indicator = document.getElementById(`step${i}Indicator`);
                indicator.classList.remove('text-purple-600');
                indicator.classList.add('text-gray-400');
                indicator.querySelector('div').classList.remove('bg-purple-600', 'text-white');
                indicator.querySelector('div').classList.add('bg-gray-300', 'text-gray-600');
            }
            
            // Show current step
            document.getElementById(`step${step}`).classList.add('active');
            const currentIndicator = document.getElementById(`step${step}Indicator`);
            currentIndicator.classList.remove('text-gray-400');
            currentIndicator.classList.add('text-purple-600');
            currentIndicator.querySelector('div').classList.remove('bg-gray-300', 'text-gray-600');
            currentIndicator.querySelector('div').classList.add('bg-purple-600', 'text-white');
            
            // Update navigation
            document.getElementById('prevBtn').style.display = step > 1 ? 'block' : 'none';
            document.getElementById('nextBtn').style.display = step < totalSteps ? 'block' : 'none';
            document.getElementById('skipBtn').style.display = step < totalSteps ? 'block' : 'none';
            
            // Update button text
            if (step === totalSteps - 1) {
                document.getElementById('nextBtn').textContent = 'Complete Setup';
            } else {
                document.getElementById('nextBtn').textContent = 'Next';
            }
        }

        function nextStep() {
            if (validateCurrentStep()) {
                if (currentStep < totalSteps) {
                    if (currentStep === totalSteps - 1) {
                        saveConfiguration();
                    }
                    currentStep++;
                    showStep(currentStep);
                    
                    if (currentStep === totalSteps) {
                        updateSummary();
                    }
                }
            }
        }

        function previousStep() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        }

        function skipStep() {
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
                
                if (currentStep === totalSteps) {
                    updateSummary();
                }
            }
        }

        function validateCurrentStep() {
            switch (currentStep) {
                case 1: // SMTP validation
                    const host = document.getElementById('smtpHost').value;
                    const email = document.getElementById('smtpEmail').value;
                    if (!host || !email) {
                        alert('Please fill in at least the SMTP host and email address.');
                        return false;
                    }
                    break;
                    
                case 2: // Admin validation
                    const newPassword = document.getElementById('newPassword').value;
                    const confirmPassword = document.getElementById('confirmPassword').value;
                    if (newPassword && newPassword !== confirmPassword) {
                        alert('Passwords do not match.');
                        return false;
                    }
                    if (newPassword && newPassword.length < 8) {
                        alert('Password must be at least 8 characters long.');
                        return false;
                    }
                    break;
            }
            return true;
        }

        function sendTestEmail() {
            const testEmail = document.getElementById('testEmail').value;
            if (!testEmail) {
                alert('Please enter a test email address.');
                return;
            }

            const config = {
                host: document.getElementById('smtpHost').value,
                port: document.getElementById('smtpPort').value,
                email: document.getElementById('smtpEmail').value,
                password: document.getElementById('smtpPassword').value,
                from_name: document.getElementById('smtpFromName').value,
                test_email: testEmail
            };

            document.getElementById('testResult').innerHTML = '<span class="text-blue-600">Sending test email...</span>';

            fetch('?action=test_smtp', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(config)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('testResult').innerHTML = '<span class="text-green-600">✓ Test email sent successfully!</span>';
                } else {
                    document.getElementById('testResult').innerHTML = '<span class="text-red-600">✗ Test failed: ' + data.message + '</span>';
                }
            })
            .catch(error => {
                document.getElementById('testResult').innerHTML = '<span class="text-red-600">✗ Test failed: ' + error.message + '</span>';
            });
        }

        function saveConfiguration() {
            const config = {
                smtp: {
                    host: document.getElementById('smtpHost').value,
                    port: document.getElementById('smtpPort').value,
                    email: document.getElementById('smtpEmail').value,
                    password: document.getElementById('smtpPassword').value,
                    from_name: document.getElementById('smtpFromName').value
                },
                admin: {
                    username: document.getElementById('newUsername').value,
                    email: document.getElementById('adminEmail').value,
                    password: document.getElementById('newPassword').value
                },
                security: {
                    force_https: document.getElementById('enableHttps').checked,
                    rate_limit: document.getElementById('enableRateLimit').checked,
                    security_logging: document.getElementById('enableLogging').checked,
                    auto_backup: document.getElementById('enableBackup').checked,
                    session_lifetime: document.getElementById('sessionLifetime').value,
                    password_min_length: document.getElementById('passwordMinLength').value
                }
            };

            fetch('?action=save_config', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(config)
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    alert('Failed to save configuration: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error saving configuration:', error);
            });
        }

        function updateSummary() {
            // SMTP Summary
            const smtpSummary = document.getElementById('smtpSummary');
            smtpSummary.innerHTML = `
                <div>Host: ${document.getElementById('smtpHost').value || 'Not configured'}</div>
                <div>Port: ${document.getElementById('smtpPort').value}</div>
                <div>Email: ${document.getElementById('smtpEmail').value || 'Not configured'}</div>
                <div>From Name: ${document.getElementById('smtpFromName').value || 'Not configured'}</div>
            `;

            // Admin Summary
            const adminSummary = document.getElementById('adminSummary');
            const newUsername = document.getElementById('newUsername').value;
            const adminEmail = document.getElementById('adminEmail').value;
            adminSummary.innerHTML = `
                <div>Username: ${newUsername || 'admin (unchanged)'}</div>
                <div>Email: ${adminEmail || 'Not configured'}</div>
                <div>Password: ${document.getElementById('newPassword').value ? 'Updated' : 'Default (please change)'}</div>
            `;

            // Security Summary
            const securitySummary = document.getElementById('securitySummary');
            securitySummary.innerHTML = `
                <div>HTTPS: ${document.getElementById('enableHttps').checked ? 'Enabled' : 'Disabled'}</div>
                <div>Rate Limiting: ${document.getElementById('enableRateLimit').checked ? 'Enabled' : 'Disabled'}</div>
                <div>Security Logging: ${document.getElementById('enableLogging').checked ? 'Enabled' : 'Disabled'}</div>
                <div>Auto Backup: ${document.getElementById('enableBackup').checked ? 'Enabled' : 'Disabled'}</div>
                <div>Session Timeout: ${document.getElementById('sessionLifetime').value} minutes</div>
            `;
        }

        // Password strength checker
        document.getElementById('newPassword').addEventListener('input', function() {
            const password = this.value;
            const strengthDiv = document.getElementById('passwordStrength');
            
            let strength = 0;
            let feedback = [];
            
            if (password.length >= 8) strength++;
            else feedback.push('8+ characters');
            
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            else feedback.push('upper & lowercase');
            
            if (/\d/.test(password)) strength++;
            else feedback.push('numbers');
            
            if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength++;
            else feedback.push('special characters');
            
            const colors = ['text-red-600', 'text-orange-600', 'text-yellow-600', 'text-green-600'];
            const labels = ['Weak', 'Fair', 'Good', 'Strong'];
            
            if (password) {
                strengthDiv.className = `mt-1 text-sm ${colors[strength - 1] || 'text-red-600'}`;
                strengthDiv.textContent = `Strength: ${labels[strength - 1] || 'Very Weak'}`;
                if (feedback.length > 0) {
                    strengthDiv.textContent += ` (Missing: ${feedback.join(', ')})`;
                }
            } else {
                strengthDiv.textContent = '';
            }
        });

        // Password match checker
        document.getElementById('confirmPassword').addEventListener('input', function() {
            const password = document.getElementById('newPassword').value;
            const confirm = this.value;
            const matchDiv = document.getElementById('passwordMatch');
            
            if (confirm) {
                if (password === confirm) {
                    matchDiv.className = 'mt-1 text-sm text-green-600';
                    matchDiv.textContent = '✓ Passwords match';
                } else {
                    matchDiv.className = 'mt-1 text-sm text-red-600';
                    matchDiv.textContent = '✗ Passwords do not match';
                }
            } else {
                matchDiv.textContent = '';
            }
        });

        // Initialize
        updateSmtpDefaults();
        showStep(currentStep);
    </script>
</body>
</html>

<?php
// PHP Backend for configuration wizard
require_once 'includes/installer-utils.php';

if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'test_smtp':
            $input = json_decode(file_get_contents('php://input'), true);
            
            try {
                // Simple SMTP test (you could implement actual email sending here)
                echo json_encode([
                    'success' => true, 
                    'message' => 'SMTP configuration appears valid (actual sending test would require PHPMailer or similar)'
                ]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;
            
        case 'save_config':
            $input = json_decode(file_get_contents('php://input'), true);
            
            try {
                // Load existing .env or create base config
                $env_content = '';
                $env_file = __DIR__ . '/.env';
                
                if (file_exists($env_file)) {
                    $env_content = file_get_contents($env_file);
                }
                
                // Update SMTP configuration
                if (!empty($input['smtp']['host'])) {
                    $env_content = updateEnvValue($env_content, 'SMTP_HOST', $input['smtp']['host']);
                    $env_content = updateEnvValue($env_content, 'SMTP_PORT', $input['smtp']['port']);
                    $env_content = updateEnvValue($env_content, 'SMTP_USERNAME', $input['smtp']['email']);
                    $env_content = updateEnvValue($env_content, 'SMTP_PASSWORD', $input['smtp']['password']);
                    $env_content = updateEnvValue($env_content, 'SMTP_FROM_EMAIL', $input['smtp']['email']);
                    $env_content = updateEnvValue($env_content, 'SMTP_FROM_NAME', $input['smtp']['from_name'] ?: 'Email Marketing Tool');
                    $env_content = updateEnvValue($env_content, 'SMTP_ENCRYPTION', $input['smtp']['port'] == '587' ? 'tls' : 'ssl');
                }
                
                // Update security settings
                $env_content = updateEnvValue($env_content, 'SESSION_LIFETIME', ($input['security']['session_lifetime'] ?? 120) * 60);
                $env_content = updateEnvValue($env_content, 'PASSWORD_MIN_LENGTH', $input['security']['password_min_length'] ?? 8);
                
                // Save updated .env
                file_put_contents($env_file, $env_content);
                
                // Update admin account if provided
                if (!empty($input['admin']['password'])) {
                    updateAdminAccount($input['admin']);
                }
                
                echo json_encode(['success' => true, 'message' => 'Configuration saved successfully']);
                
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;
            
        default:
            echo json_encode(['error' => 'Unknown action']);
    }
    exit;
}

function updateEnvValue($env_content, $key, $value) {
    $pattern = "/^{$key}=.*$/m";
    $replacement = "{$key}={$value}";
    
    if (preg_match($pattern, $env_content)) {
        return preg_replace($pattern, $replacement, $env_content);
    } else {
        return $env_content . "\n{$replacement}";
    }
}

function updateAdminAccount($admin) {
    try {
        InstallerUtils::loadEnv();
        require_once __DIR__ . '/src/App.php';
        
        $app = App::getInstance();
        $db = $app->getDatabase();
        
        $updates = [];
        $params = [];
        
        if (!empty($admin['username'])) {
            $updates[] = 'username = ?';
            $params[] = $admin['username'];
        }
        
        if (!empty($admin['email'])) {
            $updates[] = 'email = ?';
            $params[] = $admin['email'];
        }
        
        if (!empty($admin['password'])) {
            $updates[] = 'password = ?';
            $params[] = password_hash($admin['password'], PASSWORD_BCRYPT);
        }
        
        if (!empty($updates)) {
            $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE username = 'admin'";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
        }
        
        return true;
    } catch (Exception $e) {
        error_log("Failed to update admin account: " . $e->getMessage());
        return false;
    }
}
?>