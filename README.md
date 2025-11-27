# 🔐 Secure Notes Vault (SNV)

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1)](https://www.mysql.com/)

A lightweight, secure web application for storing encrypted notes with AES client-side encryption. Features a modern UI with light/dark theme support, perfect for personal use or deployment as a WebView Android app.

**Developed by [Avik](https://aviksec.xo.je)**

---

## ✨ Features

- 🔒 **Client-Side AES Encryption** - Notes encrypted in browser before transmission
- 🔐 **Secure Authentication** - BCrypt password hashing, CSRF protection
- 🌓 **Light & Dark Mode** - Auto-switching theme with persistent preference
- 📱 **Fully Responsive** - Works seamlessly on mobile and desktop
- 🔍 **Search Functionality** - Quickly find your notes
- ⚡ **Fast & Lightweight** - Minimal dependencies, optimized performance
- 🛡️ **Rate Limiting** - Protection against brute-force attacks
- 📱 **WebView Compatible** - Perfect for Android app wrapper
- ⏱️ **Auto Logout** - Session timeout for enhanced security
- 🎨 **Modern UI** - Clean, intuitive interface

---

## 🚀 Quick Start

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Mod_rewrite enabled (for clean URLs)

### Installation

#### Option 1: cPanel Deployment

1. **Download the project:**
   ```bash
   git clone https://github.com/alexavik/secure-notes-vault.git
   ```

2. **Upload files to cPanel:**
   - Compress the project folder to `.zip`
   - Upload via cPanel File Manager to `public_html` or subdirectory
   - Extract the files

3. **Create MySQL Database:**
   - Go to cPanel → MySQL® Databases
   - Create new database: `secure_notes_vault`
   - Create new user with strong password
   - Add user to database with ALL PRIVILEGES

4. **Import Database Schema:**
   - Go to cPanel → phpMyAdmin
   - Select your database
   - Import `database.sql` file

5. **Configure Application:**
   - Edit `config.php`:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'your_db_username');
     define('DB_PASS', 'your_db_password');
     define('DB_NAME', 'secure_notes_vault');
     define('ENCRYPTION_KEY', 'CHANGE_THIS_TO_RANDOM_32_CHARS!');
     define('SITE_URL', 'https://yourdomain.com');
     ```

6. **Set Permissions:**
   - Ensure files are readable: `644`
   - Ensure directories are accessible: `755`

7. **Visit your site:**
   ```
   https://yourdomain.com/register.php
   ```

#### Option 2: Local Development (XAMPP/WAMP)

1. **Clone repository:**
   ```bash
   git clone https://github.com/alexavik/secure-notes-vault.git
   cd secure-notes-vault
   ```

2. **Move to web directory:**
   - XAMPP: `C:\xampp\htdocs\snv\`
   - WAMP: `C:\wamp64\www\snv\`

3. **Create database:**
   - Open phpMyAdmin
   - Create database `secure_notes_vault`
   - Import `database.sql`

4. **Configure:**
   - Edit `config.php` with your database credentials

5. **Access:**
   ```
   http://localhost/snv/register.php
   ```

---

## 📱 Creating Android WebView App

Turn SNV into a native Android app using Android Studio:

### Step 1: Create New Project

1. Open Android Studio
2. **New Project** → **Empty Activity**
3. Name: `Secure Notes Vault`
4. Package name: `com.aviksec.notevault`
5. Language: **Java** or **Kotlin**

### Step 2: Add Internet Permission

Edit `AndroidManifest.xml`:
```xml
<manifest ...>
    <uses-permission android:name="android.permission.INTERNET" />
    <uses-permission android:name="android.permission.ACCESS_NETWORK_STATE" />
    
    <application
        android:usesCleartextTraffic="true"
        ...>
        <activity android:name=".MainActivity"
            android:configChanges="orientation|screenSize">
            ...
        </activity>
    </application>
</manifest>
```

### Step 3: MainActivity Code

**Java:**
```java
package com.aviksec.notevault;

import android.os.Bundle;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import androidx.appcompat.app.AppCompatActivity;

public class MainActivity extends AppCompatActivity {
    private WebView webView;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        webView = findViewById(R.id.webview);
        webView.setWebViewClient(new WebViewClient());
        webView.getSettings().setJavaScriptEnabled(true);
        webView.getSettings().setDomStorageEnabled(true);
        
        // Load your deployed site
        webView.loadUrl("https://yourdomain.com/secure-notes-vault");
    }

    @Override
    public void onBackPressed() {
        if (webView.canGoBack()) {
            webView.goBack();
        } else {
            super.onBackPressed();
        }
    }
}
```

### Step 4: Layout XML

Edit `res/layout/activity_main.xml`:
```xml
<?xml version="1.0" encoding="utf-8"?>
<RelativeLayout xmlns:android="http://schemas.android.com/apk/res/android"
    android:layout_width="match_parent"
    android:layout_height="match_parent">

    <WebView
        android:id="@+id/webview"
        android:layout_width="match_parent"
        android:layout_height="match_parent" />
        
