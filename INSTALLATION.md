# Email Marketing Tool - Simple Installation Guide

## 🚀 One-Click Installation

We've simplified the installation process! Choose the method that works best for your setup:

### Option 1: Universal Installer (Recommended)
Access the **one-click installer** that automatically detects your environment:
```
http://yourdomain.com/install.php
```

### Option 2: XAMPP Quick Setup
For local development with XAMPP:
```
http://localhost/email-marketing/setup-xampp.php
```

### Option 3: cPanel Hosting Setup
For shared hosting with cPanel:
```
http://yourdomain.com/setup-cpanel.php
```

### Option 4: Configuration Wizard
For guided post-installation configuration:
```
http://yourdomain.com/config-wizard.php
```

---

## 📋 Quick Setup Instructions

### For XAMPP (Local Development)
1. **Install XAMPP** from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. **Start Apache** in XAMPP Control Panel
3. **Extract files** to `xampp/htdocs/email-marketing/`
4. **Run setup**: Visit `http://localhost/email-marketing/setup-xampp.php`
5. **Done!** Access your app at `http://localhost/email-marketing/public/`

### For cPanel Hosting (Production)
1. **Upload files** to your hosting account (public_html/)
2. **Create MySQL database** in cPanel
3. **Run setup**: Visit `http://yourdomain.com/setup-cpanel.php`
4. **Configure**: Follow the guided setup
5. **Done!** Access your app at `http://yourdomain.com/public/`

### For Other Hosting
1. **Upload files** to your web server
2. **Run installer**: Visit `http://yourdomain.com/install.php`
3. **Follow steps**: Environment detection → Requirements → Database → Install
4. **Done!** Access your app

---

## 🔧 What's Included

✅ **Automatic environment detection** (XAMPP, cPanel, generic hosting)  
✅ **Smart database setup** (SQLite for dev, MySQL for production)  
✅ **One-click installation** with progress tracking  
✅ **Configuration wizard** for SMTP, admin, and security settings  
✅ **Deployment guides** for different hosting environments  
✅ **Auto-permissions setting** and security hardening  

---

## 🎯 Default Login
After installation:
- **URL**: `http://yourdomain.com/public/`
- **Username**: `admin`
- **Password**: `admin123`

⚠️ **Security**: Change the default password immediately after first login!

---

## 🆘 Troubleshooting

### Installation Issues
- **Database errors**: Check your database credentials
- **Permission errors**: Ensure web server can write to storage/ directory
- **SMTP issues**: Use the configuration wizard to test email settings

### Quick Fixes
```bash
# Fix permissions
chmod 755 public/
chmod 777 storage/

# Reset database (SQLite)
rm storage/database.sqlite
php database/migrate.php
```

### Getting Help
1. Check the [full documentation](README.md)
2. Review the [deployment guide](docs/DEPLOYMENT.md)
3. Use the configuration wizard for guided setup
4. Create an issue on GitHub

---

## 📚 Next Steps

After installation:
1. **Configure SMTP** settings for email sending
2. **Import contacts** or create contact lists
3. **Design templates** for your email campaigns
4. **Create campaigns** and start sending emails
5. **Monitor analytics** and track performance

**🎉 Your professional email marketing platform is ready to use!**