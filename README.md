# Email Marketing Tool 2.0

A comprehensive, professional-grade email marketing solution built with pure PHP and modern web technologies.

## Features

### ✨ Core Features
- **Campaign Management**: Create, schedule, and monitor email campaigns
- **Contact Management**: Import, organize, and segment email lists
- **Template System**: Pre-built and custom email templates
- **Real-time Analytics**: Track opens, clicks, and engagement metrics
- **SMTP Integration**: Custom SMTP configuration without external APIs
- **User Management**: Role-based access control (Admin, Manager, User)

### 🚀 Advanced Features
- **A/B Testing**: Test different email versions for optimization
- **Automated Sequences**: Set up drip campaigns and autoresponders
- **Geographic Tracking**: Monitor email performance by location
- **Device Analytics**: Track email opens across different devices
- **Bounce Management**: Automatic handling of bounced emails
- **Unsubscribe Management**: Built-in unsubscribe handling

### 🛡️ Security Features
- **CSRF Protection**: Secure form submissions
- **XSS Prevention**: Input sanitization and output escaping
- **SQL Injection Prevention**: Prepared statements and input validation
- **Session Security**: Secure session handling and regeneration
- **Rate Limiting**: Prevent abuse and spam
- **Password Security**: Strong password policies and hashing

## Installation

### Requirements
- PHP 7.4 or higher
- MySQL 5.7 or MariaDB 10.2+
- Web server (Apache/Nginx)
- Composer (for dependencies)

### Quick Setup

1. **Clone the repository:**
   ```bash
   git clone https://github.com/aliabbasbeing/email-marketing.git
   cd email-marketing
   ```

2. **Install dependencies:**
   ```bash
   composer install
   ```

3. **Database setup:**
   ```bash
   # Create database
   mysql -u root -p
   CREATE DATABASE bulk_mailer;
   
   # Import schema
   mysql -u root -p bulk_mailer < database/migrations/001_initial_schema.sql
   
   # Import seed data
   mysql -u root -p bulk_mailer < database/seeds/001_initial_data.sql
   ```

4. **Configure environment:**
   ```bash
   # Copy environment file
   cp .env.example .env
   
   # Edit configuration
   nano .env
   ```

5. **Web server configuration:**
   - Point document root to `/public` directory
   - Enable URL rewriting for clean URLs

### Environment Configuration

Create a `.env` file in the root directory:

```env
# Database Configuration
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=bulk_mailer
DB_USERNAME=root
DB_PASSWORD=

# SMTP Configuration
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-app-password
SMTP_ENCRYPTION=tls
SMTP_FROM_EMAIL=your-email@gmail.com
SMTP_FROM_NAME="Email Marketing Tool"

# Application Configuration
APP_DEBUG=true
APP_ENV=development
```

## Architecture

### Directory Structure
```
/
├── config/              # Configuration files
│   ├── app.php         # Application settings
│   ├── database.php    # Database configuration
│   ├── smtp.php        # SMTP settings
│   └── security.php    # Security configuration
├── src/                # Source code
│   ├── Controllers/    # Request handlers
│   ├── Models/         # Data models
│   ├── Services/       # Business logic
│   ├── Middleware/     # Request middleware
│   └── Helpers/        # Utility functions
├── public/             # Web-accessible files
│   ├── assets/         # Static assets
│   ├── css/           # Stylesheets
│   ├── js/            # JavaScript files
│   └── index.php      # Application entry point
├── templates/          # View templates
│   ├── layouts/       # Layout templates
│   ├── components/    # Reusable components
│   └── pages/         # Page templates
├── database/           # Database files
│   ├── migrations/    # Schema migrations
│   └── seeds/         # Sample data
├── storage/            # File storage
│   ├── logs/          # Application logs
│   ├── cache/         # Cache files
│   └── uploads/       # Uploaded files
├── docs/              # Documentation
└── tests/             # Test files
```

### MVC Architecture

The application follows a clean MVC (Model-View-Controller) pattern:

- **Models**: Handle data logic and database interactions
- **Views**: Present data to users (templates)
- **Controllers**: Handle user requests and coordinate between models and views

## Usage

### Default Login
- **Username**: `admin`
- **Password**: `admin123`

### Creating Campaigns

1. **Navigate to Campaigns** → Create Campaign
2. **Upload CSV file** with email contacts
3. **Configure email** subject and content
4. **Set sending options** (delay, batch size)
5. **Launch campaign** and monitor progress

### SMTP Configuration

1. **Go to Settings** → SMTP Settings
2. **Enter your SMTP details**:
   - Host (e.g., smtp.gmail.com)
   - Port (587 for TLS, 465 for SSL)
   - Username and password
   - Encryption type
3. **Test connection** before saving

### Email Templates

1. **Access Templates** section
2. **Create new template** or edit existing
3. **Use placeholders** like `{name}`, `{company}`, `{email}`
4. **Save and reuse** across campaigns

## API Documentation

### Authentication Endpoints
- `POST /login` - User login
- `POST /logout` - User logout
- `POST /register` - User registration

### Campaign Endpoints
- `GET /campaigns` - List campaigns
- `POST /campaigns` - Create campaign
- `GET /campaigns/{id}` - Get campaign details
- `POST /campaigns/{id}/start` - Start campaign
- `POST /campaigns/{id}/stop` - Stop campaign

### Email Management
- `GET /emails` - List email contacts
- `POST /emails/import` - Import contacts from CSV
- `DELETE /emails/{id}` - Delete contact

## Performance Optimization

### Caching
- File-based caching for configuration
- Query result caching for analytics
- Template caching for faster rendering

### Database Optimization
- Indexed columns for fast queries
- Connection pooling
- Query optimization

### Email Sending
- Queue-based processing
- Batch sending to prevent timeouts
- Retry mechanism for failed sends

## Security Best Practices

### Implemented Security Measures
- CSRF token validation
- SQL injection prevention
- XSS protection
- Secure session handling
- Password hashing (bcrypt)
- Rate limiting
- Input validation and sanitization

### Recommended Production Settings
- Enable HTTPS
- Set secure cookie flags
- Implement CSP headers
- Regular security updates
- Monitor access logs

## Troubleshooting

### Common Issues

**Database Connection Error**
- Check database credentials in `.env`
- Ensure MySQL service is running
- Verify database exists

**SMTP Authentication Failed**
- Use app-specific passwords for Gmail
- Check firewall settings
- Verify SMTP credentials

**Permission Denied**
- Set proper file permissions: `chmod 755 public/`
- Ensure web server can write to storage directories

**Memory Limit Errors**
- Increase PHP memory limit
- Process large campaigns in batches
- Enable chunked processing

## Contributing

1. Fork the repository
2. Create feature branch: `git checkout -b feature-name`
3. Commit changes: `git commit -am 'Add feature'`
4. Push to branch: `git push origin feature-name`
5. Submit pull request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

For support and questions:
- Create an issue on GitHub
- Email: support@example.com
- Documentation: [docs/](docs/)

## Changelog

### Version 2.0.0
- Complete architecture redesign
- MVC pattern implementation
- Enhanced security features
- Improved user interface
- Advanced analytics
- Role-based access control
- API endpoints
- Comprehensive documentation