</RelativeLayout>
```

### Step 5: Build APK

1. **Build** → **Build Bundle(s) / APK(s)** → **Build APK(s)**
2. Find APK in `app/build/outputs/apk/debug/`
3. Install on Android device

---

## 🗂️ Project Structure

```
secure-notes-vault/
├── assets/
│   ├── css/
│   │   └── style.css          # Main stylesheet with themes
│   └── js/
│       ├── aes.js             # CryptoJS AES encryption
│       └── app.js             # Application logic
├── config.php                 # Configuration file
├── db.php                     # Database connection
├── functions.php              # Helper functions
├── database.sql               # MySQL schema
├── login.php                  # Login page
├── register.php               # Registration page
├── dashboard.php              # Main dashboard
├── add_note.php               # Create note
├── edit_note.php              # Edit note
├── delete_note.php            # Delete note API
├── logout.php                 # Logout handler
└── README.md                  # This file
```

---

## 🔐 Security Features

### Encryption
- **AES-256 encryption** on client-side before data transmission
- Encrypted notes stored in database
- Decryption only happens in user's browser

### Authentication & Authorization
- BCrypt password hashing (cost factor: 12)
- Secure session management with HttpOnly cookies
- CSRF token protection on all forms
- Rate limiting on login attempts (5 attempts, 5-minute lockout)

### Input Validation & Sanitization
- Prepared statements for SQL injection prevention
- HTML entity encoding for XSS protection
- Input validation on both client and server side

### Session Security
- Automatic session timeout (30 minutes)
- Session regeneration after login
- Secure session cookies

---

## 🎨 Customization

### Change Theme Colors

Edit `assets/css/style.css`:
```css
:root {
    --primary: #007bff;  /* Your primary color */
    --secondary: #6c757d; /* Your secondary color */
}
```

### Change Encryption Key

⚠️ **Important:** Change before first deployment!

Edit `assets/js/aes.js`:
```javascript
const ENCRYPTION_KEY = 'YOUR_SECURE_32_CHARACTER_KEY_HERE';
```

### Adjust Session Timeout

Edit `config.php`:
```php
define('SESSION_TIMEOUT', 1800); // 30 minutes (in seconds)
```

---

## 📊 Database Schema

### Users Table
```sql
id, username, email, password_hash, theme, created_at, last_login
```

### Notes Table
```sql
id, user_id, title, encrypted_content, created_at, updated_at
```

### Login Attempts Table
```sql
id, email, attempt_count, last_attempt
```

---

## 🛠️ Troubleshooting

### Database Connection Error
- Verify credentials in `config.php`
- Check if MySQL service is running
- Ensure database user has proper permissions

### Encryption/Decryption Errors
- Clear browser cache
- Ensure JavaScript is enabled
- Check browser console for errors

### Session Timeout Issues
- Check `php.ini` session settings
- Verify `session.gc_maxlifetime` value
- Ensure cookies are enabled in browser

### Theme Not Persisting
- Check browser localStorage permissions
- Verify JavaScript execution
- Clear browser cache

---

## 📝 License

MIT License - feel free to use, modify, and distribute.

---

## 👨‍💻 Developer

**Avik Maji**
- 🌐 Website: [aviksec.xo.je](https://aviksec.xo.je)
- 📧 Telegram: [@unknownwarrior911](https://t.me/unknownwarrior911)
- 💻 GitHub: [@alexavik](https://github.com/alexavik)

---

## 🤝 Contributing

Contributions welcome! Please feel free to submit a Pull Request.

---

## ⭐ Support

If you find this project helpful, please give it a star on GitHub!

---

**© 2024 Avik Maji. All rights reserved.**