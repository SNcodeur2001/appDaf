#!/usr/bin/env bash
# Render build script for PHP project

set -o errexit  # exit on error

echo "🔍 Current directory: $(pwd)"
echo "📁 Listing files:"
ls -la

echo "🔄 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --verbose

echo "📁 Checking if vendor directory was created:"
ls -la vendor/ || echo "❌ vendor/ directory not found"

echo "📁 Checking autoload.php:"
ls -la vendor/autoload.php || echo "❌ vendor/autoload.php not found"

echo "✅ Build completed successfully!"
echo "ℹ️  Don't forget to run migrations manually: php migrations/migration.php --reset"
