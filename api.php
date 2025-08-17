<?php
header('Content-Type: application/json');
$mysqli = new mysqli("localhost", "root", "", "bulk_mailer");

// Handle connection errors
if ($mysqli->connect_error) {
    die(json_encode(["error" => $mysqli->connect_error]));
}

// Helper function to send JSON response
function response($data) {
    echo json_encode($data);
    exit;
}

$request_method = $_SERVER['REQUEST_METHOD'];
$endpoint = $_GET['endpoint'] ?? '';

switch ($endpoint) {
    case 'logs':
        // Handle email logs requests
        handleEmailLogs($mysqli, $request_method);
        break;

    case 'templates':
        // Handle template requests
        handleTemplates($mysqli, $request_method);
        break;

    default:
        response(["error" => "Invalid endpoint"]);
}

function handleEmailLogs($mysqli, $method) {
    if ($method == 'GET') {
        $result = $mysqli->query("SELECT * FROM email_logs ORDER BY timestamp DESC");
        $logs = $result->fetch_all(MYSQLI_ASSOC);
        response($logs);
    } elseif ($method == 'POST') {
        $email = $_POST['email'];
        $status = $_POST['status'];
        if (!$email || !$status) {
            response(["error" => "Email and status are required"]);
        }
        $stmt = $mysqli->prepare("INSERT INTO email_logs (email, status) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $status);
        $stmt->execute();
        response(["success" => true]);
    }
}

function handleTemplates($mysqli, $method) {
    $id = $_GET['id'] ?? 0;
    if ($method == 'GET') {
        if ($id) {
            $stmt = $mysqli->prepare("SELECT id, name, subject, body FROM email_templates WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $template = $result->fetch_assoc();
            response($template ? $template : ["error" => "Template not found"]);
        } else {
            $result = $mysqli->query("SELECT * FROM email_templates ORDER BY created_at DESC");
            $templates = $result->fetch_all(MYSQLI_ASSOC);
            response($templates);
        }
    } elseif ($method == 'POST') {
        $name = $_POST['name'];
        $subject = $_POST['subject'];
        $body = $_POST['body'];
        if (!$name || !$subject || !$body) {
            response(["error" => "Name, subject, and body are required"]);
        }
        $stmt = $mysqli->prepare("INSERT INTO email_templates (name, subject, body) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $subject, $body);
        $stmt->execute();
        response(["success" => true]);
    } elseif ($method == 'PUT') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$id || !$input['name'] || !$input['subject'] || !$input['body']) {
            response(["error" => "ID, name, subject, and body are required for update"]);
        }
        $stmt = $mysqli->prepare("UPDATE email_templates SET name = ?, subject = ?, body = ? WHERE id = ?");
        $stmt->bind_param("sssi", $input['name'], $input['subject'], $input['body'], $id);
        if ($stmt->execute()) {
            response(["success" => true]);
        } else {
            response(["error" => "Failed to update template"]);
        }
    } elseif ($method == 'DELETE') {
        if (!$id) {
            response(["error" => "Template ID is required"]);
        }
        $stmt = $mysqli->prepare("DELETE FROM email_templates WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        response(["success" => true]);
    }
}
?>
