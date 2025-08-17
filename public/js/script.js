let startTime;
let campaignStopped = false;
let elapsedTimeTimer;
let timestamps = [];

$('#csvMailerForm').submit(function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    // Get delay value from the form
    const delay = parseInt($('input[name="delay"]').val()) * 1000; // Convert to milliseconds

    // Reset progress bar, table, and metrics
    $('#progressWrapper').removeClass('hidden');
    $('#progressBar').css('width', '0%').text('0%');
    $('#emailStatusTable tbody').empty();
    $('#emailStatusTable').removeClass('hidden');
    $('#metricsWrapper').removeClass('hidden');

    // Reset elapsed time
    startTime = Date.now();
    campaignStopped = false;
    clearTimeout(elapsedTimeTimer);
    timestamps = [];
    updateElapsedTime();

    // Show the small loader and stop button
    $('#loader').removeClass('hidden');
    $('#stopCampaignBtn').removeClass('hidden');

    // Upload the CSV file
    $.ajax({
        url: 'csv_mailer.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            const data = JSON.parse(response);
            if (data.totalEmails > 0) {
                pollProgress(delay);
            }
        },
        error: function () {
            alert('Failed to upload CSV file.');
            $('#loader').addClass('hidden');
            $('#stopCampaignBtn').addClass('hidden');
        }
    });
});

function updateElapsedTime() {
    if (campaignStopped) return;

    const elapsed = Math.floor((Date.now() - startTime) / 1000);
    const minutes = String(Math.floor(elapsed / 60)).padStart(2, '0');
    const seconds = String(elapsed % 60).padStart(2, '0');
    $('#elapsedTime').text(`Elapsed Time: ${minutes}:${seconds}`);

    elapsedTimeTimer = setTimeout(updateElapsedTime, 1000);
}

function pollProgress(delay) {
    if (campaignStopped) return;

    $.ajax({
        url: 'csv_mailer.php',
        type: 'GET',
        success: function (response) {
            const data = JSON.parse(response);

            // Update Progress Bar
            if (data.progress !== undefined) {
                $('#progressBar').css('width', `${data.progress}%`);
                $('#progressBar').text(`${data.progress}%`);
            }

            // Append email status and timestamp
            if (data.email && data.status) {
                const timestamp = new Date().toLocaleTimeString();
                timestamps.push(timestamp);

                const row = `
                    <tr>
                        <td class="border border-gray-500 px-4 py-2">${data.email}</td>
                        <td class="border border-gray-500 px-4 py-2 ${data.status === 'sent' ? 'text-green-500' : 'text-red-500'}">
                            ${data.status}
                        </td>
                        <td class="border border-gray-500 px-4 py-2">${timestamp}</td>
                    </tr>
                `;
                $('#emailStatusTable tbody').append(row);
            }

            // Continue polling until all emails are sent
            if (data.progress < 100) {
                setTimeout(() => pollProgress(delay), delay);
            } else {
                clearTimeout(elapsedTimeTimer);
                calculateMetrics();

                // Show success message and hide unnecessary elements
                alert('Emails sent successfully!');
                $('#loader').addClass('hidden');
                $('#stopCampaignBtn').addClass('hidden');

                // Display the results button
                $('#resultsButtonWrapper').removeClass('hidden');
            }
        },
        error: function () {
            alert('An error occurred while processing emails.');
            $('#loader').addClass('hidden');
        }
    });
}

function calculateMetrics() {
    if (timestamps.length < 2) {
        $('#averageDelay').text('Average Delay Between Emails: Not enough data');
        return;
    }

    const gaps = [];
    for (let i = 1; i < timestamps.length; i++) {
        const time1 = new Date(`1970-01-01T${timestamps[i - 1]}`);
        const time2 = new Date(`1970-01-01T${timestamps[i]}`);
        gaps.push((time2 - time1) / 1000);
    }

    const averageDelay = gaps.reduce((a, b) => a + b, 0) / gaps.length;
    $('#averageDelay').text(`Average Delay Between Emails: ${averageDelay.toFixed(2)} seconds`);
}

$('#stopCampaignBtn').click(function () {
    if (!confirm('Are you sure you want to stop the campaign?')) return;

    campaignStopped = true;

    $.ajax({
        url: 'csv_mailer.php',
        type: 'POST',
        data: { action: 'stop' },
        success: function (response) {
            const data = JSON.parse(response);
            alert(data.message);
            $('#stopCampaignBtn').addClass('hidden');
            $('#loader').addClass('hidden');
            clearTimeout(elapsedTimeTimer);
        },
        error: function () {
            alert('Failed to stop the campaign.');
        }
    });
});

