# Email Marketing Tool 2.0 - Quick Start Guide

## 🚀 What's New

This is a **complete redesign** of the email marketing tool with modern architecture and professional features:

### ✨ Key Improvements
- **MVC Architecture**: Clean separation of concerns
- **Modern UI**: Beautiful Tailwind CSS interface with responsive design
- **Security**: CSRF protection, input validation, secure sessions
- **Database**: Proper schema with migrations and relationships
- **Documentation**: Comprehensive guides and API documentation

## 📋 Quick Setup (Development)

### 1. Clone and Install
```bash
git clone https://github.com/aliabbasbeing/email-marketing.git
cd email-marketing
```

### 2. Database Setup
```bash
# Create SQLite database (for development)
php database/migrate.php

# Or for MySQL (production):
# Create database: CREATE DATABASE bulk_mailer;
# Update .env with MySQL credentials
# Run: php database/migrate.php
```

### 3. Start Development Server
```bash
cd public
php -S localhost:8000
```

### 4. Login
- **URL**: http://localhost:8000/login
- **Username**: `admin`
- **Password**: `admin123`

## 🎯 What's Working Now

### ✅ Authentication System
- Secure login/logout
- Session management
- Password hashing
- Role-based access control

### ✅ Dashboard
- User statistics overview
- Quick action buttons
- Navigation menu
- Responsive design

### ✅ Database Layer
- SQLite support (development)
- MySQL support (production)
- Proper migrations system
- Model relationships

### ✅ Security Features
- CSRF protection
- Input sanitization
- XSS prevention
- Secure sessions

## 🛠️ Architecture Overview

```
📁 New Structure:
├── config/          # Configuration files
├── src/
│   ├── Controllers/ # Request handlers
│   ├── Models/      # Data models
│   └── Services/    # Business logic
├── public/          # Web entry point
├── templates/       # View templates
├── database/        # Migrations & seeds
└── storage/         # Logs, cache, uploads
```

## 🔄 Migration from Old Version

The old files are still present but the new system runs on:
- **Entry Point**: `public/index.php` (new MVC system)
- **Old Entry**: `index.php` (legacy system)

You can run both systems side by side during transition.

## 📚 Next Steps

1. **Campaign Management**: Create and manage email campaigns
2. **Contact Lists**: Import and organize email contacts
3. **Templates**: Build and customize email templates
4. **Analytics**: Track opens, clicks, and engagement
5. **SMTP Integration**: Configure email sending

## 🆘 Troubleshooting

### Database Issues
```bash
# Reset database
rm storage/database.sqlite
php database/migrate.php
```

### Permission Issues
```bash
chmod 755 public/
chmod 777 storage/
```

### Login Issues
- Default credentials: admin / admin123
- Check database was created successfully
- Verify file permissions

## 📞 Support

- Check the main [README.md](README.md) for detailed documentation
- Review the [architecture documentation](docs/)
- For issues, create a GitHub issue

---

**🎉 Congratulations! You now have a professional-grade email marketing platform!**