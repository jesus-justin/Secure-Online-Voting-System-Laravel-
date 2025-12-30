# Complete Setup & Verification Guide

## 📋 Project Structure Overview

```
Secure-Online-Voting-System-Laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/ (AuthController, VotingController, AdminController)
│   │   └── Middleware/ (AdminMiddleware, CheckIfAlreadyVoted, CheckElectionActive)
│   ├── Models/ (User, Election, Candidate, Vote, VotingToken, VoteLog)
│   └── Services/ (VotingService, RecaptchaService, DeviceFingerprintService)
├── config/ (recaptcha.php, voting.php)
├── database/
│   ├── migrations/ (7 migration files)
│   └── seeders/
├── public/
│   └── index.php
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── app.blade.php (Master layout)
│   │   ├── auth/
│   │   │   ├── login.blade.php (Modern login)
│   │   │   └── register.blade.php (Modern registration)
│   │   ├── voting/ (Election views)
│   │   ├── admin/ (Admin dashboard views)
│   │   ├── landing.blade.php (Public landing page)
│   │   └── home.blade.php (Authenticated dashboard)
│   ├── css/
│   │   └── app.css
│   └── js/
│       ├── app.js
│       └── bootstrap.js
├── routes/
│   ├── web.php (Main routes with landing & home)
│   ├── api.php
│   └── console.php
├── tests/
│   └── Feature/ (VotingTest.php)
├── .env.example
├── composer.json
├── package.json
├── phpunit.xml
├── vite.config.js
└── README.md
```

## 🚀 Quick Start Guide (XAMPP)

### Prerequisites
- XAMPP with PHP 8.0.2+ and MySQL
- Composer installed globally
- Git (optional)

### Step 1: Environment Setup

1. Copy `.env.example` to `.env`:
   ```bash
   copy .env.example .env
   ```

2. Edit `.env` with your settings:
   ```env
   APP_NAME="Secure Voting System"
   APP_URL=http://localhost/Secure-Online-Voting-System-Laravel
   
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=secure_voting
   DB_USERNAME=root
   DB_PASSWORD=
   
   RECAPTCHA_SITE_KEY=your_site_key_here
   RECAPTCHA_SECRET_KEY=your_secret_key_here
   ```

3. Generate app key:
   ```bash
   php artisan key:generate
   ```

### Step 2: Database Setup

1. Create MySQL database:
   ```sql
   CREATE DATABASE secure_voting;
   ```

2. Run migrations:
   ```bash
   php artisan migrate
   ```

3. Seed sample data (optional):
   ```bash
   php artisan db:seed
   ```

### Step 3: Install Dependencies

```bash
composer install
npm install
npm run build
```

### Step 4: Run Application

**Option A: Using Laravel Artisan**
```bash
php artisan serve
```
Access at: `http://localhost:8000`

**Option B: Using XAMPP Virtual Host**
1. Point virtual host to `public/` directory
2. Access at: `http://localhost/Secure-Online-Voting-System-Laravel`

## 📱 Page Routes & Navigation

### Public Routes (No Authentication)
- `/` → Landing Page (Hero, Features, FAQ)
- `/login` → Modern Login Form
- `/register` → Modern Registration Form

### Authenticated Routes
- `/home` → Dashboard (Stats, Elections, Sidebar)
- `/elections` → Election List
- `/elections/{id}` → Candidate Selection
- `/elections/{id}/vote` → Cast Vote
- `/elections/{id}/success` → Vote Confirmation
- `/elections/{id}/results` → Results View

### Admin Routes (Requires Admin Role)
- `/admin/dashboard` → Statistics & Overview
- `/admin/elections` → Manage Elections
- `/admin/users` → Manage Users & Verification
- `/admin/logs` → Audit Trail

## 🔐 Security Configuration

### reCAPTCHA v3 Setup
1. Go to: https://www.google.com/recaptcha/admin
2. Create new site with reCAPTCHA v3
3. Add keys to `.env`:
   ```env
   RECAPTCHA_SITE_KEY=your_key
   RECAPTCHA_SECRET_KEY=your_secret
   ```

### Email Configuration
Set in `.env`:
```env
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
```

## 🧪 Testing

Run feature tests:
```bash
php artisan test
```

## 📊 Database Schema

