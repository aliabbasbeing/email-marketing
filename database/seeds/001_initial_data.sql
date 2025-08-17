-- Default admin user (password: admin123)
INSERT INTO users (username, email, password, role, status) VALUES 
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active');

-- Default SMTP configuration (needs to be updated)
INSERT INTO smtp_config (name, host, port, username, password, encryption, from_email, from_name, is_active) VALUES 
('default', 'smtp.gmail.com', 587, 'your-email@gmail.com', 'your-app-password', 'tls', 'your-email@gmail.com', 'Email Marketing Tool', 1);

-- Sample email templates with simple content
INSERT INTO email_templates (name, subject, body, user_id, is_public, category) VALUES 
('Welcome Email', 'Welcome to Our Service!', 'Welcome {name}! Thank you for joining us.', 1, 1, 'welcome');

INSERT INTO email_templates (name, subject, body, user_id, is_public, category) VALUES 
('Newsletter', 'Monthly Newsletter', 'Hello {name}, here are our latest updates!', 1, 1, 'newsletter');

-- Sample email list
INSERT INTO email_lists (name, description, user_id, total_contacts, active_contacts) VALUES 
('Main List', 'Primary email list for marketing campaigns', 1, 0, 0);