<?php
session_start();

// Check authentication
$isAuthenticated = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Marketing</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-900 text-gray-100 font-sans p-6 relative">
<?php if (!$isAuthenticated): ?>
    <!-- Modal for Login Required -->
    <div id="loginOverlay" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-gray-800 text-gray-100 p-6 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-xl font-semibold mb-4">Authentication Required</h2>
            <p class="mb-4">You must log in to access this page.</p>
            <a href="login.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
        </div>
    </div>
    <!-- Disable Page Interaction -->
    <div class="absolute inset-0 z-40"></div>
<?php endif; ?>

<?php
include('header.php');
?>

    <form id="csvMailerForm" action="csv_mailer.php" method="POST" enctype="multipart/form-data" class="bg-gray-800 p-6 rounded-lg shadow-lg">
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Upload CSV File:</label>
            <input type="file" name="csv_file" accept=".csv" id="csvFileInput" required class="block w-full text-gray-300 bg-gray-700 border border-gray-600 rounded py-2 px-3">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Upload Attachment (Optional):</label>
            <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf" id="attachmentInput" class="block w-full text-gray-300 bg-gray-700 border border-gray-600 rounded py-2 px-3">
        </div>

        <div class="flex gap-4 mb-6">
            <button type="button" id="showEmailsBtn" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                <i class="fas fa-envelope-open-text"></i> Show Emails from CSV
            </button>
            <button type="button" id="clearEmailsBtn" class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded">
                <i class="fas fa-trash"></i> Clear Emails
            </button>
        </div>

        <div class="overflow-x-auto bg-gray-700 p-4 rounded-lg shadow-lg mb-6">
            <h2 class="text-lg font-medium mb-2">Emails from Uploaded CSV</h2>
            <table id="uploadedEmailsTable" class="w-full text-left text-sm">
                <thead class="bg-gray-600">
                    <tr>
                        <th class="py-2 px-4 border-b border-gray-500">Sr No</th>
                        <th class="py-2 px-4 border-b border-gray-500">Name</th>
                        <th class="py-2 px-4 border-b border-gray-500">Email</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-600">
                    <!-- Dynamic rows will be inserted here -->
                </tbody>
            </table>
        </div>
<!-- Template Management Section -->
<div class="mt-6">
    <button id="showTemplatesBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
        <i class="fas fa-folder-open"></i> Show Templates
    </button>
    <button id="createNewTemplateBtn" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded">
        <i class="fas fa-plus-circle"></i> Create New Template
    </button>
</div>
<!-- New Template Modal -->
<div id="newTemplateModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden">
    <div class="bg-gray-900 text-gray-100 p-6 rounded-lg shadow-lg w-full max-w-lg">
        <header class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Create New Template</h2>
            <button id="closeNewTemplateModal" class="text-gray-400 hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </header>

        <div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Name:</label>
                <input id="newTemplateName" type="text" class="block w-full text-gray-300 bg-gray-700 border border-gray-600 rounded py-2 px-3">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Subject:</label>
                <input id="newTemplateSubject" type="text" class="block w-full text-gray-300 bg-gray-700 border border-gray-600 rounded py-2 px-3">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Body:</label>
                <textarea id="newTemplateBody" class="block w-full text-gray-300 bg-gray-700 border border-gray-600 rounded py-2 px-3 resize-none min-h-[100px]"></textarea>
            </div>
        </div>

        <footer class="mt-4 flex justify-between">
            <button id="saveNewTemplate" class="bg-green-600 hover:bg-green-500 text-white font-semibold py-2 px-4 rounded">Save</button>
            <button id="closeNewTemplateFooter" class="bg-gray-700 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded">Close</button>
        </footer>
    </div>
</div>
<!-- Template Modal -->
<div id="templateModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden">
    <div class="bg-gray-900 text-gray-100 p-6 rounded-lg shadow-lg w-full max-w-3xl">
        <header class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Email Templates</h2>
            <button id="closeTemplateModal" class="text-gray-400 hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </header>

        <div id="templateList" class="overflow-y-auto max-h-96">
            <!-- Templates will be dynamically loaded here -->
        </div>

        <footer class="mt-4">
            <button id="closeTemplateModalFooter" class="bg-gray-700 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded">Close</button>
        </footer>
    </div>
