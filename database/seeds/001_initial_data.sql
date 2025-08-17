-- Default admin user (password: admin123)
INSERT INTO users (username, email, password, role, status) VALUES 
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active');

-- Default SMTP configuration (needs to be updated)
INSERT INTO smtp_config (name, host, port, username, password, encryption, from_email, from_name, is_active) VALUES 
('default', 'smtp.gmail.com', 587, 'your-email@gmail.com', 'your-app-password', 'tls', 'your-email@gmail.com', 'Email Marketing Tool', TRUE);

-- Sample email templates
INSERT INTO email_templates (name, subject, body, user_id, is_public, category) VALUES 
('Welcome Email', 'Welcome to Our Service!', 
'<h1>Welcome {name}!</h1>
<p>Thank you for joining us. We are excited to have you on board.</p>
<p>Best regards,<br>The Team</p>', 
1, TRUE, 'welcome'),

('Newsletter Template', 'Monthly Newsletter - {month}', 
'<h1>Monthly Newsletter</h1>
<p>Hello {name},</p>
<p>Here are the latest updates from {company}:</p>
<ul>
<li>Update 1</li>
<li>Update 2</li>
<li>Update 3</li>
</ul>
<p>Best regards,<br>The Team</p>', 
1, TRUE, 'newsletter'),

('Promotional Email', 'Special Offer Just for You!', 
'<h1>Exclusive Offer</h1>
<p>Hi {name},</p>
<p>We have a special offer just for you! Get 50% off your next purchase.</p>
<p><a href="#" style="background-color: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Claim Offer</a></p>
<p>Best regards,<br>The Sales Team</p>', 
1, TRUE, 'promotional');

-- Sample email list
INSERT INTO email_lists (name, description, user_id, total_contacts, active_contacts) VALUES 
('Main List', 'Primary email list for marketing campaigns', 1, 0, 0);

-- Mark initial migration as executed
INSERT INTO migrations (migration) VALUES ('001_initial_schema.sql');