$('#showEmailsBtn').click(function () {
    const fileInput = $('#csvFileInput')[0];
    if (fileInput.files.length === 0) {
        alert('Please upload a CSV file to display emails.');
        return;
    }

    const file = fileInput.files[0];
    const reader = new FileReader();

    reader.onload = function (event) {
        const lines = event.target.result.split('\n');
        const headers = lines[0].split(',').map(header => header.trim());
        const nameIndex = headers.indexOf('name');
        const emailIndex = headers.indexOf('email');

        if (emailIndex === -1) {
            alert('The uploaded CSV file must contain an "email" column.');
            return;
        }

        const tbody = $('#uploadedEmailsTable tbody');
        tbody.empty(); // Clear any previous rows

        for (let i = 1; i < lines.length; i++) {
            const values = lines[i].split(',').map(value => value.trim());
            const srNo = i; // Serial number starts from 1
            const name = nameIndex !== -1 ? values[nameIndex] || 'N/A' : 'N/A';
            const email = values[emailIndex];

            if (email && email.includes('@')) {
                const row = `<tr>
                    <td class='py-2 px-4 border-b border-gray-500'>${srNo}</td>
                    <td class='py-2 px-4 border-b border-gray-500'>${name}</td>
                    <td class='py-2 px-4 border-b border-gray-500'>${email}</td>
                </tr>`;
                tbody.append(row);
            }
        }
    };

    reader.readAsText(file);
});

$('#clearEmailsBtn').click(function () {
    $('#uploadedEmailsTable tbody').empty(); // Clear the table rows
});

$('#previewEmailBtn').click(function () {
    const previewSection = $('#emailPreview');

    // Toggle visibility of the preview section
    if (previewSection.is(':visible')) {
        previewSection.hide();
        $(this).html('<i class="fas fa-eye"></i> Preview Email'); // Change button text to "Preview Email"
        return;
    }

    const subject = $('input[name="subject"]').val();
    const body = $('textarea[name="body"]').val(); // Assume this is the HTML email template
    const fileInput = $('#csvFileInput')[0];

    if (fileInput.files.length === 0) {
        alert('Please upload a CSV file to preview the email.');
        return;
    }

    const file = fileInput.files[0];
    const reader = new FileReader();

    reader.onload = function (event) {
        const lines = event.target.result.split('\n');
        const headers = lines[0].split(',').map(header => header.trim().toLowerCase());
        const firstRow = lines[1].split(',').map(value => value.trim());

        let previewHTML = body;

        // Replace placeholders with actual values from the first row of the CSV
        headers.forEach((header, index) => {
            const placeholder = `{${header}}`;
            previewHTML = previewHTML.replaceAll(placeholder, firstRow[index] || '');
        });

        // Dynamically generate tracking link and replace {tracking_link} placeholder
        const emailIndex = headers.indexOf('email');
        if (emailIndex !== -1) {
            const email = firstRow[emailIndex];
            const trackingLink = `https://beastsmm.xyz/track.php?email=${encodeURIComponent(email)}`;
            previewHTML = previewHTML.replaceAll('{tracking_link}', `<a href="${trackingLink}" target="_blank">${trackingLink}</a>`);
        } else {
            alert('The uploaded CSV must contain an "email" column for the tracking link.');
        }

        // Render the email in an iframe
        const iframe = document.getElementById('emailPreviewFrame');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        iframeDoc.open();
        iframeDoc.write(`<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 0;
                        padding: 20px;
                        background-color: #f8f9fa;
                    }
                </style>
            </head>
            <body>
                ${previewHTML}
            </body>
            </html>`);
        iframeDoc.close();

        // Show the preview section and update button text
        previewSection.show();
        $('#previewEmailBtn').html('<i class="fas fa-eye-slash"></i> Hide Preview'); // Change button text to "Hide Preview"
    };

    reader.readAsText(file);
});


