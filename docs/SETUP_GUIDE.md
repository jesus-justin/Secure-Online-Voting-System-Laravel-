# Quick Setup Guide for XAMPP Users

This guide will help you get the Secure Online Voting System running on your Windows machine with XAMPP.

## Step-by-Step Setup

### 1. Install Composer (if not already installed)

Download and install Composer from: https://getcomposer.org/download/

Or run this command in PowerShell (as Administrator):
```powershell
# Download Composer installer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

# Install Composer
php composer-setup.php --install-dir=C:\xampp\php --filename=composer

# Remove installer
php -r "unlink('composer-setup.php');"

# Add to PATH
[Environment]::SetEnvironmentVariable("Path", $env:Path + ";C:\xampp\php", "Machine")
```

### 2. Install Project Dependencies

Open a new PowerShell window in your project directory:
```powershell
cd C:\xampp\htdocs\Secure-Online-Voting-System-Laravel-

# Install PHP dependencies
composer install

# Install Node dependencies (optional, for frontend assets)
npm install
```

### 3. Set Up Environment File

```powershell
# Copy the example environment file
Copy-Item .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Edit .env File

Open `.env` in a text editor and update:

```env
APP_NAME="Secure Voting System"
APP_URL=http://localhost/Secure-Online-Voting-System-Laravel-/public

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=secure_voting
DB_USERNAME=root
DB_PASSWORD=

# Generate a 32-character encryption key
VOTE_ENCRYPTION_KEY=abcdefghijklmnopqrstuvwxyz123456

# Get these from https://www.google.com/recaptcha/admin
RECAPTCHA_SITE_KEY=your_site_key_here
RECAPTCHA_SECRET_KEY=your_secret_key_here
```

### 5. Create Database

1. Start XAMPP Control Panel
2. Start Apache and MySQL
3. Click "Admin" button next to MySQL (opens phpMyAdmin)
4. Click "New" to create a database
5. Name it `secure_voting`
6. Click "Create"

Or use command line:
```powershell
# Open MySQL command line
C:\xampp\mysql\bin\mysql.exe -u root -p

# In MySQL prompt:
CREATE DATABASE secure_voting;
exit;
```

### 6. Run Migrations and Seeders

```powershell
# Run migrations to create tables
php artisan migrate

# Seed the database with sample data
php artisan db:seed
```

### 7. Create Storage Link

```powershell
php artisan storage:link
```

### 8. Set Up Google reCAPTCHA (Important!)

1. Go to https://www.google.com/recaptcha/admin
2. Click the "+" icon to register a new site
3. Choose:
   - Label: "Secure Voting System"
   - reCAPTCHA type: **reCAPTCHA v3**
   - Domains: `localhost`
4. Accept terms and submit
5. Copy the **Site Key** and **Secret Key**
6. Update your `.env` file with these keys

### 9. Start the Application

#### Option A: Using PHP Built-in Server (Recommended for Development)

```powershell
# Start the server
php artisan serve

# In a new PowerShell window, start the queue worker
php artisan queue:work
```

Visit: http://localhost:8000

#### Option B: Using XAMPP Apache

1. Make sure Apache is running in XAMPP
2. Visit: http://localhost/Secure-Online-Voting-System-Laravel-/public

### 10. Login and Test

**Admin Login:**
- URL: http://localhost:8000/login (or your Apache URL)
- Email: `admin@securevoting.com`
- Password: `admin123`

**Voter Login:**
- Email: `john@example.com`
- Password: `password123`

## Troubleshooting

### Composer not found
```powershell
# Add Composer to PATH
$env:Path += ";C:\xampp\php"

# Or restart PowerShell after installation
```

### MySQL Connection Error
- Make sure MySQL is running in XAMPP
- Check database name, username, and password in `.env`
- Default XAMPP MySQL password is empty (no password)

### Permission Errors
```powershell
# Give write permissions to storage and cache
icacls "storage" /grant Everyone:F /T
icacls "bootstrap\cache" /grant Everyone:F /T
```

### reCAPTCHA not working
- Make sure you're using reCAPTCHA v3 (not v2)
- Check that your site key and secret key are correct
- For local development, add `localhost` to allowed domains

### Queue not processing
Make sure queue worker is running:
```powershell
php artisan queue:work
```

### Encryption key error
Generate a new 32-character key:
```powershell
# In PowerShell
-join ((48..57) + (97..122) | Get-Random -Count 32 | % {[char]$_})
```

Then add it to `.env`:
```env
VOTE_ENCRYPTION_KEY=your_generated_32_character_key
```

## Common Commands

```powershell
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Reset database (WARNING: Deletes all data)
php artisan migrate:fresh --seed

# View all routes
php artisan route:list

# Create new admin user
php artisan tinker
# Then in tinker:
\App\Models\User::create([
    'name' => 'New Admin',
    'email' => 'newadmin@example.com',
    'password' => bcrypt('password'),
    'voter_id' => 'VID-ADMIN002',
    'is_admin' => true,
    'is_verified' => true
]);
exit
```

## Next Steps

1. **Change Admin Password** - Login as admin and change the default password
2. **Create an Election** - Go to Admin Dashboard → Create New Election
3. **Add Candidates** - Edit your election and add candidates
4. **Verify Users** - Go to Manage Users to verify new registrations
5. **Test Voting** - Login as a voter and cast a vote
6. **View Results** - Check the results page after the election

## Security Reminder

Before deploying to production:
- [ ] Change all default passwords
- [ ] Get proper reCAPTCHA keys for your domain
- [ ] Generate a secure VOTE_ENCRYPTION_KEY
- [ ] Set APP_DEBUG=false in .env
- [ ] Set APP_ENV=production in .env
- [ ] Use HTTPS/SSL certificate
- [ ] Set up regular database backups

## Need Help?

- Check the main README.md for detailed documentation
- Review error logs: `storage/logs/laravel.log`
- Open an issue on GitHub

---

**Happy Voting! 🗳️**
