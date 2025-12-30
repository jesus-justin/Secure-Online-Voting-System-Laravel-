# Secure Online Voting System (Laravel)

A comprehensive, secure voting platform built with Laravel that ensures one-vote-per-user enforcement, anonymous voting, real-time vote tallying, and advanced security features including vote encryption, tampering detection, and device fingerprinting.

## � Quick Start

**New users?** Start with the documentation:
- **[📘 START_HERE.md](docs/START_HERE.md)** - Visual project overview
- **[⚙️ COMPLETE_SETUP_GUIDE.md](docs/COMPLETE_SETUP_GUIDE.md)** - Detailed setup instructions  
- **[🗂️ PROJECT_STRUCTURE.md](docs/PROJECT_STRUCTURE.md)** - File organization

## �🔐 Core Features

- **Secure Voter Registration** - Email-based registration with admin verification
- **One-Vote-Per-User Enforcement** - Strict validation to prevent duplicate voting
- **Real-time Vote Tally** - Live vote counting and result visualization
- **Admin Dashboard** - Complete election management interface
- **Voting Schedule** - Configurable election start/end times
- **Anonymous Voting** - Optional anonymous voting mode
- **Election Results** - Visual representation of voting outcomes

## 🛡️ Advanced Security Features

- **Token-based Voting Links** - Unique, time-limited tokens for each voter
- **SHA-256 Vote Hashing** - Cryptographic hashing for vote integrity
- **Database-level Encryption** - AES-256 encryption for sensitive vote data
- **IP & Device Fingerprint Validation** - Prevent multiple votes from same device/IP
- **Vote Tampering Detection** - Automated integrity verification system
- **Google reCAPTCHA v3** - Protection against bots and automated attacks
- **Rate Limiting** - Configurable request throttling
- **Comprehensive Audit Logs** - Track all voting activities

## 📋 Tech Stack

- **Framework:** Laravel 10
- **Database:** MySQL
- **Queue System:** Laravel Queue (for asynchronous vote processing)
- **Security:** Google reCAPTCHA v3
- **Frontend:** Bootstrap 5 + Blade Templates
- **Encryption:** SHA-256, AES-256-CBC

## 📦 Installation

### Prerequisites

- PHP >= 8.0.2
- Composer
- MySQL
- Node.js & NPM
- XAMPP (for Windows) or similar local server

### Step 1: Clone the Repository

```bash
git clone https://github.com/jesus-justin/Secure-Online-Voting-System-Laravel-.git
cd Secure-Online-Voting-System-Laravel-
```

### Step 2: Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install NPM dependencies
npm install
```

### Step 3: Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Generate vote encryption key (must be exactly 32 characters)
# Edit .env and set:
# VOTE_ENCRYPTION_KEY=your_32_character_encryption_key
```

### Step 4: Configure Database

Edit your `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=secure_voting
DB_USERNAME=root
DB_PASSWORD=
```

Create the database:

```bash
# Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE secure_voting;
exit;
```

### Step 5: Configure Google reCAPTCHA

