# Secure Online Voting System - Quick Start
# This script helps you set up the project quickly on Windows with XAMPP

Write-Host "================================================" -ForegroundColor Cyan
Write-Host "   Secure Online Voting System - Quick Setup   " -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""

# Check if Composer is installed
Write-Host "Checking for Composer..." -ForegroundColor Yellow
try {
    $composerVersion = composer --version 2>$null
    Write-Host "✓ Composer found: $composerVersion" -ForegroundColor Green
} catch {
    Write-Host "✗ Composer not found!" -ForegroundColor Red
    Write-Host "Please install Composer from https://getcomposer.org/download/" -ForegroundColor Yellow
    Write-Host "After installation, restart PowerShell and run this script again." -ForegroundColor Yellow
    pause
    exit
}

# Check if PHP is available
Write-Host "Checking for PHP..." -ForegroundColor Yellow
try {
    $phpVersion = php -v 2>$null
    Write-Host "✓ PHP found" -ForegroundColor Green
} catch {
    Write-Host "✗ PHP not found in PATH!" -ForegroundColor Red
    Write-Host "Make sure XAMPP PHP is in your system PATH" -ForegroundColor Yellow
    pause
    exit
}

Write-Host ""
Write-Host "Starting setup process..." -ForegroundColor Cyan
Write-Host ""

# Step 1: Install Composer dependencies
Write-Host "[1/8] Installing Composer dependencies..." -ForegroundColor Yellow
composer install --no-interaction
if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ Dependencies installed" -ForegroundColor Green
} else {
    Write-Host "✗ Failed to install dependencies" -ForegroundColor Red
    pause
    exit
}

# Step 2: Copy .env file
Write-Host "[2/8] Setting up environment file..." -ForegroundColor Yellow
if (!(Test-Path .env)) {
    Copy-Item .env.example .env
    Write-Host "✓ .env file created" -ForegroundColor Green
} else {
    Write-Host "⚠ .env file already exists, skipping" -ForegroundColor Yellow
}

# Step 3: Generate application key
Write-Host "[3/8] Generating application key..." -ForegroundColor Yellow
php artisan key:generate --no-interaction
if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ Application key generated" -ForegroundColor Green
} else {
    Write-Host "✗ Failed to generate application key" -ForegroundColor Red
}

# Step 4: Generate encryption key
Write-Host "[4/8] Generating vote encryption key..." -ForegroundColor Yellow
$encryptionKey = -join ((48..57) + (97..122) | Get-Random -Count 32 | ForEach-Object {[char]$_})
Write-Host "Generated encryption key: $encryptionKey" -ForegroundColor Cyan
Write-Host "⚠ Please add this to your .env file as VOTE_ENCRYPTION_KEY" -ForegroundColor Yellow

# Step 5: Check MySQL connection
Write-Host "[5/8] Checking MySQL connection..." -ForegroundColor Yellow
Write-Host "⚠ Make sure MySQL is running in XAMPP!" -ForegroundColor Yellow
Write-Host "Press any key to continue once MySQL is running..." -ForegroundColor Yellow
pause

# Step 6: Database setup
Write-Host "[6/8] Setting up database..." -ForegroundColor Yellow
Write-Host "Have you created the 'secure_voting' database in phpMyAdmin? (Y/N)" -ForegroundColor Yellow
$dbCreated = Read-Host
if ($dbCreated -eq "Y" -or $dbCreated -eq "y") {
    Write-Host "Running migrations..." -ForegroundColor Yellow
    php artisan migrate --force
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✓ Database migrated successfully" -ForegroundColor Green
        
        Write-Host "Do you want to seed the database with sample data? (Y/N)" -ForegroundColor Yellow
        $seedDb = Read-Host
        if ($seedDb -eq "Y" -or $seedDb -eq "y") {
            php artisan db:seed --force
            if ($LASTEXITCODE -eq 0) {
                Write-Host "✓ Database seeded successfully" -ForegroundColor Green
            }
        }
    } else {
        Write-Host "✗ Database migration failed" -ForegroundColor Red
        Write-Host "Please check your database configuration in .env" -ForegroundColor Yellow
    }
} else {
    Write-Host "Please create the database first:" -ForegroundColor Yellow
    Write-Host "1. Open phpMyAdmin (http://localhost/phpmyadmin)" -ForegroundColor Cyan
    Write-Host "2. Click 'New' to create a database" -ForegroundColor Cyan
    Write-Host "3. Name it 'secure_voting'" -ForegroundColor Cyan
    Write-Host "4. Run this script again" -ForegroundColor Cyan
    pause
    exit
}

# Step 7: Create storage link
Write-Host "[7/8] Creating storage link..." -ForegroundColor Yellow
php artisan storage:link
if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ Storage link created" -ForegroundColor Green
} else {
    Write-Host "⚠ Storage link may already exist" -ForegroundColor Yellow
}

# Step 8: Final instructions
Write-Host ""
Write-Host "================================================" -ForegroundColor Green
Write-Host "          Setup Complete! 🎉                     " -ForegroundColor Green
Write-Host "================================================" -ForegroundColor Green
Write-Host ""
Write-Host "IMPORTANT: Complete these steps manually:" -ForegroundColor Yellow
Write-Host ""
Write-Host "1. Edit .env file and add:" -ForegroundColor Cyan
Write-Host "   VOTE_ENCRYPTION_KEY=$encryptionKey" -ForegroundColor White
Write-Host ""
Write-Host "2. Get Google reCAPTCHA keys:" -ForegroundColor Cyan
Write-Host "   - Visit: https://www.google.com/recaptcha/admin" -ForegroundColor White
Write-Host "   - Create a new site (reCAPTCHA v3)" -ForegroundColor White
Write-Host "   - Add keys to .env:" -ForegroundColor White
Write-Host "     RECAPTCHA_SITE_KEY=your_site_key" -ForegroundColor White
Write-Host "     RECAPTCHA_SECRET_KEY=your_secret_key" -ForegroundColor White
Write-Host ""
Write-Host "3. Start the application:" -ForegroundColor Cyan
Write-Host "   php artisan serve" -ForegroundColor White
Write-Host ""
Write-Host "4. In a new terminal, start the queue worker:" -ForegroundColor Cyan
Write-Host "   php artisan queue:work" -ForegroundColor White
Write-Host ""
Write-Host "5. Visit: http://localhost:8000" -ForegroundColor Cyan
Write-Host ""
Write-Host "Default Credentials:" -ForegroundColor Yellow
Write-Host "  Admin - Email: admin@securevoting.com | Password: admin123" -ForegroundColor White
Write-Host "  Voter - Email: john@example.com | Password: password123" -ForegroundColor White
Write-Host ""
Write-Host "For more details, check SETUP_GUIDE.md" -ForegroundColor Cyan
Write-Host ""
pause