$(document).ready(function () {
    // Show Create New Template Modal
    $('#createNewTemplateBtn').click(function () {
        $('#newTemplateModal').removeClass('hidden');
    });

    // Close Create New Template Modal
    $('#closeNewTemplateModal, #closeNewTemplateFooter').click(function () {
        $('#newTemplateModal').addClass('hidden');
    });

    // Save New Template
    $('#saveNewTemplate').click(function () {
        const name = $('#newTemplateName').val();
        const subject = $('#newTemplateSubject').val();
        const body = $('#newTemplateBody').val();

        if (!name || !subject || !body) {
            alert('All fields are required.');
            return;
        }

        $.ajax({
            url: 'api.php?endpoint=templates',
            type: 'POST',
            data: {
                name: name,
                subject: subject,
                body: body
            },
            success: function (response) {
                if (response.success) {
                    alert('Template created successfully');
                    $('#newTemplateModal').addClass('hidden');
                    $('#newTemplateName, #newTemplateSubject, #newTemplateBody').val('');
                    loadTemplates(); // Refresh the template list
                } else {
                    alert(response.error || 'Failed to create template');
                }
            },
            error: function () {
                alert('Failed to create template');
            }
        });
    });

    // Load Templates (existing function)
    function loadTemplates() {
        // Your existing code
    }
});
$(document).ready(function () {
    let currentTemplateId = null; // Track the currently edited template

    // Show templates modal
    $('#showTemplatesBtn').click(function () {
        $('#templateModal').removeClass('hidden');
        loadTemplates();
    });

    // Close templates modal
    $('#closeTemplateModal, #closeTemplateModalFooter').click(function () {
        $('#templateModal').addClass('hidden');
    });

    // Close template details modal
    $('#closeTemplateDetailsModal, #closeTemplateDetailsFooter').click(function () {
        $('#templateDetailsModal').addClass('hidden');
    });

    // Load templates
    function loadTemplates() {
        $.ajax({
            url: 'api.php?endpoint=templates',
            type: 'GET',
            success: function (data) {
                const templateList = $('#templateList');
                templateList.empty();

                data.forEach(template => {
                    const templateItem = $(
                        `<div class="mb-4 p-4 bg-gray-800 rounded shadow">
                            <h3 class="text-lg font-medium">${template.name}</h3>
                            <div class="flex gap-2 mt-2">
                                <button class="bg-green-500 hover:bg-green-600 text-white font-semibold py-1 px-3 rounded open-template" data-id="${template.id}">Open</button>
                                <button class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-1 px-3 rounded edit-template" data-id="${template.id}">Edit</button>
                                <button class="bg-red-500 hover:bg-red-600 text-white font-semibold py-1 px-3 rounded delete-template" data-id="${template.id}">Delete</button>
                            </div>
                        </div>`
                    );

                    templateList.append(templateItem);
                });

                // Attach click handlers
                $('.open-template').click(function () {
                    const id = $(this).data('id');
                    openTemplate(id);
                });

                $('.edit-template').click(function () {
                    const id = $(this).data('id');
                    editTemplate(id);
                });

                $('.delete-template').click(function () {
                    const id = $(this).data('id');
                    deleteTemplate(id);
                });
            },
            error: function () {
                alert('Failed to load templates');
            }
        });
    }

    // Open template details
    function openTemplate(id) {
        $.ajax({
            url: `api.php?endpoint=templates&id=${id}`,
            type: 'GET',
            success: function (template) {
                if (template.error) {
                    alert(template.error);
                    return;
                }

                currentTemplateId = id; // Track the current template ID
                $('#templateName').val(template.name);
                $('#templateSubject').val(template.subject);
                $('#templateBody').val(template.body);

                $('#templateDetailsModal').removeClass('hidden');
            },
            error: function () {
                alert('Failed to load template details');
            }
        });
    }

    // Copy to clipboard functionality
    $(document).on('click', '.copy-btn', function () {
        const target = $($(this).data('copy-target'));
        navigator.clipboard.writeText(target.val()).then(() => {
            alert('Copied to clipboard');
        }).catch(() => {
            alert('Failed to copy');
        });
    });

    // Delete template
    function deleteTemplate(id) {
        if (!confirm('Are you sure you want to delete this template?')) return;

        $.ajax({
            url: `api.php?endpoint=templates&id=${id}`,
            type: 'DELETE',
            success: function (response) {
                if (response.success) {
                    loadTemplates();
                } else {
                    alert(response.error);
                }
            },
            error: function () {
                alert('Failed to delete template');
            }
        });
    }

    // Edit template (open for editing)
    function editTemplate(id) {
        openTemplate(id); // Opens the modal for editing
    }

    // Save edited template
    $('#saveTemplate').click(function () {
        if (!currentTemplateId) {
            alert('No template selected for saving.');
            return;
        }

        const updatedTemplate = {
            name: $('#templateName').val(),
            subject: $('#templateSubject').val(),
            body: $('#templateBody').val()
        };

        $.ajax({
            url: `api.php?endpoint=templates&id=${currentTemplateId}`,
            type: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify(updatedTemplate),
            success: function (response) {
                if (response.success) {
                    alert('Template updated successfully');
                    $('#templateDetailsModal').addClass('hidden');
                    loadTemplates();
                } else {
                    alert(response.error);
                }
            },
            error: function () {
                alert('Failed to save template');
            }
        });
    });
});

function updateEmailLogs(emailLogs) {
    const logsTableBody = document.getElementById('emailLogsBody');
    logsTableBody.innerHTML = '';  // Clear current logs

    emailLogs.forEach(log => {
        const row = document.createElement('tr');

        // Email column
        const emailCell = document.createElement('td');
        emailCell.className = 'py-2 px-4 border-b border-gray-500';
        emailCell.textContent = log.email;
        row.appendChild(emailCell);

        // Status column
        const statusCell = document.createElement('td');
        statusCell.className = 'py-2 px-4 border-b border-gray-500';
        statusCell.textContent = log.status;
        row.appendChild(statusCell);

        // Error column (if any)
        const errorCell = document.createElement('td');
        errorCell.className = 'py-2 px-4 border-b border-gray-500';
        errorCell.textContent = log.error || '-';
        row.appendChild(errorCell);

        // Append the row to the table body
        logsTableBody.appendChild(row);
    });
}

// Update email logs on the frontend as they are sent
function handleEmailLogs(response) {
    if (response.emailLogs) {
        updateEmailLogs(response.emailLogs);
    }
}

// Example of handling the response from the backend
$.ajax({
    url: 'csv_mailer.php',
    method: 'POST',
    data: formData,  // Replace with actual form data
    success: function(response) {
        const jsonResponse = JSON.parse(response);
        handleEmailLogs(jsonResponse);
    },
    error: function() {
        alert("An error occurred.");
    }
});

