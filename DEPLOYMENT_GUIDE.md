# 🚀 Deployment Guide - Secure Notes Vault

## 💻 cPanel Deployment (Recommended for Beginners)

### Step 1: Prepare Files

1. **Download the project from GitHub:**
   ```bash
   git clone https://github.com/alexavik/secure-notes-vault.git
   cd secure-notes-vault
   ```

2. **Create a ZIP file:**
   ```bash
   zip -r secure-notes-vault.zip .
   ```

### Step 2: Upload to cPanel

1. **Login to cPanel**
2. Navigate to **File Manager**
3. Go to `public_html` (or subdirectory like `public_html/notes`)
4. Click **Upload**
5. Select `secure-notes-vault.zip`
6. After upload, right-click and **Extract**
7. Delete the ZIP file

### Step 3: Create MySQL Database

1. In cPanel, go to **MySQL® Databases**
2. **Create New Database:**
   - Database Name: `secure_notes_vault`
   - Click **Create Database**
3. **Create Database User:**
   - Username: `snv_user` (or your choice)
   - Password: Generate a strong password (save it!)
   - Click **Create User**
4. **Add User to Database:**
   - Select user and database
   - Grant **ALL PRIVILEGES**
   - Click **Make Changes**

### Step 4: Import Database Schema

1. In cPanel, go to **phpMyAdmin**
2. Select your database (`secure_notes_vault`)
3. Click **Import** tab
4. Choose file: `database.sql`
5. Click **Go**
6. Verify tables are created: `users`, `notes`, `login_attempts`

### Step 5: Configure Application

1. In File Manager, edit `config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'snv_user');  // Your database username
   define('DB_PASS', 'your_password_here');  // Your database password
   define('DB_NAME', 'secure_notes_vault');
   define('ENCRYPTION_KEY', 'CHANGE_THIS_TO_RANDOM_32_CHARACTERS!');
   define('SITE_URL', 'https://yourdomain.com');
   ```

2. **Generate random encryption key:**
   - Visit: https://www.random.org/strings/
   - Set: 32 characters, 1 string
   - Copy and paste into `ENCRYPTION_KEY`

### Step 6: Set Permissions

1. Select all PHP files
2. Right-click → **Change Permissions**
3. Set to `644` for files
4. Set to `755` for directories

### Step 7: Test Installation

1. Visit: `https://yourdomain.com/register.php`
2. Create an account
3. Login and create a test note
4. Verify encryption/decryption works

---

## 🐳 Docker Deployment

### Coming Soon

---

## ☁️ Railway / Heroku Deployment

### Railway

1. **Fork the repository**
2. **Connect to Railway:**
   - Visit: https://railway.app
   - Click **Start a New Project**
   - Select **Deploy from GitHub repo**
3. **Add MySQL Database:**
   - Click **New** → **Database** → **MySQL**
4. **Set Environment Variables:**
   - `DB_HOST`: (from Railway MySQL)
   - `DB_USER`: (from Railway MySQL)
   - `DB_PASS`: (from Railway MySQL)
   - `DB_NAME`: (from Railway MySQL)
   - `ENCRYPTION_KEY`: (generate 32-char random)
5. **Import Schema:**
   - Connect to Railway MySQL CLI
   - Run `database.sql`
6. **Deploy!**

---

## 🔧 Local Development Setup

### Using XAMPP (Windows)

1. **Install XAMPP:**
   - Download: https://www.apachefriends.org/
   - Install to `C:\xampp`

2. **Copy project:**
   ```bash
   cd C:\xampp\htdocs
   git clone https://github.com/alexavik/secure-notes-vault.git
   cd secure-notes-vault
   ```

3. **Start services:**
   - Open XAMPP Control Panel
   - Start **Apache** and **MySQL**

4. **Create database:**
   - Visit: http://localhost/phpmyadmin
   - Create database: `secure_notes_vault`
   - Import `database.sql`

5. **Configure:**
   - Edit `config.php`:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_NAME', 'secure_notes_vault');
     define('SITE_URL', 'http://localhost/secure-notes-vault');
     ```

6. **Access:**
   - Visit: http://localhost/secure-notes-vault/register.php

### Using MAMP (Mac)

1. **Install MAMP:**
   - Download: https://www.mamp.info/

2. **Copy project to:**
   ```
   /Applications/MAMP/htdocs/secure-notes-vault/
   ```

3. **Follow steps 3-6 from XAMPP guide above**

---

## 🔒 Security Checklist Post-Deployment

- [ ] Changed `ENCRYPTION_KEY` in config.php
- [ ] Changed default database password
- [ ] Enabled HTTPS (SSL certificate)
- [ ] Updated `SITE_URL` in config.php
- [ ] Set proper file permissions (644 for files, 755 for dirs)
- [ ] Disabled `display_errors` in config.php (production)
- [ ] Tested registration, login, note creation
- [ ] Verified encryption/decryption works
- [ ] Checked session timeout
- [ ] Tested rate limiting (5 failed logins)
- [ ] Reviewed `.htaccess` security headers
- [ ] Backed up database regularly

---

## 📱 Android App Deployment

See main `README.md` for detailed Android WebView app creation guide.

---

## 🐛 Troubleshooting

### "Database connection failed"
- Check credentials in `config.php`
- Verify MySQL service is running
- Test database connection in phpMyAdmin

### "CSRF token validation failed"
- Clear browser cookies
- Check if sessions are working: `<?php session_start(); echo session_id(); ?>`

### "Encryption error"
- Ensure JavaScript is enabled
- Check browser console for errors
- Verify CryptoJS library is loaded

### "Cannot modify header information"
- Remove any whitespace before `<?php` tags
- Check for `echo` statements before redirects

---

## 📞 Support

Need help? Contact:
- 📧 Telegram: [@unknownwarrior911](https://t.me/unknownwarrior911)
- 🌐 Website: [aviksec.xo.je](https://aviksec.xo.je)

---

**Developed by Avik Maji**
© 2024 All rights reserved.