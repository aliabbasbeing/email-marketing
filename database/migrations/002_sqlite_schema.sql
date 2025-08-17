CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role TEXT DEFAULT 'user' CHECK(role IN ('admin', 'manager', 'user')),
    status TEXT DEFAULT 'active' CHECK(status IN ('active', 'inactive', 'suspended')),
    last_login DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_users_username ON users(username);
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_users_status ON users(status);

CREATE TABLE IF NOT EXISTS smtp_config (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL DEFAULT 'default',
    host VARCHAR(255) NOT NULL,
    port INTEGER NOT NULL DEFAULT 587,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    encryption TEXT DEFAULT 'tls' CHECK(encryption IN ('tls', 'ssl', 'none')),
    from_email VARCHAR(255) NOT NULL,
    from_name VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS campaigns (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    subject VARCHAR(500) NOT NULL,
    body TEXT NOT NULL,
    user_id INTEGER NOT NULL,
    status TEXT DEFAULT 'draft' CHECK(status IN ('draft', 'scheduled', 'running', 'paused', 'completed', 'failed')),
    total_emails INTEGER DEFAULT 0,
    sent_emails INTEGER DEFAULT 0,
    failed_emails INTEGER DEFAULT 0,
    scheduled_at DATETIME,
    started_at DATETIME,
    completed_at DATETIME,
    failed_at DATETIME,
    failure_reason TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_campaigns_user_id ON campaigns(user_id);
CREATE INDEX IF NOT EXISTS idx_campaigns_status ON campaigns(status);
CREATE INDEX IF NOT EXISTS idx_campaigns_created_at ON campaigns(created_at);

CREATE TABLE IF NOT EXISTS email_lists (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    user_id INTEGER NOT NULL,
    total_contacts INTEGER DEFAULT 0,
    active_contacts INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_email_lists_user_id ON email_lists(user_id);

CREATE TABLE IF NOT EXISTS contacts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email VARCHAR(255) NOT NULL,
    name VARCHAR(255),
    company VARCHAR(255),
    phone VARCHAR(20),
    status TEXT DEFAULT 'active' CHECK(status IN ('active', 'inactive', 'bounced', 'unsubscribed')),
    list_id INTEGER,
    user_id INTEGER NOT NULL,
    custom_fields TEXT, -- JSON as TEXT for SQLite compatibility
    subscribed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    unsubscribed_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (list_id) REFERENCES email_lists(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE UNIQUE INDEX IF NOT EXISTS idx_contacts_email_user ON contacts(email, user_id);
CREATE INDEX IF NOT EXISTS idx_contacts_email ON contacts(email);
CREATE INDEX IF NOT EXISTS idx_contacts_status ON contacts(status);
CREATE INDEX IF NOT EXISTS idx_contacts_list_id ON contacts(list_id);
CREATE INDEX IF NOT EXISTS idx_contacts_user_id ON contacts(user_id);

CREATE TABLE IF NOT EXISTS email_templates (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    subject VARCHAR(500) NOT NULL,
    body TEXT NOT NULL,
    user_id INTEGER NOT NULL,
    is_public BOOLEAN DEFAULT 0,
    category VARCHAR(100),
    tags TEXT, -- JSON as TEXT for SQLite compatibility
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_email_templates_user_id ON email_templates(user_id);
CREATE INDEX IF NOT EXISTS idx_email_templates_category ON email_templates(category);

CREATE TABLE IF NOT EXISTS email_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    campaign_id INTEGER,
    email VARCHAR(255) NOT NULL,
    name VARCHAR(255),
    subject VARCHAR(500),
    status TEXT DEFAULT 'pending' CHECK(status IN ('pending', 'sent', 'failed', 'bounced')),
    error_message TEXT,
    is_opened BOOLEAN DEFAULT 0,
    opened_at DATETIME,
    clicked_at DATETIME,
    bounced_at DATETIME,
    unsubscribed_at DATETIME,
    csv_data TEXT, -- JSON as TEXT for SQLite compatibility
    tracking_id VARCHAR(100) UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_email_logs_campaign_id ON email_logs(campaign_id);
CREATE INDEX IF NOT EXISTS idx_email_logs_email ON email_logs(email);
CREATE INDEX IF NOT EXISTS idx_email_logs_status ON email_logs(status);
CREATE INDEX IF NOT EXISTS idx_email_logs_tracking_id ON email_logs(tracking_id);
CREATE INDEX IF NOT EXISTS idx_email_logs_created_at ON email_logs(created_at);

CREATE TABLE IF NOT EXISTS progress_tracker (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    campaign_id INTEGER,
    email VARCHAR(255) NOT NULL,
    status TEXT DEFAULT 'pending' CHECK(status IN ('pending', 'processing', 'sent', 'failed')),
    error_message TEXT,
    csv_data TEXT, -- JSON as TEXT for SQLite compatibility
    processed_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_progress_tracker_campaign_id ON progress_tracker(campaign_id);
CREATE INDEX IF NOT EXISTS idx_progress_tracker_status ON progress_tracker(status);
CREATE INDEX IF NOT EXISTS idx_progress_tracker_email ON progress_tracker(email);

CREATE TABLE IF NOT EXISTS email_opens (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email_log_id INTEGER NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    opened_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (email_log_id) REFERENCES email_logs(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_email_opens_email_log_id ON email_opens(email_log_id);
CREATE INDEX IF NOT EXISTS idx_email_opens_opened_at ON email_opens(opened_at);

CREATE TABLE IF NOT EXISTS email_clicks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email_log_id INTEGER NOT NULL,
    url TEXT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    clicked_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (email_log_id) REFERENCES email_logs(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_email_clicks_email_log_id ON email_clicks(email_log_id);
CREATE INDEX IF NOT EXISTS idx_email_clicks_clicked_at ON email_clicks(clicked_at);

CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INTEGER,
    ip_address VARCHAR(45),
    user_agent TEXT,
    payload TEXT,
    last_activity DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_sessions_user_id ON sessions(user_id);
CREATE INDEX IF NOT EXISTS idx_sessions_last_activity ON sessions(last_activity);

CREATE TABLE IF NOT EXISTS migrations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    migration VARCHAR(255) NOT NULL,
    executed_at DATETIME DEFAULT CURRENT_TIMESTAMP
);