1. Go to [Google reCAPTCHA Admin](https://www.google.com/recaptcha/admin)
2. Register your site (use reCAPTCHA v3)
3. Add keys to `.env`:

```env
RECAPTCHA_SITE_KEY=your_site_key_here
RECAPTCHA_SECRET_KEY=your_secret_key_here
```

### Step 6: Run Migrations and Seeders

```bash
# Run database migrations
php artisan migrate

# Seed the database with sample data
php artisan db:seed

# Or run both together
php artisan migrate:fresh --seed
```

### Step 7: Create Storage Link

```bash
php artisan storage:link
```

### Step 8: Start the Development Server

```bash
# Start Laravel development server
php artisan serve

# In a new terminal, start the queue worker
php artisan queue:work

# In another terminal, compile assets (optional)
npm run dev
```

Visit: `http://localhost:8000`

## 👤 Default Credentials

### Admin Account
- **Email:** admin@securevoting.com
- **Password:** admin123

### Voter Account
- **Email:** john@example.com
- **Password:** password123

## 🚀 Usage Guide

### For Voters

1. **Register:** Create an account with valid email
2. **Wait for Verification:** Admin must verify your account
3. **Login:** Use your credentials to access the system
4. **View Elections:** Browse active elections
5. **Cast Vote:** Select a candidate and submit your vote
6. **Verify:** Receive confirmation of successful vote
7. **View Results:** Check results after election ends

### For Administrators

1. **Login:** Use admin credentials
2. **Dashboard:** View statistics and recent activity
3. **Create Election:** Set up new election with candidates
4. **Manage Users:** Verify new voter registrations
5. **Monitor Votes:** Track voting activity and logs
6. **Detect Tampering:** Run integrity checks on votes
7. **View Results:** Access real-time election results

## 📂 Project Structure

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdminController.php
│   │   │   ├── AuthController.php
│   │   │   └── VotingController.php
│   │   └── Middleware/
│   │       ├── AdminMiddleware.php
│   │       ├── CheckElectionActive.php
│   │       └── CheckIfAlreadyVoted.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Election.php
│   │   ├── Candidate.php
│   │   ├── Vote.php
│   │   ├── VotingToken.php
│   │   └── VoteLog.php
│   ├── Services/
│   │   ├── VotingService.php
│   │   ├── RecaptchaService.php
│   │   └── DeviceFingerprintService.php
│   └── Jobs/
│       └── ProcessVote.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
│       ├── layouts/
│       ├── auth/
│       ├── voting/
│       └── admin/
├── routes/
│   ├── web.php
│   └── api.php
└── config/
    ├── voting.php
    └── recaptcha.php
```

## 🔧 Configuration

### Security Settings (`.env`)

```env
# Enable/disable security features
ENABLE_IP_VALIDATION=true
ENABLE_DEVICE_FINGERPRINT=true
MAX_VOTE_ATTEMPTS=3
RATE_LIMIT_PER_MINUTE=10

# Vote encryption
VOTE_ENCRYPTION_KEY=your_32_character_encryption_key

# Google reCAPTCHA
RECAPTCHA_SITE_KEY=your_site_key
RECAPTCHA_SECRET_KEY=your_secret_key
```

### Queue Configuration

For production, use a robust queue driver:

```env
QUEUE_CONNECTION=database  # or redis, sqs, etc.
```

Run queue worker:

```bash
php artisan queue:work --tries=3
```

## 🔒 Security Features Explained

### 1. Vote Hashing (SHA-256)
Each vote is hashed using SHA-256 with a combination of election ID, candidate ID, user ID, and timestamp to create a unique, tamper-proof identifier.

### 2. Vote Encryption (AES-256-CBC)
Vote data is encrypted before storage using AES-256-CBC encryption with a unique initialization vector.

### 3. Device Fingerprinting
Generates a unique fingerprint based on user agent, browser headers, and request characteristics to prevent multiple votes from the same device.

### 4. IP Validation
Tracks and validates IP addresses to prevent voting from the same network multiple times.

### 5. Tampering Detection
Automated system to verify vote integrity by comparing stored hashes with recalculated values.

### 6. Rate Limiting
Prevents brute force attacks by limiting the number of requests per minute.

### 7. Google reCAPTCHA v3
Protects forms from bots with invisible CAPTCHA verification.

## 📊 Database Schema

### Users Table
- id, name, email, password
- voter_id (unique identifier)
- is_admin, is_verified
- timestamps

### Elections Table
- id, title, description
- start_time, end_time
- is_active, allow_anonymous
- max_votes_per_user
- timestamps

### Candidates Table
- id, election_id
- name, description, photo
- position
- timestamps

### Votes Table
- id, election_id, candidate_id, user_id
- vote_hash (SHA-256)
- encrypted_vote (AES-256)
- ip_address, device_fingerprint
- is_verified, is_tampered
- timestamps

### Voting Tokens Table
- id, election_id, user_id
- token, is_used
- expires_at, used_at
- timestamps

### Vote Logs Table
- id, election_id, user_id
- action, ip_address
- device_fingerprint, details
- timestamps

## 🧪 Testing

```bash
# Run tests
php artisan test

# Run specific test
php artisan test --filter VotingTest
```

## 🚢 Deployment

### Production Checklist

1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false`
3. Configure proper database credentials
4. Set up queue worker as a service
5. Configure cron for scheduled tasks
6. Set up SSL certificate
7. Configure proper file permissions
8. Enable Laravel's maintenance mode during updates

### Optimize for Production

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

## 📝 API Documentation

The system includes RESTful API endpoints (under development) for:
- User authentication
- Election management
- Vote submission
- Result retrieval

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License.

## 👨‍💻 Developer

**Jesus Justin**
- GitHub: [@jesus-justin](https://github.com/jesus-justin)

## 🐛 Bug Reports

Found a bug? Please open an issue on GitHub with:
- Description of the bug
- Steps to reproduce
- Expected behavior
- Screenshots (if applicable)

## 🆘 Support

For support and questions:
- Open an issue on GitHub
- Check existing documentation

## 🔄 Changelog

### Version 1.0.0 (2025-01-01)
- Initial release
- Core voting functionality
- Advanced security features
- Admin dashboard
- Real-time results

## 🎯 Future Enhancements

- [ ] Email notifications
- [ ] SMS verification
- [ ] Blockchain integration
- [ ] Mobile app (React Native)
- [ ] Multi-language support
- [ ] Advanced analytics dashboard
- [ ] PDF result export
- [ ] Live streaming of results
- [ ] Voter authentication via biometrics

## ⚠️ Important Notes

1. **Encryption Key:** Never commit your `VOTE_ENCRYPTION_KEY` to version control
2. **Admin Password:** Change default admin password immediately after installation
3. **reCAPTCHA Keys:** Use domain-specific keys in production
4. **Database Backups:** Implement regular automated backups
5. **Queue Workers:** Ensure queue workers are running for async vote processing

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Google reCAPTCHA Documentation](https://developers.google.com/recaptcha)
- [Bootstrap Documentation](https://getbootstrap.com/docs)

---

**⭐ Star this repository if you find it useful!**
