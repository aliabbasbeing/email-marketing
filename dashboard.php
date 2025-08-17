<?php
include('includes/db_connect.php');
include('config.php'); // Include the config file for the base URL

// Handle AJAX requests for templates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');

    $action = $_POST['action'];

    if ($action === 'add' || $action === 'edit') {
        $name = trim($_POST['name'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $body = trim($_POST['body'] ?? '');
        $templateId = $_POST['id'] ?? null;

        if (empty($name) || empty($subject) || empty($body)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit;
        }

        try {
            if ($action === 'add') {
                // Add a new template
                $stmt = $pdo->prepare("INSERT INTO email_templates (name, subject, body, created_at) VALUES (:name, :subject, :body, NOW())");
                $stmt->execute([
                    ':name' => $name,
                    ':subject' => $subject,
                    ':body' => $body,
                ]);
            } elseif ($action === 'edit' && $templateId) {
                // Edit an existing template
                $stmt = $pdo->prepare("UPDATE email_templates SET name = :name, subject = :subject, body = :body WHERE id = :id");
                $stmt->execute([
                    ':name' => $name,
                    ':subject' => $subject,
                    ':body' => $body,
                    ':id' => $templateId,
                ]);
            }

            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    } elseif ($action === 'delete' && isset($_POST['id'])) {
        $templateId = $_POST['id'];

        try {
            $stmt = $pdo->prepare("DELETE FROM email_templates WHERE id = :id");
            $stmt->execute([':id' => $templateId]);

            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'view') {
    $templateId = $_GET['id'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM email_templates WHERE id = :id");
        $stmt->execute([':id' => $templateId]);
        $template = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($template) {
            echo json_encode(['success' => true, 'template' => $template]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Template not found.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Fetch all saved campaigns
$stmt = $pdo->query("SELECT * FROM campaigns ORDER BY created_at DESC");
$campaigns = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all templates
$templateStmt = $pdo->query("SELECT * FROM email_templates ORDER BY created_at DESC");
$templates = $templateStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-900 text-gray-100 font-sans p-6">
<?php
include('header.php');
?>

    <!-- Campaign Section -->
    <div class="bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-2xl font-semibold text-indigo-300 mb-4">Saved Campaigns</h2>
        <?php if (count($campaigns) > 0): ?>
            <table class="w-full border-collapse bg-gray-800 rounded-lg overflow-hidden shadow-lg">
                <thead>
                    <tr class="bg-indigo-700 text-white">
                        <th class="border border-gray-700 px-4 py-2">Campaign Name</th>
                        <th class="border border-gray-700 px-4 py-2">Created At</th>
                        <th class="border border-gray-700 px-4 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($campaigns as $campaign): ?>
                        <tr class="hover:bg-gray-700">
                            <td class="border border-gray-700 px-4 py-2 text-indigo-200">
                                <?php echo htmlspecialchars($campaign['name']); ?>
                            </td>
                            <td class="border border-gray-700 px-4 py-2 text-indigo-200">
                                <?php echo htmlspecialchars($campaign['created_at']); ?>
                            </td>
                            <td class="border border-gray-700 px-4 py-2">
                            <a href="campaign.php?id=<?php echo $campaign['id']; ?>" 
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded"
                                target="_blank">
                                    <i class="fas fa-eye"></i> View Campaign
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-gray-400 text-center py-4">No campaigns have been saved yet.</p>
        <?php endif; ?>
    </div>

    <!-- Template Section -->
    <div class="bg-gray-800 rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-semibold text-indigo-300 mb-4">Email Templates</h2>
        <button id="addTemplateBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded mb-4">
            <i class="fas fa-plus"></i> Add New Template
        </button>
        <?php if (count($templates) > 0): ?>
            <table class="w-full border-collapse bg-gray-800 rounded-lg overflow-hidden shadow-lg">
                <thead>
                    <tr class="bg-indigo-700 text-white">
                        <th class="border border-gray-700 px-4 py-2">Template Name</th>
                        <th class="border border-gray-700 px-4 py-2">Created At</th>
                        <th class="border border-gray-700 px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($templates as $template): ?>
                        <tr class="hover:bg-gray-700">
                            <td class="border border-gray-700 px-4 py-2 text-indigo-200">
                                <?php echo htmlspecialchars($template['name']); ?>
                            </td>
                            <td class="border border-gray-700 px-4 py-2 text-indigo-200">
                                <?php echo htmlspecialchars($template['created_at']); ?>
                            </td>
                            <td class="border border-gray-700 px-4 py-2">
                                <button class="viewTemplateBtn bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded" data-id="<?php echo $template['id']; ?>">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <button class="editTemplateBtn bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded" data-id="<?php echo $template['id']; ?>" data-name="<?php echo htmlspecialchars($template['name']); ?>" data-subject="<?php echo htmlspecialchars($template['subject']); ?>" data-body="<?php echo htmlspecialchars($template['body']); ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="deleteTemplateBtn bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded" data-id="<?php echo $template['id']; ?>">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-gray-400 text-center py-4">No templates have been created yet.</p>
        <?php endif; ?>
    </div>

    <!-- Template Modal -->
    <div id="templateModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center hidden">
        <div class="bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-3xl">
            <header class="flex justify-between items-center mb-4">
                <h2 id="templateModalTitle" class="text-2xl font-semibold text-indigo-300"></h2>
                <button id="closeTemplateModal" class="text-gray-300 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </header>
            <form id="templateForm">
                <input type="hidden" id="templateId" name="id">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-200">Template Name:</label>
                    <input id="templateName" name="name" type="text" class="w-full p-2 rounded bg-gray-700 text-gray-200 border border-gray-600">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-200">Template Subject:</label>
                    <input id="templateSubject" name="subject" type="text" class="w-full p-2 rounded bg-gray-700 text-gray-200 border border-gray-600">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-200">Template Body:</label>
                    <textarea id="templateBody" name="body" class="w-full p-2 rounded bg-gray-700 text-gray-200 border border-gray-600"></textarea>
                </div>
                <footer class="flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                        Save Template
                    </button>
                </footer>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Open Add Template Modal
            $('#addTemplateBtn').click(function () {
                $('#templateModalTitle').text('Add New Template');
                $('#templateForm')[0].reset();
                $('#templateId').val('');
                $('#templateModal').removeClass('hidden');
            });

            // Open Edit Template Modal
            $('.editTemplateBtn').click(function () {
                $('#templateModalTitle').text('Edit Template');
                $('#templateId').val($(this).data('id'));
                $('#templateName').val($(this).data('name'));
                $('#templateSubject').val($(this).data('subject'));
                $('#templateBody').val($(this).data('body'));
                $('#templateModal').removeClass('hidden');
            });

            // View Template
$('.viewTemplateBtn').click(function () {
    const templateId = $(this).data('id');

    $.ajax({
        url: '', // Use the current script
        type: 'GET',
        data: { action: 'view', id: templateId },
        success: function (response) {
            if (response.success) {
                const { name, subject, body } = response.template;
                $('#templateModalTitle').text(`View Template: ${name}`);
                $('#templateName').val(name).prop('disabled', true);
                $('#templateSubject').val(subject).prop('disabled', true);
                $('#templateBody').val(body).prop('disabled', true);
                $('#templateModal').removeClass('hidden');
            } else {
                alert('Failed to fetch template details: ' + response.message);
            }
        },
        error: function () {
            alert('An error occurred while fetching template details.');
        }
    });
});

            // Delete Template
            $('.deleteTemplateBtn').click(function () {
                const templateId = $(this).data('id');
                if (!confirm('Are you sure you want to delete this template?')) return;

                $.post('', { action: 'delete', id: templateId }, function (response) {
                    if (response.success) {
                        alert('Template deleted successfully.');
                        location.reload();
                    } else {
                        alert('Failed to delete template: ' + response.message);
                    }
                });
            });

            // Close Template Modal
            $('#closeTemplateModal').click(function () {
                $('#templateModal').addClass('hidden');
            });

            // Save Template (Add or Edit)
            $('#templateForm').submit(function (e) {
                e.preventDefault();

                const action = $('#templateId').val() ? 'edit' : 'add';
                const templateData = {
                    action: action,
                    id: $('#templateId').val(),
                    name: $('#templateName').val(),
                    subject: $('#templateSubject').val(),
                    body: $('#templateBody').val(),
                };

                $.post('', templateData, function (response) {
                    if (response.success) {
                        alert('Template saved successfully.');
                        $('#templateModal').addClass('hidden');
                        location.reload();
                    } else {
                        alert('Failed to save template: ' + response.message);
                    }
                });
            });
        });
    </script>
</body>
</html>
