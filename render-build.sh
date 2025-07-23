#!/usr/bin/env bash
# Render build script for PHP project

set -o errexit  # exit on error

echo "🔄 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

echo "✅ Build completed successfully!"
echo "ℹ️  Don't forget to run migrations manually: php migrations/migration.php --reset"
