# Quick Start Guide

## 30-Second Setup

### 1. Database
```bash
mysql -u root heartifact-mid < database.sql
```

### 2. Gmail Configuration
Open `config/email.php` and update:
```php
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
define('FROM_EMAIL', 'your-email@gmail.com');
```

### 3. Google OAuth Configuration (optional)
To allow users to sign in with Google:

1. Create OAuth credentials in Google Cloud Console.
2. Set the redirect URI to `http://localhost/heartifact-mid/oauth2callback.php`.
3. Edit `config/google.php` and supply the `GOOGLE_CLIENT_ID` and `GOOGLE_CLIENT_SECRET`.

### 3. Install Dependencies
Run in project root:
```bash
composer install
composer require phpmailer/phpmailer google/apiclient
```

This installs PHPMailer for email and the Google API client for Google sign-in.

### 4. Test
- Registration: http://localhost/heartifact-mid/?action=register
- Login: http://localhost/heartifact-mid/?action=login
- Google: click the "Sign in with Google" button on login/register pages

Need Gmail app password? 
- Enable 2FA: https://myaccount.google.com/security
- Get app password: https://myaccount.google.com/apppasswords

### 3. Install Email Library (Optional but Recommended)
```bash
composer install
```

### 4. Test
- Registration: http://localhost/heartifact-mid/?action=register
- Login: http://localhost/heartifact-mid/?action=login

---

## User Flow

1. **Register** (or sign in with Google) → Verify Email → **Login** → Enter OTP → **Home**

*Google logins skip the OTP step and create/verify the account automatically.*

---

## What's Included

| Module | Features |
|--------|----------|
| **Registration** | Full name, email, password, validation, hashing |
| **Email Verification** | Unique token, 24-hour expiration, email link |
| **Login** | Email/password, verification check, secure password verify |
| **OTP** | 6-digit code, 10-min expiration, email delivery |
| **Home** | Welcome message, user info, logout button |

---

## Key Technologies

- **PHP 7.4+** - Backend
- **MySQL 5.7+** - Database
- **PHPMailer** - Email delivery via Gmail SMTP
- **PDO** - Database abstraction
- **bcrypt** - Password hashing

---

## File Reference

| File | Purpose |
|------|---------|
| `config/db.php` | MySQL connection |
| `config/email.php` | Gmail SMTP settings |
| `models/user.php` | Database queries |
| `controllers/AuthController.php` | Business logic |
| `views/*.php` | Form pages |
| `database.sql` | Schema |

---

## Common Issues

**⚠️ Email not sending?**
- Check Gmail 2FA enabled
- Verify app password is correct
- Check spam folder
- Ensure PHPMailer is installed

**⚠️ Database error?**
- Run `database.sql` first
- Check MySQL running
- Verify credentials in `config/db.php`

**⚠️ "Email already registered"?**
- Email exists but not verified yet
- User must verify before login