### Users Table
- id, name, email, password
- voter_id, is_verified, verified_at
- is_admin, last_login_at
- created_at, updated_at

### Elections Table
- id, title, description
- status (pending, active, completed)
- start_date, end_date
- created_at, updated_at

### Candidates Table
- id, election_id, name, description
- image_url, created_at, updated_at

### Votes Table
- id, user_id, election_id, candidate_id
- encrypted_vote, vote_hash
- device_fingerprint, ip_address
- created_at

### Vote Logs Table
- id, vote_id, action, old_value, new_value
- performed_at

### Voting Tokens Table
- id, user_id, election_id, token
- used_at, created_at, expires_at

### Jobs Table
- id, queue, payload, attempts
- reserved_at, available_at
- created_at

## 🎯 User Roles & Permissions

### Regular User
- ✓ Register & Login
- ✓ Wait for verification
- ✗ Vote (until verified)
- ✓ View elections & candidates
- ✓ Vote once verified
- ✓ View results

### Verified User
- ✓ All of above
- ✓ Vote in active elections
- ✓ View vote history
- ✓ See one-time token status

### Admin User
- ✓ All regular user features
- ✓ Create & manage elections
- ✓ Add candidates
- ✓ Verify new users
- ✓ View audit logs
- ✓ Detect vote tampering
- ✓ View system statistics

## 🔄 Voting Flow

```
1. User Registration
   ├─ User fills form with name, email, password
   ├─ reCAPTCHA v3 verification
   ├─ User account created (unverified)
   └─ Redirect to login

2. Login
   ├─ User enters email & password
   ├─ reCAPTCHA v3 verification
   ├─ User redirected to Home Dashboard
   └─ Status shown: Verified/Pending

3. Home Dashboard
   ├─ Display user stats
   ├─ Show active elections
   ├─ Show if user already voted
   └─ Provide voting link (if verified)

4. Voting
   ├─ Select candidate
   ├─ Confirm selection
   ├─ Vote encrypted (AES-256-CBC)
   ├─ Hash stored (SHA-256)
   ├─ Device fingerprint logged
   ├─ One-time token used
   └─ Redirect to success page

5. Results
   ├─ View real-time election results
   ├─ See vote count per candidate
   └─ Download results report (admin only)
```

## 📈 Security Checklist

- ✅ AES-256-CBC encryption for votes
- ✅ SHA-256 hashing for tampering detection
- ✅ Device fingerprinting (user agent based)
- ✅ IP address logging
- ✅ One-time voting tokens
- ✅ reCAPTCHA v3 integration
- ✅ Admin verification requirement
- ✅ Complete audit logging
- ✅ CSRF protection (Laravel default)
- ✅ Password hashing (Laravel default)
- ✅ Session management (Laravel default)
- ✅ SQL injection protection (Eloquent ORM)

## ⚙️ Configuration Files

### config/voting.php
```php
return [
    'rate_limit' => 5, // per minute
    'max_candidates' => 20,
    'encryption_algorithm' => 'AES-256-CBC',
];
```

### config/recaptcha.php
```php
return [
    'site_key' => env('RECAPTCHA_SITE_KEY'),
    'secret_key' => env('RECAPTCHA_SECRET_KEY'),
    'threshold' => 0.5,
];
```

## 🐛 Troubleshooting

### Database Connection Error
```
Error: SQLSTATE[HY000] [1045] Access denied
Solution: Check DB_HOST, DB_USERNAME, DB_PASSWORD in .env
```

### reCAPTCHA Verification Failed
```
Solution: Verify RECAPTCHA_SITE_KEY and RECAPTCHA_SECRET_KEY are correct
```

### Sessions Not Working
```
Solution: Clear cache: php artisan cache:clear
Restart browser or clear cookies
```

### 404 on /home route
```
Solution: Ensure routes are cached properly
Run: php artisan route:cache
```

## 📞 Support & Documentation

- **Laravel Documentation**: https://laravel.com/docs
- **Bootstrap Documentation**: https://getbootstrap.com/docs
- **Bootstrap Icons**: https://icons.getbootstrap.com

## 🎉 You're All Set!

Your Secure Online Voting System is now ready to use. Start by:
1. Running migrations
2. Creating test admin account
3. Creating test elections
4. Testing the complete voting flow

---

**Last Updated**: 2024  
**Version**: 1.0.0  
**License**: MIT
