#!/usr/bin/env bash

# Secure Online Voting System - Development Setup Script

echo "🗳️  Secure Online Voting System - Setup"
echo "========================================"
echo ""

# Check PHP version
echo "Checking PHP version..."
php_version=$(php -r 'echo PHP_VERSION;')
echo "PHP version: $php_version"

# Install Composer dependencies
echo ""
echo "Installing Composer dependencies..."
composer install --no-interaction

# Copy environment file if it doesn't exist
if [ ! -f .env ]; then
    echo ""
    echo "Creating .env file..."
    cp .env.example .env
    echo "✅ .env file created"
fi

# Generate application key
echo ""
echo "Generating application key..."
php artisan key:generate

# Generate vote encryption key (32 characters)
echo ""
echo "Generating vote encryption key..."
vote_key=$(openssl rand -base64 32 | cut -c1-32)
sed -i.bak "s/VOTE_ENCRYPTION_KEY=.*/VOTE_ENCRYPTION_KEY=$vote_key/" .env
echo "✅ Vote encryption key generated"

# Create database
echo ""
echo "Setting up database..."
php artisan migrate --force

# Seed database
echo ""
read -p "Would you like to seed the database with sample data? (y/n): " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan db:seed
    echo "✅ Database seeded"
fi

# Create storage links
echo ""
echo "Creating storage symlinks..."
php artisan storage:link

# Set permissions
echo ""
echo "Setting directory permissions..."
chmod -R 775 storage bootstrap/cache

# Install NPM dependencies
if command -v npm &> /dev/null; then
    echo ""
    echo "Installing NPM dependencies..."
    npm install
    
    echo ""
    echo "Building assets..."
    npm run build
else
    echo ""
    echo "⚠️  NPM not found. Skipping frontend build."
fi

# Clear caches
echo ""
echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo ""
echo "✅ Setup complete!"
echo ""
echo "Next steps:"
echo "1. Update your .env file with database credentials"
echo "2. Configure reCAPTCHA keys (RECAPTCHA_SITE_KEY and RECAPTCHA_SECRET_KEY)"
echo "3. Run 'php artisan serve' to start the development server"
echo ""
echo "Default admin credentials (if seeded):"
echo "Email: admin@securevoting.com"
echo "Password: admin123"
echo ""
