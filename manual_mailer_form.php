<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual Mailer</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1e1e2f;
            color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #c5c6c7;
        }
        form {
            background-color: #282a36;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input, textarea, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #44475a;
            border-radius: 4px;
            background-color: #44475a;
            color: #f5f5f5;
        }
        button {
            background-color: #6272a4;
            cursor: pointer;
        }
        button:hover {
            background-color: #50fa7b;
        }
        #progress {
            background-color: #44475a;
            padding: 10px;
            border-radius: 8px;
            margin-top: 20px;
            display: none;
        }
        #loader {
            border: 4px solid #f3f3f3;
            border-radius: 50%;
            border-top: 4px solid #6272a4;
            width: 16px;
            height: 16px;
            animation: spin 2s linear infinite;
            display: inline-block;
            vertical-align: middle;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .error-message {
            color: #ff5555;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Send Emails to Manually Inputted Addresses</h1>

    <form id="manualMailerForm" action="manual_mailer.php" method="POST">
        <div id="emailFields">
            <input type="email" name="emails[]" placeholder="Recipient Email" required>
        </div>
        <button type="button" id="addEmailField">Add More Emails</button><br><br>

        <label>Email Subject:</label>
        <input type="text" name="subject" required>

        <label>Email Body:</label>
        <textarea name="body" required></textarea>

        <label>Delay between emails (seconds):</label>
        <input type="number" name="delay" min="1" value="1" required>

        <button type="submit">Start Sending Emails</button>
        <div id="loader" style="display: none;"></div>
    </form>

    <div id="progress">
        <h2>Progress</h2>
        <p>Emails Sent: <span id="emailsSent">0</span></p>
        <p>Emails Remaining: <span id="emailsRemaining">0</span></p>
    </div>

    <div class="error-message" id="error-message"></div>

    <script>
        document.getElementById('addEmailField').onclick = function() {
            var emailField = document.createElement('input');
            emailField.type = 'email';
            emailField.name = 'emails[]';
            emailField.placeholder = 'Recipient Email';
            emailField.required = true;
            emailField.style.display = 'block';
            document.getElementById('emailFields').appendChild(emailField);
        };

        $('#manualMailerForm').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $('#loader').show();
            $('#error-message').text('');
            $('#progress').show();

            $.post('manual_mailer.php', formData, function(response) {
                $('#progress').show();
                if (response.error) {
                    $('#error-message').text(response.error);
                    $('#loader').hide();
                } else {
                    let data = JSON.parse(response);
                    let interval = setInterval(function() {
                        $.getJSON('status.php', { campaign_id: data.campaign_id }, function(status) {
                            $('#emailsSent').text(status.emailsSent);
                            $('#emailsRemaining').text(status.emailsRemaining);
                            if (status.complete) {
                                clearInterval(interval);
                                $('#loader').hide();
                                alert("Emails sent successfully!");
                            }
                        });
                    }, 1000);
                }
            }).fail(function(xhr, status, error) {
                $('#error-message').text("An error occurred: " + error);
                $('#loader').hide();
            });
        });
    </script>
</body>
</html>