</div>

<!-- Template Details Modal -->
<div id="templateDetailsModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden">
    <div class="bg-gray-900 text-gray-100 p-6 rounded-lg shadow-lg w-full max-w-lg">
        <header class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Template Details</h2>
            <button id="closeTemplateDetailsModal" class="text-gray-400 hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </header>

        <div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Name:</label>
                <div class="flex items-center">
                    <input id="templateName" type="text" class="block w-full text-gray-300 bg-gray-700 border border-gray-600 rounded py-2 px-3">
                    <button class="ml-2 text-blue-500 hover:text-blue-400 copy-btn" data-copy-target="#templateName">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Subject:</label>
                <div class="flex items-center">
                    <input id="templateSubject" type="text" class="block w-full text-gray-300 bg-gray-700 border border-gray-600 rounded py-2 px-3">
                    <button class="ml-2 text-blue-500 hover:text-blue-400 copy-btn" data-copy-target="#templateSubject">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Body:</label>
                <div class="flex items-center">
                    <textarea id="templateBody" class="block w-full text-gray-300 bg-gray-700 border border-gray-600 rounded py-2 px-3 resize-none min-h-[100px]"></textarea>
                    <button class="ml-2 text-blue-500 hover:text-blue-400 copy-btn" data-copy-target="#templateBody">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
        </div>

        <footer class="mt-4 flex justify-between">
            <button id="saveTemplate" class="bg-green-600 hover:bg-green-500 text-white font-semibold py-2 px-4 rounded">Save</button>
            <button id="closeTemplateDetailsFooter" class="bg-gray-700 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded">Close</button>
            <!-- Add this button inside the footer of your template details modal -->
<button id="loadToFields" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
    Load to Fields
</button>

        </footer>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Ensure the event listener is added after the document is fully loaded
    document.getElementById('loadToFields').addEventListener('click', function() {
        // Retrieve values from modal inputs
        var modalSubject = document.getElementById('templateSubject').value;
        var modalBody = document.getElementById('templateBody').value;

        // Set these values to the main form's subject and body inputs
        document.querySelector('[name="subject"]').value = modalSubject;
        document.querySelector('[name="body"]').value = modalBody;

        // Hide the modal after transferring the data
        document.getElementById('templateDetailsModal').classList.add('hidden');

        // Prevent any form submission if this button is within a form
        event.preventDefault();
    });
});

</script>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Subject:</label>
            <input type="text" name="subject" required class="block w-full text-gray-300 bg-gray-700 border border-gray-600 rounded py-2 px-3">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Content:</label>
            <textarea name="body" placeholder="Use placeholders like {name}, {company}" required class="block w-full text-gray-300 bg-gray-700 border border-gray-600 rounded py-2 px-3 resize-none min-h-[100px]"></textarea>
        </div>

        <div class="flex gap-4 mb-6">
            <button type="button" id="previewEmailBtn" class="bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-2 px-4 rounded">
                <i class="fas fa-eye"></i> Preview Email
            </button>
        </div>

        <div id="emailPreview" class="hidden bg-gray-700 p-4 rounded-lg shadow-lg mb-6">
            <h3 class="text-lg font-medium mb-2">Email Preview</h3>
            <iframe id="emailPreviewFrame" class="w-full h-64 border border-gray-600 rounded"></iframe>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Delay between emails (seconds):</label>
            <input type="number" name="delay" min="1" value="1" required class="block w-full text-gray-300 bg-gray-700 border border-gray-600 rounded py-2 px-3">
        </div>
        <div id="progressWrapper" class="hidden w-full bg-gray-300 rounded mt-4 shadow-lg">
    <div id="progressBar" class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-center py-2 rounded shadow-md font-bold" style="width: 0%">
        0%
    </div>
</div>

<table id="emailStatusTable" class="w-full mt-6 border-collapse hidden bg-gray-900 rounded-lg overflow-hidden shadow-lg">
    <thead>
        <tr class="bg-indigo-700 text-white">
            <th class="border border-gray-700 px-4 py-2">Email</th>
            <th class="border border-gray-700 px-4 py-2">Status</th>
            <th class="border border-gray-700 px-4 py-2">Timestamp</th>
        </tr>
    </thead>
    <tbody>
        <!-- Email statuses will be appended dynamically -->
    </tbody>
</table>

<!-- Additional Metrics Section -->
<div id="metricsWrapper" class="hidden mt-6 bg-indigo-800 p-4 rounded-lg shadow-lg">
    <p id="elapsedTime" class="text-lg font-semibold text-indigo-200">Elapsed Time: 00:00</p>
    <p id="averageDelay" class="text-lg font-semibold text-indigo-200">Average Delay Between Emails: Calculating...</p>
</div>

<!-- Campaign Results Button -->
<div id="resultsButtonWrapper" class="hidden mt-6 flex justify-center">
    <a href="campaign_results.php" target="_blank" class="bg-green-600 hover:bg-green-700 text-white text-lg font-semibold py-2 px-6 rounded shadow-lg">
        <i class="fas fa-chart-line"></i> View Campaign Results
    </a>
</div>



<div id="loader" class="hidden flex items-center justify-center mt-4">
    <div class="w-6 h-6 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
    <span class="ml-2 text-sm text-indigo-300">Processing...</span>
</div>

<br>
<div class="flex justify-between items-center">
    <button id="startCampaignBtn" type="button" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded">
        <i class="fas fa-play"></i> Start Sending Emails
    </button>
</div>

<!-- Confirmation Modal -->
<div id="confirmationModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-gray-900 text-gray-100 p-6 rounded-lg shadow-lg w-full max-w-sm">
        <h2 class="text-lg font-semibold mb-4">Confirm Action</h2>
        <p class="mb-4">Enter the password to start sending emails:</p>
        <input
            type="password"
            id="confirmationPassword"
            placeholder="Enter password"
            class="w-full text-gray-300 bg-gray-700 border border-gray-600 rounded py-2 px-3 mb-4"
        />
        <div class="flex justify-end gap-4">
            <button id="cancelModalBtn" class="bg-gray-700 hover:bg-gray-600 text-white py-2 px-4 rounded">Cancel</button>
            <button id="confirmModalBtn" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded">Confirm</button>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
    const startCampaignBtn = document.getElementById('startCampaignBtn');
    const confirmationModal = document.getElementById('confirmationModal');
    const confirmationPassword = document.getElementById('confirmationPassword');
    const confirmModalBtn = document.getElementById('confirmModalBtn');
    const cancelModalBtn = document.getElementById('cancelModalBtn');

    // Predefined password (case-insensitive)
    const predefinedPassword = "yes";

    // Show modal when clicking "Start Sending Emails"
    startCampaignBtn.addEventListener('click', () => {
        confirmationModal.classList.remove('hidden');
    });

    // Hide modal when clicking "Cancel"
    cancelModalBtn.addEventListener('click', () => {
        confirmationModal.classList.add('hidden');
        confirmationPassword.value = ""; // Clear the password input
    });

    // Confirm password
    confirmModalBtn.addEventListener('click', () => {
        if (confirmationPassword.value.toLowerCase() === predefinedPassword.toLowerCase()) {
            confirmationModal.classList.add('hidden'); // Hide modal
            confirmationPassword.value = ""; // Clear the password input
        }
    });
});

</script>
            <button id="stopCampaignBtn" class="hidden bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded">
        <i class="fas fa-stop-circle"></i> Stop Campaign
    </button>
            <div id="loader" class="hidden border-4 border-gray-200 border-t-blue-500 rounded-full w-6 h-6 animate-spin"></div>
        </div>
    </form>
    <script src="script.js"></script>
</body>
</html>
